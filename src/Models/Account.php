<?php

namespace Kainotomo\PHMoney\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class Account extends Base
{
    const TYPES = ['ASSET', 'BANK',  'CASH', 'SHARE', 'STOCK', 'FUND', 'LIABILITY', 'RECEIVABLE', 'PAYABLE', 'EQUITY', 'INCOME', 'EXPENSE', 'CREDIT'];
    const ASSETS = ['ASSET', 'CASH', 'BANK', 'STOCK', 'FUND', 'RECEIVABLE'];
    const LIABILITYS = ['CREDIT', 'PAYABLE', 'LIABILITY'];
    const INCOMES = ['INCOME'];
    const EXPENSES = ['EXPENSE'];
    const EQUITYS = ['EQUITY'];
    const ASSET = 'ASSET';
    const BANK = 'BANK';
    const CASH = 'CASH';
    const RECEIVABLE = 'RECEIVABLE';
    const PAYABLE = 'PAYABLE';
    const SHARE = 'SHARE';
    const STOCK = 'STOCK';
    const FUND = 'FUND';
    const LIABILITY = 'LIABILITY';
    const EQUITY = 'EQUITY';
    const INCOME = 'INCOME';
    const EXPENSE = 'EXPENSE';
    const CREDIT = 'CREDIT';

    protected $fillable = [
        'team_id',
        'name',
        'code',
        'description',
        'commodity_scu',
        'hidden',
        'placeholder',
        'account_type',
        'parent_guid',
        'commodity_guid',
        'non_std_scu'
    ];

    protected $with = ['commodity'];

    protected $casts = [
        'hidden' => 'boolean',
        'placeholder' => 'boolean'
    ];

    /**
     * Has one parent Account
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_guid', 'guid');
    }

    /**
     * Belongs to Commodity
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function commodity()
    {
        return $this->belongsTo(Commodity::class, 'commodity_guid', 'guid');
    }

    /**
     * Has many accounts.
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return string
     */
    public function childs()
    {
        return $this->hasMany(Account::class, 'parent_guid', 'guid')->orderBy('name', 'asc');
    }

    /**
     * Accounts tree.
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return string
     */
    public function childs_tree()
    {
        return $this->hasMany(Account::class, 'parent_guid', 'guid')->with('childs_tree')->orderBy('name', 'asc');
    }

    /**
     * Accounts tree with sum.
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return string
     */
    public function childs_tree_with_sum()
    {
        return $this->hasMany(Account::class, 'parent_guid', 'guid')->with('childs_tree_with_sum')->withCount(['splits as amount' => function ($query) {
            $query->select(DB::raw('sum(1.0*value_num/value_denom)'));
        }])->orderBy('name', 'asc');
    }

    /**
     * Has many Splits
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function splits()
    {
        return $this->hasMany(Split::class, 'account_guid', 'guid');
    }

    /**
     * Get full name with parent
     *
     * @param string $value
     * @return string
     */
    public function getFullNameAttribute($value)
    {
        return $this->parent->account_type !== 'ROOT' ? $this->parent->full_name . ':' . $this->name : $this->name;
    }

    /**
     * Calculate amounts with summing children amount
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @param Collection $accounts
     * @return array
     */
    public static function calculateAmountTotal(Collection $accounts, float &$net_assets, float &$profits)
    {
        $sum = 0;
        foreach ($accounts as $account) {
            $account->amount = $account->amount ?? 0;

            if (
                $account->account_type == Account::INCOME ||
                $account->account_type == Account::EXPENSE
            ) {
                $profits -= $account->amount;
            }
            if (
                $account->account_type == Account::ASSET ||
                $account->account_type == Account::BANK ||
                $account->account_type == Account::CASH ||
                $account->account_type == Account::LIABILITY ||
                $account->account_type == Account::RECEIVABLE ||
                $account->account_type == Account::STOCK ||
                $account->account_type == Account::FUND
            ) {
                $net_assets += $account->amount;
            }

            $children_result = 0;
            if ($account->childs_tree_with_sum) {
                $children_result = Account::calculateAmountTotal($account->childs_tree_with_sum, $net_assets, $profits);
            }
            $account->amount_total = $account->amount + $children_result;
            $sum += $account->amount_total;
        }
        return $sum;
    }

    /**
     * Convert accounts list to flat list
     *
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     *
     * @param bool $include_placeloders
     * @param bool $with_root
     * @param Illuminate\Support\Collection $accounts
     * @param Illuminate\Database\Eloquent\Factories\HasFactory $childs
     * @param int $level
     * @param string $date_start
     * @param string $date_end
     * @param Illuminate\Support\Collection $amounts
     * @return \Illuminate\Support\Collection
     */
    public static function getFlatList(bool $with_root = false, bool $include_placeloders = false, SupportCollection $accounts = null, Collection $childs = null, int $level = 0, string $date_start = null, string $date_end = null, SupportCollection $amounts = null)
    {
        $is_first = false;
        if (is_null($accounts)) {
            $accounts = collect();
            $book = Book::with('root_account')->first();
            if ($with_root) {
                $level = 1;
                $accounts[] = collect([
                    'pk' => $book->root_account->pk,
                    'guid' => $book->root_account->guid,
                    'name' => $book->root_account->name,
                    'name_simple' => $book->root_account->name,
                    'name_indent' => $book->root_account->name,
                    'type' => $book->root_account->account_type,
                    'code' => $book->root_account->code,
                    'description' => $book->root_account->description,
                    'placeholder' => $book->root_account->placeholder,
                    'level' => $level,
                    'commodity' => [
                        'guid' => $book->root_account->commodity->guid,
                        'fraction' => $book->root_account->commodity->fraction,
                        'mnemonic' => $book->root_account->commodity->mnemonic,
                        'namespace' => $book->root_account->commodity->namespace,
                    ],
                    'amount' => 0,
                    'amount_sum' => 0,
                ]);
            }
            $childs = $book->root_account->childs_tree()->with('parent')->get();
            $is_first = true;
        }

        $sum = 0;
        foreach ($childs as $child) {
            $child->level = $level;
            $indent = '';
            for ($i = 0; $i < $level; $i++) {
                $indent .= '-';
            }

            if ($amounts) {
                $amount = $amounts->where('guid', $child->guid)->first();
                $amount = $amount ? (float) $amount->amount : 0;
            } else {
                $amount = 0;
            }

            $account = collect([
                'pk' => $child->pk,
                'guid' => $child->guid,
                'name' => $child->full_name,
                'name_simple' => $child->name,
                'name_indent' => $indent . $child->name,
                'code' => $child->code,
                'description' => $child->description,
                'type' => $child->account_type,
                'placeholder' => $child->placeholder,
                'level' => $level,
                'commodity' => [
                    'guid' => $child->commodity->guid,
                    'fraction' => $child->commodity->fraction,
                    'mnemonic' => $child->commodity->mnemonic,
                    'namespace' => $child->commodity->namespace,
                ],
                'amount' => $amount,
                'amount_sum' => $amount,
            ]);

            if ($include_placeloders) {
                $accounts[] = $account;
            } elseif (!$child->placeholder) {
                $accounts[] = $account;
            }

            $child_childs_tree = $child->childs_tree()->with('parent')->get();
            if ($child_childs_tree) {
                $childs_sum = Account::getFlatList($with_root, $include_placeloders, $accounts, $child_childs_tree, $level + 1, $date_start, $date_end, $amounts);
                $account['amount_sum'] += $childs_sum;
            }
            $sum += $account['amount_sum'];
        }

        if ($is_first) {
            return $accounts;
        } else {
            return $sum;
        }
    }
}
