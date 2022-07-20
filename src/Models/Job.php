<?php

namespace Kainotomo\PHMoney\Models;

class Job extends Base
{
    protected $fillable = [
        'team_id',
        'id',
        'name',
        'reference',
        'active',
        'owner_type',
        'owner_guid'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $with = ['customer', 'vendor'];

    /**
     * Belongs to Customer
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'owner_guid', 'guid');
    }

    /**
     * Belongs to vendor
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'owner_guid', 'guid');
    }

    /**
     * Define a one-to-one relationship.
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rate() {
        return $this->hasOne(Slot::class, 'obj_guid', 'guid')->where(['name' => 'job-rate', 'slot_type' => 3]);
    }

}
