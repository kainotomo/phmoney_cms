<?php

namespace Kainotomo\PHMoney\Models;

class Vendor extends Base
{
    protected $fillable = [
        'team_id',
        'name',
        'id',
        'notes',
        'currency',
        'active',
        'tax_override',
        'addr_name',
        'addr_addr1',
        'addr_addr2',
        'addr_addr3',
        'addr_addr4',
        'addr_phone',
        'addr_fax',
        'addr_email',
        'terms',
        'tax_inc',
        'tax_table',
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
        return $this->belongsTo(Taxtable::class, 'tax_table', 'guid');
    }
}
