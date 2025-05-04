<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budgets extends Model
{
    protected $fillable = [
        'user_id',
        'kategori_id',
        'jumlah',
    ];
}
