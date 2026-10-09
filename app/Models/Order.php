<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    protected $guarded = ['order_id'];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function table()
    {
        return $this->belongsTo(DiningTable::class, 'table_id', 'table_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id', 'order_id');
    }

    public function getStatusAttribute(): ?string
    {
        return [
            'pending' => 'new',
            'in_progress' => 'brewing',
        ][$this->attributes['order_status'] ?? ''] ?? ($this->attributes['order_status'] ?? null);
    }

    public function setStatusAttribute(string $status): void
    {
        $this->attributes['order_status'] = [
            'new' => 'pending',
            'brewing' => 'in_progress',
        ][$status] ?? $status;
    }
}