<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferRecipient extends Model
{
    protected $fillable = [
        'bank',
        'account_name',
        'account_number',
    ];
}
