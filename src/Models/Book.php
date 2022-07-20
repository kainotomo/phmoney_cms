<?php

namespace Kainotomo\PHMoney\Models;

class Book extends Base
{
    /**
     * Has one parent root account
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function root_account()
    {
        return $this->belongsTo(Account::class, 'root_account_guid', 'guid');
    }

}
