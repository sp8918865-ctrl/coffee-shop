<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningTable extends Model
{
    public $timestamps = false;

    protected $table = 'tables';

    protected $primaryKey = 'table_id';

    protected $guarded = ['table_id'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id', 'table_id');
    }
}
