<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Unit;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_id', 'unit', 'sku', 'barcode', 'brand_id','image', 'name', 'color', 'volume', 'purchase_price', 'sale_price', 'min_stock', 'is_active','color_code'];

    protected function casts(): array
    {
        return ['volume' => 'decimal:3', 'unit' => Unit::class, 'purchase_price' => 'decimal:4', 'sale_price' => 'decimal:4', 'min_stock' => 'decimal:3', 'is_active' => 'boolean', ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (blank($product->sku)) {
                $product->sku = self::generateSku();
            }

            if (blank($product->barcode)) {
                $product->barcode = self::generateBarcode();
            }
        });
    }

    public static function generateSku(): string
    {
        do {
            $sku = 'SKU-'. Str::upper(
                    Str::random(6)
                );
        } while (self::where('sku', $sku)->exists());

        return $sku;
    }

    public static function generateBarcode(): string
    {
        do {
            /*
             * Генерируем первые 12 цифр EAN-13.
             */
            $code = '';

            for ($i = 0; $i < 12; $i++) {
                $code .= random_int(0, 9);
            }

            /*
             * Рассчитываем последнюю контрольную цифру.
             */
            $sum = 0;

            for ($i = 0; $i < 12; $i++) {
                $digit = (int) $code[$i];

                $sum += ($i % 2 === 0)
                    ? $digit
                    : $digit * 3;
            }

            $checkDigit = (10 - ($sum % 10)) % 10;

            $barcode = $code . $checkDigit;
        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, "brand_id");
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
