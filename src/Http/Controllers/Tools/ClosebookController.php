<?php

namespace Kainotomo\PHMoney\Http\Controllers\Tools;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Providers\Jetstream\Jetstream;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Kainotomo\PHMoney\Http\Requests\ClosebookRequest;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Slot;
use Kainotomo\PHMoney\Models\Transaction;

class ClosebookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $accounts = Account::getFlatList()->where('type', 'EQUITY');
        return Jetstream::inertia()->render(request(), 'Tools/Closebook/Index', [
            'accounts' => $accounts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\ClosebookRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClosebookRequest $request)
    {
        $validated = $request->validated();

        $date_start = (new Carbon($validated['post_date']))->startOfYear();
        $date_end = (new Carbon($validated['post_date']))->endOfYear();
        $year = (new Carbon($validated['post_date']))->year;

        //********************************************
        // Handle incomes
        //********************************************

        $accounts = DB::connection('phmoney_portfolio')->table('splits')
            ->select(
                'accounts.guid',
                'accounts.name',
                DB::raw('sum(1.0*phmprt_splits.value_num/phmprt_splits.value_denom) as amount'),
                'transactions.post_date',
                'commodities.mnemonic',
                'commodities.fraction',
            )
            ->whereIn('accounts.account_type', Account::INCOMES)
            ->where('splits.team_id', $request->user()->currentTeam->id)
            ->where('accounts.team_id', $request->user()->currentTeam->id)
            ->where('transactions.team_id', $request->user()->currentTeam->id)
            ->where('commodities.team_id', $request->user()->currentTeam->id)
            ->where('transactions.post_date', '>=', $date_start)
            ->where('transactions.post_date', '<=', $date_end)
            ->leftJoin('accounts', 'accounts.guid', '=', 'splits.account_guid')
            ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
            ->leftJoin('commodities', 'commodities.guid', '=', 'accounts.commodity_guid')
            ->groupBy('splits.account_guid')
            ->get();

        $post_account = Account::where('guid', $validated['income_account_guid'])->first();
        $denom = $post_account->commodity_scu;
        $num = $accounts->sum('amount') * $denom;

        $transaction = Transaction::create([
            'currency_guid' => $post_account->commodity_guid,
            'num' => '',
            'post_date' => $validated['post_date'],
            'enter_date' => now(),
            'description' => $validated['description'] ?? 'Closing year ' . $year,
        ]);

        Slot::create([
            'name' => 'date-posted',
            'obj_guid' => $transaction->guid,
            'slot_type' => 10,
            'int64_val' => 0,
            'gdate_val' => $validated['post_date'],
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);

        Slot::create([
            'name' => 'book_closing',
            'obj_guid' => $transaction->guid,
            'slot_type' => 1,
            'int64_val' => 1,
            'gdate_val' => null,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);

        $transaction->splits()->create([
            'account_guid' => $post_account->guid,
            'memo' => '',
            'action' => '',
            'reconcile_state' => 'n',
            'value_num' => $num,
            'value_denom' => $denom,
            'quantity_num' => $num,
            'quantity_denom' => $denom,
        ]);

        foreach ($accounts as $account ) {
            $transaction->splits()->create([
                'account_guid' => $account->guid,
                'memo' => '',
                'action' => '',
                'reconcile_state' => 'n',
                'value_num' => -$account->amount * $account->fraction,
                'value_denom' => $account->fraction,
                'quantity_num' => -$account->amount * $account->fraction,
                'quantity_denom' => $account->fraction,
            ]);
        }

        //********************************************
        // Handle expenses
        //********************************************

        $accounts = DB::connection('phmoney_portfolio')->table('splits')
            ->select(
                'accounts.guid',
                'accounts.name',
                DB::raw('sum(1.0*phmprt_splits.value_num/phmprt_splits.value_denom) as amount'),
                'transactions.post_date',
                'commodities.mnemonic',
                'commodities.fraction',
            )
            ->whereIn('accounts.account_type', Account::EXPENSES)
            ->where('splits.team_id', $request->user()->currentTeam->id)
            ->where('accounts.team_id', $request->user()->currentTeam->id)
            ->where('transactions.team_id', $request->user()->currentTeam->id)
            ->where('commodities.team_id', $request->user()->currentTeam->id)
            ->where('transactions.post_date', '>=', $date_start)
            ->where('transactions.post_date', '<=', $date_end)
            ->leftJoin('accounts', 'accounts.guid', '=', 'splits.account_guid')
            ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
            ->leftJoin('commodities', 'commodities.guid', '=', 'accounts.commodity_guid')
            ->groupBy('splits.account_guid')
            ->get();

        $post_account = Account::where('guid', $validated['income_account_guid'])->first();
        $denom = $post_account->commodity_scu;
        $num = $accounts->sum('amount') * $denom;

        $transaction = Transaction::create([
            'currency_guid' => $post_account->commodity_guid,
            'num' => '',
            'post_date' => $validated['post_date'],
            'enter_date' => now(),
            'description' => $validated['description'] ?? 'Closing year ' . $year,
        ]);

        Slot::create([
            'name' => 'date-posted',
            'obj_guid' => $transaction->guid,
            'slot_type' => 10,
            'int64_val' => 0,
            'gdate_val' => $validated['post_date'],
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);

        Slot::create([
            'name' => 'book_closing',
            'obj_guid' => $transaction->guid,
            'slot_type' => 1,
            'int64_val' => 1,
            'gdate_val' => null,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);

        $transaction->splits()->create([
            'account_guid' => $post_account->guid,
            'memo' => '',
            'action' => '',
            'reconcile_state' => 'n',
            'value_num' => $num,
            'value_denom' => $denom,
            'quantity_num' => $num,
            'quantity_denom' => $denom,
        ]);

        foreach ($accounts as $account ) {
            $transaction->splits()->create([
                'account_guid' => $account->guid,
                'memo' => '',
                'action' => '',
                'reconcile_state' => 'n',
                'value_num' => -$account->amount * $account->fraction,
                'value_denom' => $account->fraction,
                'quantity_num' => -$account->amount * $account->fraction,
                'quantity_denom' => $account->fraction,
            ]);
        }

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'book-closed');
    }
}
