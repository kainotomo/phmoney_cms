<?php

namespace Kainotomo\PHMoney\Models;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Split extends Base
{
    protected $fillable = [
        'team_id', 'tx_guid', 'account_guid', 'memo', 'action', 'reconcile_state', 'reconcile_date', 'value_num', 'value_denom', 'quantity_num', 'quantity_denom', 'lot_guid'
    ];

    /**
     * Belongs to Account
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_guid', 'guid');
    }

    /**
     * Belongs to Transaction
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'tx_guid', 'guid');
    }

    public function lot()
    {
        return $this->hasOne(Lot::class, 'guid', 'lot_guid');
    }

    /**
     * Get all splits for an account with their balances
     *
     * @param Account $account
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getBalancesForAccount(Account $account)
    {
        $splits = Split::select(
            'guid',
            'tx_guid',
            DB::raw('1.0*value_num/value_denom as amount'),
        )->with(['transaction' => function ($query) {
            $query->select('guid', 'post_date');
        }])
            ->where(['account_guid' => $account->guid])
            ->get();
        $splits = $splits->sortBy(function ($split, $key) {
            return $split->transaction->post_date;
        })->values();

        // calculate balance for all splits
        $balance = 0;
        foreach ($splits as $split) {
            $balance += $split->amount;
            $split->balance = $balance;
        }

        $splits = $splits->transform(function ($item, $key) {
            return collect(['guid' => $item->guid, 'balance' => $item->balance]);
        });
        return $splits;
    }

    /**
     * Get all splits for an account with their balances
     *
     * @param Account $account
     * @param Request $request
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getForAccount(Account $account, Request $request)
    {
        if ($request->page) {
            $balances = Cache::get('balances:' . $account->guid, null);
            if (is_null($balances)) {
                Cache::forget('balances:' . $account->guid);
                $balances = Split::getBalancesForAccount($account);
                Cache::forever('balances:' . $account->guid, $balances);
            }
        } else {
            Cache::forget('balances:' . $account->guid);
            $balances = Split::getBalancesForAccount($account);
            Cache::forever('balances:' . $account->guid, $balances);
        }
        $limit = $request->limit ?? 15;
        $page = $request->page ?? 1;
        $total = $balances->count();
        $balances = $balances->slice(($page - 1) * $limit, $limit);
        $guids = $balances->pluck('guid');

        $query = Split::select(
            '*',
            DB::raw('1.0*value_num/value_denom as amount'),
            DB::raw('1.0*quantity_num/quantity_denom as shares'),
            DB::raw('(1.0*value_num/value_denom)/(quantity_num/quantity_denom) as price')
        )
            ->with(['account', 'transaction', 'transaction.splits' => function ($query) use ($account) {
                $query->select(
                    '*',
                    DB::raw('1.0*value_num/value_denom as amount'),
                    DB::raw('1.0*quantity_num/quantity_denom as shares'),
                    DB::raw('(1.0*value_num/value_denom)/(quantity_num/quantity_denom) as price')
                )->where('account_guid', '<>', $account->guid);
            }, 'transaction.splits.account', 'transaction.commodity'])
            ->whereIn('guid', $guids);
        if ($request->memo) {
            $query->where('memo', 'LIKE', '%' . $request->memo . '%');
        }
        if ($request->description) {
            $query->where(function ($query) use ($request) {
                $query->whereHas('transaction', function ($query) use ($request) {
                    $query->where('description', 'LIKE', '%' . $request->description . '%');
                });
            });
        }
        if ($request->num) {
            $query->where(function ($query) use ($request) {
                $query->whereHas('transaction', function ($query) use ($request) {
                    $query->where('num', 'LIKE', '%' . $request->num . '%');
                });
            });
        }
        if ($request->date_start) {
            $query->where(function ($query) use ($request) {
                $query->whereHas('transaction', function ($query) use ($request) {
                    $date_start = (new Carbon($request->date_start))->startOfDay();
                    $query->where('post_date', '>=', $date_start);
                });
            });
        }
        if ($request->date_end) {
            $query->where(function ($query) use ($request) {
                $query->whereHas('transaction', function ($query) use ($request) {
                    $date_end = (new Carbon($request->date_end))->endOfDay();
                    $query->where('post_date', '<=', $date_end);
                });
            });
        }
        $splits = $query->get();
        $splits = $splits->sortBy(function ($split, $key) {
            return $split->transaction->post_date;
        })->values();

        // calculate balance for all splits
        foreach ($splits as $key => $split) {
            $split->error_message = null;
            $split->precision = strlen(strval($split->value_denom)) - 1;
            $split->precision_shares = strlen(strval($split->quantity_denom)) - 1;

            $split->debit = null;
            if ($split->amount > 0) {
                $split->debit = (float) $split->amount;
            }

            $split->credit = null;
            if ($split->amount < 0) {
                $split->credit = abs($split->amount);
            }

            $balance = $balances->firstWhere('guid', $split->guid);
            $balance = $balance ? $balance['balance'] : null;
            $split->balance = $balance;

            foreach ($split->transaction->splits as $split_child) {
                $precision = strlen(strval($split_child->value_denom)) - 1;
                $split_child->precision = $precision;
                $split_child->error_message = null;
                $split_child->debit = null;
                if ($split_child->value_num > 0) {
                    $split_child->debit = round($split_child->value_num / $split_child->value_denom, $precision);
                }

                $split_child->credit = null;
                if ($split_child->value_num < 0) {
                    $split_child->credit = abs(round($split_child->value_num / $split_child->value_denom, $precision));
                }

                $split_child->precision_shares = strlen(strval($split_child->quantity_denom)) - 1;
                $split_child->shares = round($split_child->quantity_num / $split_child->quantity_denom, $split_child->precision_shares);
                $split_child->shares = $split_child->shares == 0 ? null : $split_child->shares;
                try {
                    $split_child->price = ($split_child->value_num / $split_child->value_denom) / ($split_child->quantity_num / $split_child->quantity_denom);
                } catch (\Throwable $th) {
                    $split_child->price = null;
                }
            }
        }

        return new LengthAwarePaginator($splits, $total, $limit, $page);
    }

    /**
     * Get all splits for an account with their balances
     *
     * @param Transaction $transaction
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getForTransaction(Transaction $transaction)
    {
        $splits = Split::select(
            '*',
            DB::raw('1.0*value_num/value_denom as amount'),
            DB::raw('1.0*quantity_num/quantity_denom as shares'),
            DB::raw('(1.0*value_num/value_denom)/(quantity_num/quantity_denom) as price')
        )
            ->with(['account', 'transaction', 'transaction.splits' => function ($query) {
                $query->select(
                    '*',
                    DB::raw('1.0*value_num/value_denom as amount'),
                    DB::raw('1.0*quantity_num/quantity_denom as shares'),
                    DB::raw('(1.0*value_num/value_denom)/(quantity_num/quantity_denom) as price')
                );
            }, 'transaction.splits.account', 'transaction.commodity'])
            ->where('tx_guid', $transaction->guid)
            ->get();
        $splits = $splits->sortBy(function ($split, $key) {
            return $split->transaction->post_date;
        })->values();

        // calculate balance for all splits
        foreach ($splits as $key => $split) {
            $split->error_message = null;
            $split->precision = strlen(strval($split->value_denom)) - 1;
            $split->precision_shares = strlen(strval($split->quantity_denom)) - 1;

            $split->debit = null;
            if ($split->amount > 0) {
                $split->debit = (float) $split->amount;
            }

            $split->credit = null;
            if ($split->amount < 0) {
                $split->credit = abs($split->amount);
            }
        }

        return $splits;
    }
}
