<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_item_id';

    protected $guarded = ['order_item_id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function variants()
    {
        return $this->belongsToMany(Variant::class, 'order_item_variants', 'order_item_id', 'variant_id')
            ->withPivot('order_item_variant_id');
    }
}