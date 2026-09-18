<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoneyTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = ['number', 'direction', 'cash_account_id', 'counterparty_id', 'document_type', 'document_id', 'transaction_date', 'amount', 'currency', 'payment_method', 'status', 'comment', 'created_by'];

    protected function casts(): array
    {
        return ['transaction_date' => 'date', 'amount' => 'decimal:2'];
    }

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class);
    }

    public function document(): MorphTo
    {
        return $this->morphTo();
    }

    public function expense(): HasOne
    {
        return $this->hasOne(Expense::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
