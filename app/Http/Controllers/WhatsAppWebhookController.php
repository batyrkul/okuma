<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        abort_unless(
            hash_equals(
                $expected,
                (string) $request->header('X-Hub-Signature-256')
            ),
            403
        );

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

                    Log::info('WhatsApp reading answer', [
                        'message_id' => $message['id'] ?? null,
                        'phone' => $message['from'] ?? null,
                        'pages' => (int) $answer,
                        'reply_to' => data_get($message, 'context.id'),
                        'timestamp' => $message['timestamp'] ?? null,
                    ]);
                }
            }
        }

        return response()->json(['ok' => true]);
    }
}
