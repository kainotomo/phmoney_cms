<?php

namespace Kainotomo\PHMoney\Http\Controllers\Reports;

use Kainotomo\PHMoney\Http\Controllers\ReportController;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Commodity;
use Kainotomo\PHMoney\Models\Split;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;
use Kainotomo\PHMoney\Models\Setting;
use Kainotomo\PHMoney\Models\Slot;

class IncomeExpenseController extends ReportController
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function cash_flow(Request $request)
    {
        $date_start = $this->getStartDate($request);
        $date_end = $this->getEndDate($request);

        $accounts = Account::getFlatList(false, true);
        if ($request->accounts) {
            $pks = explode(',', $request->accounts);
            $selected_accounts = $accounts->whereIn('pk', $pks);
            $other_accounts = $accounts->whereNotIn('pk', $pks);
        } else {
            $selected_accounts = $accounts->whereIn('type', Account::ASSETS);
            $other_accounts = $accounts->whereNotIn('type', Account::ASSETS);
        }

        if ($request->level) {
            $selected_accounts = $selected_accounts->where('level', '<=', $request->level);
        }

        $account_guids = $other_accounts->pluck('guid');
        $splits = Split::select(
            'tx_guid',
            'account_guid',
            DB::raw('SUM(1.0*value_num/value_denom) as amount')
        )
            ->whereHas('transaction', function ($query) use ($date_start, $date_end) {
                $query->where('post_date', '>=', $date_start);
                $query->where('post_date', '<=', $date_end);
            })
            ->whereIn('account_guid', $other_accounts->pluck('guid'))
            ->groupBy('account_guid')
            ->get();

        $splits = $splits->transform(function ($item, $key) use ($other_accounts) {
            $account = $other_accounts->firstWhere('guid', $item->account_guid);
            return collect([
                'guid' => $account['guid'],
                'name' => $account['name'],
                'commodity' => $account['commodity'],
                'amount' => (float) $item->amount,
            ]);
        });

        $money_out = $splits->where('amount', '>', 0);
        $money_out_amount = $money_out->sum('amount');
        $money_in = $splits->where('amount', '<', 0);
        $money_in = $money_in->transform(function ($item, $key) {
            return collect([
                'guid' => $item['guid'],
                'name' => $item['name'],
                'commodity' => $item['commodity'],
                'amount' => abs($item['amount']),
            ]);
        });
        $money_in_amount = $money_in->sum('amount');

        return Jetstream::inertia()->render(request(), 'Reports/IncomeExpense/CashFlow', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => $accounts,
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Cash Flow",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency, true) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'date_start' => $date_start,
            'date_end' => $date_end,
            'selected_accounts' => $selected_accounts,
            'money_out' => $money_out,
            'money_out_amount' => $money_out_amount,
            'money_in' => $money_in,
            'money_in_amount' => $money_in_amount,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function cash_flow_columnchart(Request $request)
    {
        $chart = $this->chart($request, [
            0 => [
                'name' => 'Money In',
                'accounts' => Account::INCOMES
            ],
            1 => [
                'name' => 'Money Out',
                'accounts' => Account::EXPENSES
            ],
        ]);

        $chart['columns'][3] = ['type' => 'number', 'name' => 'Net Flow'];

        for ($i = 0; $i < count($chart['rows']); $i++) {
            $chart['rows'][$i][1] = -$chart['rows'][$i][1];
            $chart['rows'][$i][3] = $chart['rows'][$i][1] - $chart['rows'][$i][2];
        }

        return Jetstream::inertia()->render(request(), 'Reports/ColumnChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList(),
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Cash Flow Columnchart",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency, true) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'date_start' => $chart['date_start'],
            'date_end' => $chart['date_end'],
            'columns' => $chart['columns'],
            'rows' => $chart['rows'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function expenses_columnchart(Request $request)
    {
        $chart = $this->chart($request, [
            0 => [
                'name' => 'Amount',
                'accounts' => Account::EXPENSES
            ]
        ]);

        return Jetstream::inertia()->render(request(), 'Reports/ColumnChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList(),
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Expenses",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency, true) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'date_start' => $chart['date_start'],
            'date_end' => $chart['date_end'],
            'columns' => $chart['columns'],
            'rows' => $chart['rows'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function expenses_piechart(Request $request)
    {
        $piechart = $this->piechart($request, Account::EXPENSES);

        return Jetstream::inertia()->render(request(), 'Reports/PieChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => $piechart['accounts'],
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Expenses",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency, true) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'level' => $request->level,
            'date_end' => $piechart['date_end'],
            'rows' => $piechart['rows'],
            'total' => $piechart['total'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function incomeexpense_columnchart(Request $request)
    {
        $chart = $this->chart($request, [
            0 => [
                'name' => 'Money In',
                'accounts' => Account::INCOMES
            ],
            1 => [
                'name' => 'Money Out',
                'accounts' => Account::EXPENSES
            ],
        ]);

        $chart['columns'][3] = ['type' => 'number', 'name' => 'Net Flow'];

        for ($i = 0; $i < count($chart['rows']); $i++) {
            $chart['rows'][$i][1] = -$chart['rows'][$i][1];
            $chart['rows'][$i][3] = $chart['rows'][$i][1] - $chart['rows'][$i][2];
        }

        return Jetstream::inertia()->render(request(), 'Reports/ColumnChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList(),
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Income & Expense",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency, true) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'date_start' => $chart['date_start'],
            'date_end' => $chart['date_end'],
            'columns' => $chart['columns'],
            'rows' => $chart['rows'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function incomeexpense_linechart(Request $request)
    {
        $chart = $this->chart($request, [
            0 => [
                'name' => 'Money In',
                'accounts' => Account::INCOMES
            ],
            1 => [
                'name' => 'Money Out',
                'accounts' => Account::EXPENSES
            ],
        ]);

        $chart['columns'][3] = ['type' => 'number', 'name' => 'Net Flow'];

        for ($i = 0; $i < count($chart['rows']); $i++) {
            $chart['rows'][$i][1] = -$chart['rows'][$i][1];
            $chart['rows'][$i][3] = $chart['rows'][$i][1] - $chart['rows'][$i][2];
        }

        return Jetstream::inertia()->render(request(), 'Reports/LineChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList(),
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Income & Expense",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency, true) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'date_start' => $chart['date_start'],
            'date_end' => $chart['date_end'],
            'columns' => $chart['columns'],
            'rows' => $chart['rows'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function income_columnchart(Request $request)
    {
        $chart = $this->chart($request, [
            0 => [
                'name' => 'Amount',
                'accounts' => Account::INCOMES
            ]
        ]);

        for ($i = 0; $i < count($chart['rows']); $i++) {
            $chart['rows'][$i][1] = -$chart['rows'][$i][1];
        }

        return Jetstream::inertia()->render(request(), 'Reports/ColumnChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList(),
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Income",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency, true) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'date_start' => $chart['date_start'],
            'date_end' => $chart['date_end'],
            'columns' => $chart['columns'],
            'rows' => $chart['rows'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function income_piechart(Request $request)
    {
        $piechart = $this->piechart($request, Account::INCOMES);

        return Jetstream::inertia()->render(request(), 'Reports/PieChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => $piechart['accounts'],
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Income",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency, true) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'level' => $request->level,
            'date_end' => $piechart['date_end'],
            'rows' => $piechart['rows'],
            'total' => $piechart['total'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function profit_loss(Request $request)
    {
        $date_start = $this->getStartDate($request);
        $date_end = $this->getEndDate($request);

        $slot_ids = Slot::select('obj_guid')->where('name', 'book_closing')->pluck('obj_guid');

        $amounts = DB::connection('phmoney_portfolio')->table('splits')
            ->select(
                'accounts.guid',
                'accounts.name',
                DB::raw('sum(1.0*phmprt_splits.value_num/phmprt_splits.value_denom) as amount'),
                'transactions.post_date',
                'commodities.mnemonic',
                'commodities.fraction',
            )
            ->whereIn('accounts.account_type', array_merge(Account::INCOMES, Account::EXPENSES))
            ->where('splits.team_id', $request->user()->currentTeam->id)
            ->where('accounts.team_id', $request->user()->currentTeam->id)
            ->where('transactions.team_id', $request->user()->currentTeam->id)
            ->where('commodities.team_id', $request->user()->currentTeam->id)
            ->where('transactions.post_date', '>=', $date_start)
            ->where('transactions.post_date', '<=', $date_end)
            ->whereNotIn('splits.tx_guid', $slot_ids)
            ->leftJoin('accounts', 'accounts.guid', '=', 'splits.account_guid')
            ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
            ->leftJoin('commodities', 'commodities.guid', '=', 'accounts.commodity_guid')
            ->groupBy('splits.account_guid')
            ->get();

        $accounts = Account::getFlatList(false, true, null, null, 0, $date_start, $date_end, $amounts);
        $items = collect($accounts->all());

        if ($request->accounts) {
            $items = $items->whereIn('pk', explode(",", $request->accounts));
        }

        $incomes_items = $items->whereIn('type', Account::INCOMES);
        $incomes = [
            'items' => !is_null($request->level) ? $incomes_items->where('level', '<=', $request->level) : $incomes_items,
            'total' => abs($incomes_items->sum('amount'))
        ];

        $expenses_items = $items->whereIn('type', Account::EXPENSES);
        $expenses = [
            'items' => !is_null($request->level) ? $expenses_items->where('level', '<=', $request->level) : $expenses_items,
            'total' => $expenses_items->sum('amount')
        ];

        return Jetstream::inertia()->render(request(), 'Reports/IncomeExpense/ProfitLoss', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => $accounts,
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Profit & Loss",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'date_start' => $date_start,
            'date_end' => $date_end,
            'level' => $request->level,
            'incomes' => $incomes,
            'expenses' => $expenses,
            'net_income' => $incomes['total'] - $expenses['total'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function trial_balance(Request $request)
    {
        $date_start = $this->getStartDate($request);
        $date_end = $this->getEndDate($request);

        $amounts = DB::connection('phmoney_portfolio')->table('splits')
            ->select(
                'accounts.guid',
                'accounts.name',
                DB::raw('sum(1.0*phmprt_splits.value_num/phmprt_splits.value_denom) as amount'),
                'transactions.post_date',
                'commodities.mnemonic',
                'commodities.fraction',
            )
            ->where('transactions.post_date', '<=', $date_end)
            ->where('splits.team_id', $request->user()->currentTeam->id)
            ->where('accounts.team_id', $request->user()->currentTeam->id)
            ->where('transactions.team_id', $request->user()->currentTeam->id)
            ->where('commodities.team_id', $request->user()->currentTeam->id)
            ->leftJoin('accounts', 'accounts.guid', '=', 'splits.account_guid')
            ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
            ->leftJoin('commodities', 'commodities.guid', '=', 'accounts.commodity_guid')
            ->groupBy('splits.account_guid')
            ->get();

        $accounts = Account::getFlatList(false, true, null, null, 0, $date_start, $date_end, $amounts);
        $items = collect($accounts->all());

        if ($request->accounts) {
            $items = $items->whereIn('pk', explode(",", $request->accounts));
        }

        $items = $items->transform(function ($item, $key) {
            return collect([
                'guid' => $item['guid'],
                'name' => $item['name'],
                'name_simple' => $item['name_simple'],
                'name_indent' => $item['name_indent'],
                'code' => $item['code'],
                'level' => $item['level'],
                'commodity' => $item['commodity'],
                'debit' => $item['amount'] > 0 ? $item['amount'] : null,
                'credit' => $item['amount'] < 0 ? abs($item['amount']) : null,
            ]);
        });

        if ($request->export_csv === "true") {
            return response()->streamDownload(function () use ($items) {
                echo $items->toInlineCsv(['name', 'code', 'debit', 'credit']);
            }, __FUNCTION__ . '.csv');
        }

        if ($request->export_json === "true") {
            return response()->streamDownload(function () use ($items) {
                echo json_encode($items);
            }, __FUNCTION__ . '.json');
        }

        return Jetstream::inertia()->render(request(), 'Reports/IncomeExpense/TrialBalance', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => $accounts,
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Trial Balance",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency, true) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'date_start' => $date_start,
            'date_end' => $date_end,
            'items' => $items,
            'total_debit' => $items->sum('debit'),
            'total_credit' => $items->sum('credit'),
        ]);
    }
}
