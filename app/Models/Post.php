<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    //beri akses create & update
    protected $fillable = [
        'pic',
        'nama',
        'keterangan'
    ];

    //ijin  upload gambar
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($pic) => url('/storage/posts'. 'pic'),
        );
    }
}
