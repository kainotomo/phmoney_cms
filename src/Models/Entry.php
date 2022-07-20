<?php

namespace Kainotomo\PHMoney\Models;

class Entry extends Base
{
    protected $fillable = [
        'team_id',
        'date',
        'date_entered',
        'description',
        'action',
        'notes',
        'quantity_num',
        'quantity_denom',
        'i_acct',
        'i_price_num',
        'i_price_denom',
        'i_discount_num',
        'i_discount_denom',
        'invoice',
        'i_disc_type',
        'i_disc_how',
        'i_taxable',
        'i_taxincluded',
        'i_taxtable',
        'b_acct',
        'b_price_num',
        'b_price_denom',
        'bill',
        'b_taxable',
        'b_taxincluded',
        'b_taxtable',
        'b_paytype',
        'billable',
        'billto_type',
        'billto_guid',
        'order_guid',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'date_entered' => 'datetime:Y-m-d',
        'i_taxable' => 'boolean',
        'i_taxincluded' => 'boolean',
        'b_taxable' => 'boolean',
        'b_taxincluded' => 'boolean',
        'billable' => 'boolean',
    ];

    public function parent_invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice', 'guid');
    }

    public function parent_bill()
    {
        return $this->belongsTo(Invoice::class, 'bill', 'guid');
    }

    public function invoice_account()
    {
        return $this->belongsTo(Account::class, 'i_acct', 'guid');
    }

    public function invoice_taxtable()
    {
        return $this->belongsTo(Taxtable::class, 'i_taxtable', 'guid')->with('entries');
    }

    public function bill_account()
    {
        return $this->belongsTo(Account::class, 'b_acct', 'guid');
    }

    public function bill_taxtable()
    {
        return $this->belongsTo(Taxtable::class, 'b_taxtable', 'guid');
    }

    public function getDenomAttribute($value)
    {
        if ($this->parent_invoice) {
            return $this->invoice_account->commodity_scu;
        }
        if ($this->parent_bill) {
            return $this->bill_account->commodity_scu;
        }
    }

    public function getAmountAttribute($value)
    {
        if ($this->parent_invoice) {
            return ($this->quantity_num / $this->quantity_denom) * ($this->i_price_num / $this->i_price_denom);
        }
        if ($this->parent_bill) {
            return ($this->quantity_num / $this->quantity_denom) * (-$this->b_price_num / $this->b_price_denom);
        }
    }

    public function getTaxAttribute($value)
    {
        $tax = 0;

        if ($this->parent_invoice) {
            if ($this->i_taxtable === null || $this->i_taxable === false) {
                return 0;
            }
            foreach ($this->invoice_taxtable->entries as $taxtable_entry) {
                $tax_amount = $taxtable_entry->amount_num / $taxtable_entry->amount_denom;
                if ($taxtable_entry->type == 2) //percentage
                {
                    $tax = $tax + ($this->amount * $tax_amount / 100);
                } else //value
                {
                    $tax = $tax + $tax_amount;
                }
            }
        }
        if ($this->parent_bill) {
            if ($this->b_taxtable === null || $this->b_taxable === false) {
                return 0;
            }
            foreach ($this->bill_taxtable->entries as $taxtable_entry) {
                $tax_amount = $taxtable_entry->amount_num / $taxtable_entry->amount_denom;
                if ($taxtable_entry->type == 2) //percentage
                {
                    $tax = $tax - ($this->amount * $tax_amount / 100);
                } else //value
                {
                    $tax = $tax - $tax_amount;
                }
            }
        }

        return $tax;
    }

    public function getDiscountAttribute($value)
    {
        return $this->i_discount_num / $this->i_discount_denom;
    }

    public function getTotalNumAttribute($value)
    {
        if ($this->parent_invoice) {
            $total = $this->i_disc_type === 'PERCENT' ? $this->amount - ($this->amount * $this->discount / 100) : $this->amount - $this->discount;
            if ($this->i_taxincluded) {
                return (-$total + $this->tax) * $this->invoice_account->commodity_scu;
            } else {
                return -$total * $this->invoice_account->commodity_scu;
            }
        }

        if ($this->parent_bill) {
            $total = $this->amount;
            if ($this->b_taxincluded) {
                return (-$total + $this->tax) * $this->bill_account->commodity_scu;
            } else {
                return -$total * $this->bill_account->commodity_scu;
            }
        }
    }

    public function getSaleAttribute($value) {
        return -$this->total_num / $this->denom;
    }
}
