<?php

namespace Kainotomo\PHMoney\Http\Controllers\Reports;

use Kainotomo\PHMoney\Http\Controllers\ReportController;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Commodity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;
use Kainotomo\PHMoney\Models\Setting;
use Kainotomo\PHMoney\Models\Split;

class AssetsLiabilitiesController extends ReportController
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function balance_sheet(Request $request)
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

        $assets_items = $items->whereIn('type', Account::ASSETS);
        $assets = [
            'items' => !is_null($request->level) ? $assets_items->where('level', '<=', $request->level) : $assets_items,
            'total' => $assets_items->sum('amount')
        ];

        $liabilities_items = $items->whereIn('type', [Account::LIABILITY]);
        $liabilities = [
            'items' => !is_null($request->level) ? $liabilities_items->where('level', '<=', $request->level) : $liabilities_items,
            'total' => abs($liabilities_items->sum('amount'))
        ];

        $equities_items = $items->whereIn('type', [Account::EQUITY]);
        $equities = [
            'items' => !is_null($request->level) ? $equities_items->where('level', '<=', $request->level) : $equities_items,
            'total' => $equities_items->sum('amount')
        ];

        return Jetstream::inertia()->render(request(), 'Reports/AssetsLiabilities/BalanceSheet', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => $accounts,
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Balance Sheet",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency, true) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'date_end' => $date_end,
            'level' => $request->level,
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equities' => $equities,
            'total_equity' => abs($assets['total'] - $liabilities['total']),
            'retained_losses' => abs($assets['total'] - $liabilities['total'] + $equities['total']),
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function assets_columnchart(Request $request)
    {
        $chart = $this->chart($request, [
            0 => [
                'name' => 'Amount',
                'accounts' => Account::ASSETS
            ]
        ]);

        return Jetstream::inertia()->render(request(), 'Reports/ColumnChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList(false, true),
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Assets",
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
    public function assets_piechart(Request $request)
    {
        $piechart = $this->piechart($request, Account::ASSETS);

        return Jetstream::inertia()->render(request(), 'Reports/PieChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => $piechart['accounts'],
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Assets",
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
    public function liabilities_columnchart(Request $request)
    {
        $chart = $this->chart($request, [
            0 => [
                'name' => 'Amount',
                'accounts' => Account::LIABILITYS
            ]
        ]);

        return Jetstream::inertia()->render(request(), 'Reports/ColumnChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList(),
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Liabilities",
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
    public function liabilities_piechart(Request $request)
    {
        $piechart = $this->piechart($request, Account::LIABILITYS);

        return Jetstream::inertia()->render(request(), 'Reports/PieChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => $piechart['accounts'],
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Liabilities",
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
    public function networth_columnchart(Request $request)
    {
        $chart = $this->chart($request, [
            0 => [
                'name' => 'Amount',
                'accounts' => array_merge(Account::ASSETS, Account::LIABILITYS)
            ]
        ]);

        return Jetstream::inertia()->render(request(), 'Reports/ColumnChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList(),
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Net Worth",
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
    public function networth_linechart(Request $request)
    {
        $chart = $this->chart($request, [
            0 => [
                'name' => 'Amount',
                'accounts' => array_merge(Account::ASSETS, Account::LIABILITYS)
            ]
        ]);

        return Jetstream::inertia()->render(request(), 'Reports/LineChart', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList(),
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Net Worth",
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
    public function general_ledger(Request $request)
    {
        $date_start = $this->getStartDate($request);
        $date_end = $this->getEndDate($request);

        $items = [];
        if ($request->accounts) {

            if ($request->export_csv === "true") {

                $rows = DB::connection('phmoney_portfolio')->table('splits')
                    ->select(
                        'accounts.name',
                        'accounts.code',
                        DB::raw('1.0*phmprt_splits.value_num/phmprt_splits.value_denom as amount'),
                        'transactions.post_date',
                        'commodities.mnemonic',
                        'transactions.description',
                        'transactions.num',
                        'splits.memo',
                    )
                    ->where('transactions.post_date', '>=', $date_start)
                    ->where('transactions.post_date', '<=', $date_end)
                    ->where('splits.team_id', $request->user()->currentTeam->id)
                    ->where('accounts.team_id', $request->user()->currentTeam->id)
                    ->where('transactions.team_id', $request->user()->currentTeam->id)
                    ->where('commodities.team_id', $request->user()->currentTeam->id)
                    ->leftJoin('accounts', 'accounts.guid', '=', 'splits.account_guid')
                    ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
                    ->leftJoin('commodities', 'commodities.guid', '=', 'accounts.commodity_guid')
                    ->orderBy('transactions.post_date')
                    ->get();

                $rows = $rows->transform(function ($row, $key) {
                    return collect([
                        'name' => $row->name,
                        'code' => $row->code,
                        'num' => $row->num,
                        'description' => $row->description,
                        'memo' => $row->memo,
                        'mnemonic' => $row->mnemonic,
                        'amount' => $row->amount,
                    ]);
                });

                return response()->streamDownload(function () use ($rows) {
                    echo $rows->toInlineCsv([
                        'name',
                        'code',
                        'num',
                        'description',
                        'memo',
                        'mnemonic',
                        'amount'
                    ]);
                }, __FUNCTION__ . '.csv');
            }

            $pks = explode(',', $request->accounts);
            foreach ($pks as $pk) {
                $item = [];
                $account = Account::where('pk', $pk)->first();
                $splits = $account->splits()->select(
                    'tx_guid',
                    DB::raw('1.0*value_num/value_denom as amount')
                )
                    ->whereHas('transaction', function ($query) use ($date_start) {
                        $query->where('post_date', '<', $date_start);
                    })->get();
                $balance_bf = $splits->sum('amount');

                $splits = $account->splits()->select(
                    'guid',
                    'memo',
                    'tx_guid',
                    DB::raw('1.0*value_num/value_denom as amount'),
                )->with(['transaction' => function ($query) {
                    $query->select('guid', 'post_date', 'num', 'description');
                }])->whereHas('transaction', function ($query) use ($date_start, $date_end) {
                    $query->where('post_date', '>=', $date_start);
                    $query->where('post_date', '<=', $date_end);
                })->get();
                $splits = $splits->sortBy(function ($split, $key) {
                    return $split->transaction->post_date;
                })->values();

                // calculate balance
                $balance = $balance_bf;
                foreach ($splits as $split) {
                    $balance += $split->amount;
                    $split->balance = $balance;
                    $split->debit = $split->amount > 0 ? (float) $split->amount : null;
                    $split->credit = $split->amount < 0 ? abs($split->amount) : null;
                }

                $splits = $splits->transform(function ($item, $key) {
                    return collect([
                        'post_date' => $item->transaction->post_date,
                        'guid' => $item->guid,
                        'amount' => (float) $item->amount,
                        'credit' => $item->credit,
                        'debit' => $item->debit,
                        'balance' => $item->balance,
                        'memo' => $item->memo,
                        'num' => $item->transaction->num,
                        'description' => $item->transaction->description,
                    ]);
                });

                $item['account'] = $account;
                $item['balance_bf'] = $balance_bf;
                $item['splits'] = $splits;
                $item['total'] = $balance;
                $items[] = $item;
            }

            if ($request->export_json === "true") {
                return response()->streamDownload(function () use ($items) {
                    echo json_encode($items);
                }, __FUNCTION__ . '.json');
            }
        }

        return Jetstream::inertia()->render(request(), 'Reports/AssetsLiabilities/GeneralLedger', [
            'print' => $request->print == 'true' ? true :  false,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList(false, true),
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "General Ledger",
            'company' => $request->company ?? null,
            'currency' => $request->currency ? json_decode($request->currency, true) : Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'date_start' => $date_start,
            'date_end' => $date_end,
            'items' => $items,
        ]);
    }
}
