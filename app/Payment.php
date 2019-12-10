<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
  // user_id,genre_id,budget,target_monthの挿入を許可
    protected  $fillable = [
        'user_id',
        'genre_id',
        'payment',
        'target_month',
        'flag',
        'memo'
    ];
}
