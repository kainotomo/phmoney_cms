<?php

namespace Kainotomo\PHMoney\Models;

class Invoice extends Base
{
    protected $fillable = [
        'team_id',
        'id',
        'date_opened',
        'date_posted',
        'notes',
        'active',
        'currency',
        'owner_type',
        'owner_guid',
        'terms',
        'billing_id',
        'post_txn',
        'post_lot',
        'post_acc',
        'billto_type',
        'billto_guid',
        'charge_amt_num',
        'charge_amt_denom'
    ];

    protected $casts = [
        'active' => 'boolean',
        'date_opened' => 'datetime:Y-m-d',
        'date_posted' => 'datetime:Y-m-d',
    ];

    public function getInvoiceTypeAttribute($value)
    {
        if ($this->customer) {
            return "Invoice";
        }

        if ($this->vendor) {
            return "Bill";
        }

        if ($this->employee) {
            return "Voucher";
        }

        if ($this->job) {
            if ($this->job->customer) {
                return "Invoice";
            }
            if ($this->job->vendor) {
                return "Bill";
            }
        }

        return "Invoice";
    }

    public function getOwnerAttribute($value)
    {
        if ($this->customer) {
            return $this->customer;
        }

        if ($this->vendor) {
            return $this->vendor;
        }

        if ($this->employee) {
            return $this->employee;
        }

        if ($this->job) {
            if ($this->job->customer) {
                return $this->job->customer;
            }
            if ($this->job->vendor) {
                return $this->job->vendor;
            }
        }

        return null;
    }

    public function getAutoDescriptionAttribute($value) {
        if ($this->invoice_type === 'Invoice') {
            $value = $this->job ? $this->job->customer->name : $this->customer->name;
        }
        if ($this->invoice_type === 'Bill') {
            $value = $this->job ? $this->job->vendor->name : $this->vendor->name;
        }
        if ($this->invoice_type === 'Voucher') {
            $value = $this->employee->name;
        }
        return $value;
    }

    protected $with = ['customer', 'vendor', 'employee', 'job', 'billto_customer', 'billto_job'];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class, 'commodity', 'guid');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'owner_guid', 'guid');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'owner_guid', 'guid');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'owner_guid', 'guid');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'owner_guid', 'guid');
    }

    public function billto_customer()
    {
        return $this->belongsTo(Customer::class, 'billto_guid', 'guid');
    }

    public function billto_job()
    {
        return $this->belongsTo(Job::class, 'billto_guid', 'guid');
    }

    public function slots()
    {
        return $this->hasMany(Slot::class, 'obj_guid', 'guid');
    }

    public function type()
    {
        return $this->hasOne(Slot::class, 'obj_guid', 'guid')->where(['name' => 'credit-note', 'slot_type' => 1]);
    }

    public function due_date()
    {
        return $this->hasOne(Slot::class, 'obj_guid', 'post_txn')->where(['name' => 'trans-date-due', 'slot_type' => 6]);
    }

    public function billterm()
    {
        return $this->belongsTo(Billterm::class, 'terms', 'guid');
    }

    public function lot()
    {
        return $this->hasOne(Lot::class, 'guid', 'post_lot');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'guid', 'post_txn');
    }

    public function splits()
    {
        return $this->hasMany(Split::class, 'lot_guid', 'post_lot');
    }

    public function invoice_entrys()
    {
        return $this->hasMany(Entry::class, 'invoice', 'guid');
    }

    public function bill_entrys()
    {
        return $this->hasMany(Entry::class, 'bill', 'guid');
    }

    public function getEntrysAttribute($value) {
        if ($this->invoice_type === 'Invoice') {
            return $this->invoice_entrys;
        } else {
            return $this->bill_entrys;
        }
    }
}
