<?php

namespace Kainotomo\PHMoney\Models;

class Customer extends Base
{
    protected $fillable = [
        'team_id',
        'name',
        'id',
        'notes',
        'active',
        'discount_num',
        'discount_denom',
        'credit_num',
        'credit_denom',
        'currency',
        'tax_override',
        'addr_name',
        'addr_addr1',
        'addr_addr2',
        'addr_addr3',
        'addr_addr4',
        'addr_phone',
        'addr_fax',
        'addr_email',
        'shipaddr_name',
        'shipaddr_addr1',
        'shipaddr_addr2',
        'shipaddr_addr3',
        'shipaddr_addr4',
        'shipaddr_phone',
        'shipaddr_fax',
        'shipaddr_email',
        'terms',
        'tax_included',
        'taxtable',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $with = ['commodity', 'billterm', 'tax'];

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
     * Belongs to Billterm
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function billterm()
    {
        return $this->belongsTo(Billterm::class, 'terms', 'guid');
    }

    /**
     * Belongs to Taxtable
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function tax()
    {
        return $this->belongsTo(Taxtable::class, 'taxtable', 'guid');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'owner_guid', 'guid');
    }
}
