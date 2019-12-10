<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    // user_id,genre_id,budget,target_monthの挿入を許可
    protected  $fillable = [
        'user_id',
        'genre_id',
        'budget',
        'target_month'
    ];
}
