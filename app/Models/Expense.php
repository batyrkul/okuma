<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = ['money_transaction_id', 'expense_type_id', 'expense_item_id', 'purpose', 'attachment_path'];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(MoneyTransaction::class, 'money_transaction_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ExpenseItem::class, 'expense_item_id');
    }
}
