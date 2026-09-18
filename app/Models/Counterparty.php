<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\CounterpartyType;

class Counterparty extends Model
{
    use SoftDeletes;

    protected $fillable = ['type', 'name', 'inn', 'phone', 'email', 'address', 'credit_limit', 'payment_days', 'note', 'is_active'];

    protected function casts(): array
    {
        return ['credit_limit' => 'decimal:2', 'payment_days' => 'integer', 'is_active' => 'boolean', 'type' => CounterpartyType::class];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'buyer_id');
    }

    public function moneyTransactions(): HasMany
    {
        return $this->hasMany(MoneyTransaction::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ScheduledNotification::class);
    }
}
