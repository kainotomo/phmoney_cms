<?php

namespace Kainotomo\PHMoney\Models;

class Taxtable extends Base
{
    protected $fillable = [
        'team_id',
        'name',
        'refcount',
        'invisible',
        'parent',
    ];

    protected $with = ['entries'];

    public function entries() {
        return $this->hasMany(TaxtableEntry::class, 'taxtable', 'guid');
    }

}
