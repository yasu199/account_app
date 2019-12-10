<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected  $fillable = [
        'genre_id',
        'genre_name',
        'status'
    ];
}
