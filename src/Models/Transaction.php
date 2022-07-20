<?php

namespace Kainotomo\PHMoney\Models;

use Kainotomo\PHMoney\Casts\DateTime;

class Transaction extends Base
{
    protected $fillable = [
        'team_id', 'currency_guid', 'num', 'post_date', 'enter_date', 'description'
    ];

    protected $casts = [
        'post_date' => DateTime::class,
        'enter_date' => DateTime::class
    ];

    /**
     * Belongs to Commodity
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function commodity()
    {
        return $this->belongsTo(Commodity::class, 'currency_guid', 'guid');
    }

    /**
     * Has many splits.
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function splits()
    {
        return $this->hasMany(Split::class, 'tx_guid', 'guid');
    }

}
