<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ScheduledNotification extends Model
{
    protected $fillable = ['counterparty_id', 'document_type', 'document_id', 'channel', 'recipient', 'message', 'scheduled_at', 'sent_at', 'status', 'error_message'];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime', 'sent_at' => 'datetime'];
    }

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class);
    }

    public function document(): MorphTo
    {
        return $this->morphTo();
    }
}
