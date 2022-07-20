<?php

namespace Kainotomo\PHMoney\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Base
{
    use HasFactory;

    protected $casts = [
        'params' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'params'
    ];

}
