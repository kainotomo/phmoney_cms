<?php

namespace Kainotomo\PHMoney\Models;


class Commodity extends Base
{
    protected $table = 'commodities';

    protected $fillable = [
        'team_id', 'namespace', 'mnemonic', 'fullname', 'cusip', 'fraction', 'quote_source', 'quote_tz'
    ];

    const NAMESPACES = ['AMEX', 'EUREX',  'FUND', 'NASDAQ', 'NYSE'];
    const CURRENCY = 'CURRENCY';

    /**
     * Has one parent Account
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'commodity_guid', 'guid');
    }

    /**
     * Get team active commodity
     *
     * @return void
     */
    public static function active_commodity() {
        return Commodity::where('namespace', Commodity::CURRENCY)->first();
    }

}
