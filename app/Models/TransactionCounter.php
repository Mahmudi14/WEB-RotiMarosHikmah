<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionCounter extends Model
{
    protected $fillable = [
        'counter_date',
        'last_number',
    ];

    protected $casts = [
        'counter_date' => 'date',
        'last_number' => 'integer',
    ];
}