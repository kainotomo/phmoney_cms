<?php

namespace Kainotomo\PHMoney\Models;

class Employee extends Base
{
    protected $fillable = [
        'team_id',
        'username',
        'id',
        'language',
        'acl',
        'active',
        'currency',
        'ccard_guid',
        'workday_num',
        'workday_denom',
        'rate_num',
        'rate_denom',
        'addr_name',
        'addr_addr1',
        'addr_addr2',
        'addr_addr3',
        'addr_addr4',
        'addr_phone',
        'addr_fax',
        'addr_email',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $with = ['commodity', 'ccard'];

    /**
     * Belongs to Commodity
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function commodity()
    {
        return $this->belongsTo(Commodity::class, 'currency', 'guid');
    }

    /**
     * Belongs to Account
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function ccard()
    {
        return $this->belongsTo(Account::class, 'ccard_guid', 'guid');
    }

}
