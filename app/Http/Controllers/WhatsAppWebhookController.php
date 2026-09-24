<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use App\Models\Schadule;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        // PHP может заменять точки в именах параметров на "_".
        $query = $request->query();

        $mode = $query['hub_mode'] ?? $query['hub.mode'] ?? '';
        $token = $query['hub_verify_token']
            ?? $query['hub.verify_token']
            ?? '';
        $challenge = $query['hub_challenge']
            ?? $query['hub.challenge']
            ?? '';

        $expected = (string) config('services.whatsapp.verify_token');

        abort_unless(
            $expected !== ''
            && $mode === 'subscribe'
            && is_string($token)
            && hash_equals($expected, $token),
            403
        );

        return response($challenge, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function receive(Request $request)
    {
        $secret = (string) config('services.whatsapp.app_secret');
        $phoneId = (string) config('services.whatsapp.phone_number_id');

        abort_if($secret === '' || $phoneId === '', 503);

        $expected = 'sha256=' . hash_hmac(
                'sha256',
                $request->getContent(),
                $secret
            );

        $signature = (string) $request->header('X-Hub-Signature-256');

        if (!hash_equals($expected, $signature)) {
            Log::warning('WhatsApp signature mismatch', [
                'signature_present' => $signature !== '',
                'signature_format_valid' =>
                    preg_match('/^sha256=[a-f0-9]{64}$/D', $signature) === 1,
                'body_bytes' => strlen($request->getContent()),
                'body_sha256' => hash('sha256', $request->getContent()),
            ]);

            return response()->json([
                'error' => 'invalid_signature',
            ], 403);
        }

        foreach ($request->input('entry', []) as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];

                if (
                    (string) data_get($value, 'metadata.phone_number_id')
                    !== $phoneId
                ) {
                    continue;
                }

                foreach ($value['messages'] ?? [] as $message) {
                    $answer = match ($message['type'] ?? '') {
                        'text' => data_get($message, 'text.body'),
                        'button' => data_get($message, 'button.text'),
                        'interactive' =>
                            data_get($message, 'interactive.button_reply.title')
                            ?? data_get($message, 'interactive.list_reply.title'),
                        default => null,
                    };

                    if (!is_string($answer)) {
                        continue;
                    }

                    $answer = trim($answer);

                    // Принимаем только целое количество страниц, включая 0.
                    if (!preg_match('/^\d{1,5}$/D', $answer)) {
                        continue;
                    }

                    $this->saveReading($message, (int) $answer);
                }
            }
        }

        return response()->json(['ok' => true]);
    }


    private function saveReading(array $message, int $pages): void
    {
        $phone = (string) ($message['from'] ?? '');
        $messageId = (string) ($message['id'] ?? '');
        $timestamp = (string) ($message['timestamp'] ?? '');

        if (
            $phone === ''
            || $messageId === ''
            || !ctype_digit($timestamp)
            || (int) $timestamp <= 0
        ) {
            Log::warning('WhatsApp: missing message fields', [
                'message_id' => $messageId,
            ]);

            return;
        }

        $sentAt = (int) $timestamp;

        $date = CarbonImmutable::createFromTimestampUTC($sentAt)
            ->setTimezone('Asia/Bishkek')
            ->toDateString();

        $result = DB::transaction(function () use (
            $phone, $messageId, $sentAt, $date, $pages
        ) {
            // Блокировка упорядочивает одновременные ответы одного клиента.
            $customers = Customer::query()
                ->where('phone', $phone)
                ->lockForUpdate()
                ->get();

            if ($customers->count() !== 1) {
                return 'customer_missing_or_ambiguous';
            }

            $customer = $customers->first();

            if (
                DB::table('whatsapp_incoming_messages')
                    ->where('message_id', $messageId)
                    ->exists()
            ) {
                return 'duplicate_message';
            }

            $reports = Schadule::query()
                ->where('customer_id', $customer->id)
                ->where('date', $date)
                ->lockForUpdate()
                ->get();

            // Если ранее созданы дубли вручную, не выбираем случайную запись.
            if ($reports->count() > 1) {
                return 'duplicate_daily_reports';
            }

            $latestTimestamp = DB::table('whatsapp_incoming_messages')
                ->where('customer_id', $customer->id)
                ->where('date', $date)
                ->max('sent_at');

            DB::table('whatsapp_incoming_messages')->insert([
                'message_id' => $messageId,
                'customer_id' => $customer->id,
                'date' => $date,
                'sent_at' => $sentAt,
                'pages' => $pages,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Запоздавшее старое событие не заменяет более новый ответ.
            if ($latestTimestamp !== null && $sentAt < $latestTimestamp) {
                return 'older_message';
            }

            Schadule::updateOrCreate(
                [
                    'customer_id' => $customer->id,
                    'date' => $date,
                ],
                [
                    'total' => $pages,
                ]
            );

            return 'saved';
        }, 3);

        Log::info('WhatsApp reading result', [
            'message_id' => $messageId,
            'date' => $date,
            'pages' => $pages,
            'result' => $result,
        ]);
    }
}
