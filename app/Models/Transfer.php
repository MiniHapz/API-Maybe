<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'user_id',
        'user_diterima_id',
        'jumlah',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userDiterima()
    {
        return $this->belongsTo(User::class, 'user_diterima_id');
    }

    public function getWaktuTransaksiAttribute()
    {
        return $this->created_at->format('d-m-Y H:i:s');
    }
}