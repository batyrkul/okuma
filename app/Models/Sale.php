<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = ['number', 'buyer_id', 'warehouse_id', 'document_date', 'due_date', 'status', 'currency', 'subtotal', 'discount', 'total', 'cost_total', 'note', 'created_by', 'posted_by', 'posted_at'];

    protected function casts(): array
    {
        return ['document_date' => 'date', 'due_date' => 'date', 'posted_at' => 'datetime', 'subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'total' => 'decimal:2', 'cost_total' => 'decimal:2'];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class, 'buyer_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'document');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(MoneyTransaction::class, 'document');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
