<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Savings extends Model
{
    protected $fillable = [
        'debit',
        'credit',
        'balance',
    ];
}
