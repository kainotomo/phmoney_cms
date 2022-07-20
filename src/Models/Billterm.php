<?php

namespace Kainotomo\PHMoney\Models;

class Billterm extends Base
{
    protected $fillable = [
        'team_id',
        'name',
        'description',
        'refcount',
        'invisible',
        'parent',
        'type',
        'duedays',
        'discountdays',
        'discount_num',
        'discount_denom',
        'cutoff',
    ];
}
