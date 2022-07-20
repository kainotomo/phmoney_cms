<?php

namespace Kainotomo\PHMoney\Models;

class Lot extends Base
{
    protected $fillable = [
        'team_id', 'account_guid', 'is_closed'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_guid', 'guid');
    }

}
