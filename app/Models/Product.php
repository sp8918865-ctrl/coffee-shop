<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $guarded = ['product_id'];

    protected function casts(): array
    {
        return [
            'stok' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function scopeAvailableForSale(Builder $query): Builder
    {
        return $query->where('is_available', true)->where('stok', '>', 0);
    }

    public function deductStock(int $quantity): bool
    {
        if ($quantity < 1) {
            return false;
        }

        return static::query()
            ->whereKey($this->getKey())
            ->where('is_available', true)
            ->where('stok', '>=', $quantity)
            ->decrement('stok', $quantity) === 1;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class, 'product_id');
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_product', 'product_id', 'ingredient_id')
            ->withPivot('quantity_required');
    }
}
