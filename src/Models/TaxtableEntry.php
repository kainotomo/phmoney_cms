<?php

namespace Kainotomo\PHMoney\Models;

class TaxtableEntry extends Base
{
    protected $connection = 'phmoney_portfolio';

    public $timestamps = false;

    protected $fillable = [
        'team_id',
        'taxtable',
        'account',
        'amount_num',
        'amount_denom',
        'type'
    ];

    protected $with = ['tax_account'];

    public function parent() {
        return $this->belongsTo(Taxtable::class, 'taxtable', 'guid');
    }

    public function tax_account() {
        return $this->belongsTo(Account::class, 'account', 'guid');
    }
}
