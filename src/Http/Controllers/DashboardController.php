<?php

namespace Kainotomo\PHMoney\Http\Controllers;

use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Commodity;
use Kainotomo\PHMoney\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class DashboardController extends ReportController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $options = Slot::getOptions();
        $request->merge([
            'date_start' => $options['accounting_period']['date_start'],
            'date_end' => $options['accounting_period']['date_end']
        ]);
        $date_start = $this->getStartDate($request);
        $date_end = $this->getEndDate($request);

        $incomes = DB::connection('phmoney_portfolio')->table('splits')
            ->select(
                DB::raw('sum(phmprt_splits.value_num/phmprt_splits.value_denom) as amount'),
            )
            ->where('splits.team_id', $request->user()->currentTeam->id)
            ->where('accounts.team_id', $request->user()->currentTeam->id)
            ->where('transactions.team_id', $request->user()->currentTeam->id)
            ->where('commodities.team_id', $request->user()->currentTeam->id)
            ->whereIn('accounts.account_type', Account::INCOMES)
            ->where('transactions.post_date', '>=', $date_start)
            ->where('transactions.post_date', '<=', $date_end)
            ->leftJoin('accounts', 'accounts.guid', '=', 'splits.account_guid')
            ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
            ->leftJoin('commodities', 'commodities.guid', '=', 'accounts.commodity_guid')
            ->get();

        $receivables = DB::connection('phmoney_portfolio')->table('splits')
            ->select(
                DB::raw('sum(phmprt_splits.value_num/phmprt_splits.value_denom) as amount'),
            )
            ->where('splits.team_id', $request->user()->currentTeam->id)
            ->where('accounts.team_id', $request->user()->currentTeam->id)
            ->where('transactions.team_id', $request->user()->currentTeam->id)
            ->where('commodities.team_id', $request->user()->currentTeam->id)
            ->where('accounts.account_type', Account::RECEIVABLE)
            ->where('transactions.post_date', '>=', $date_start)
            ->where('transactions.post_date', '<=', $date_end)
            ->leftJoin('accounts', 'accounts.guid', '=', 'splits.account_guid')
            ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
            ->leftJoin('commodities', 'commodities.guid', '=', 'accounts.commodity_guid')
            ->get();

        $expenses = DB::connection('phmoney_portfolio')->table('splits')
            ->select(
                DB::raw('sum(phmprt_splits.value_num/phmprt_splits.value_denom) as amount'),
            )
            ->where('splits.team_id', $request->user()->currentTeam->id)
            ->where('accounts.team_id', $request->user()->currentTeam->id)
            ->where('transactions.team_id', $request->user()->currentTeam->id)
            ->where('commodities.team_id', $request->user()->currentTeam->id)
            ->whereIn('accounts.account_type', Account::EXPENSES)
            ->where('transactions.post_date', '>=', $date_start)
            ->where('transactions.post_date', '<=', $date_end)
            ->leftJoin('accounts', 'accounts.guid', '=', 'splits.account_guid')
            ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
            ->leftJoin('commodities', 'commodities.guid', '=', 'accounts.commodity_guid')
            ->get();

        $payables = DB::connection('phmoney_portfolio')->table('splits')
            ->select(
                DB::raw('sum(phmprt_splits.value_num/phmprt_splits.value_denom) as amount'),
            )
            ->where('splits.team_id', $request->user()->currentTeam->id)
            ->where('accounts.team_id', $request->user()->currentTeam->id)
            ->where('transactions.team_id', $request->user()->currentTeam->id)
            ->where('commodities.team_id', $request->user()->currentTeam->id)
            ->where('accounts.account_type', Account::PAYABLE)
            ->where('transactions.post_date', '>=', $date_start)
            ->where('transactions.post_date', '<=', $date_end)
            ->leftJoin('accounts', 'accounts.guid', '=', 'splits.account_guid')
            ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
            ->leftJoin('commodities', 'commodities.guid', '=', 'accounts.commodity_guid')
            ->get();


        $cashflow_columnchart = $this->chart($request, [
            0 => [
                'name' => 'Money In',
                'accounts' => Account::INCOMES
            ],
            1 => [
                'name' => 'Money Out',
                'accounts' => Account::EXPENSES
            ],
        ]);

        $cashflow_columnchart['columns'][3] = ['type' => 'number', 'name' => 'Net Flow'];

        for ($i = 0; $i < count($cashflow_columnchart['rows']); $i++) {
            $cashflow_columnchart['rows'][$i][1] = -$cashflow_columnchart['rows'][$i][1];
            $cashflow_columnchart['rows'][$i][3] = $cashflow_columnchart['rows'][$i][1] - $cashflow_columnchart['rows'][$i][2];
        }

        return Jetstream::inertia()->render(request(), 'Dashboard', [
            'currency' => Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'print' => true,
            'total_income' => -$incomes[0]->amount ?? 0,
            'total_receivables' => $receivables[0]->amount ?? 0,
            'total_expenses' => $expenses[0]->amount ?? 0,
            'total_payables' => $payables[0]->amount ?? 0,
            'total_profit' => -$incomes[0]->amount - $expenses[0]->amount ?? 0,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'total_upcoming' => $receivables[0]->amount - $payables[0]->amount ?? 0,
            'columns' => $cashflow_columnchart['columns'],
            'rows' => $cashflow_columnchart['rows'],
        ]);
    }
}
