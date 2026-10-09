<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'variant_id';

    protected $guarded = ['variant_id'];

    protected function casts(): array
    {
        return [
            'additional_price' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function orderItems()
    {
        return $this->belongsToMany(OrderItem::class, 'order_item_variants', 'variant_id', 'order_item_id')
            ->withPivot('order_item_variant_id');
    }
}
