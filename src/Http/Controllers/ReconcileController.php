<?php

namespace Kainotomo\PHMoney\Http\Controllers;

use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Split;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class ReconcileController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Kainotomo\PHMoney\Models\Account  $account
     * @return \Inertia\Response
     */
    public function index(Request $request, Account $account)
    {
        $date_statement = $request->date_statement ? Carbon::parse($request->date_statement)->endOfDay() : now()->endOfDay();
        $starting_balance = (float) Account::where('guid', $account->guid)
            ->withCount(['splits as starting_balance' => function ($query) {
                $query->select(DB::raw('sum(1.0*value_num/value_denom)'))->where('reconcile_state', 'y');
            }])->first()->starting_balance;

        $ending_balance = (float) Account::where('guid', $account->guid)
            ->withCount(['splits as starting_balance' => function ($query) use ($date_statement) {
                $query->select(DB::raw('sum(1.0*value_num/value_denom)'))
                    ->whereHas('transaction', function ($query) use ($date_statement) {
                        $query->where('post_date', '<=', $date_statement);
                    });
            }])->first()->starting_balance;

        $credits = $account->splits()->with('transaction', 'account')->select(DB::raw('guid, account_guid, tx_guid, (value_num/value_denom) as amount'))
            ->where('reconcile_state', '<>', 'y')
            ->whereHas('transaction', function ($query) use ($date_statement) {
                $query->where('post_date', '<=', $date_statement);
            })->where('value_num', '>', 0)->get();

        $debits = $account->splits()->with('transaction', 'account')->select(DB::raw('guid, account_guid, tx_guid, (value_num/value_denom) as amount'))
            ->where('reconcile_state', '<>', 'y')
            ->whereHas('transaction', function ($query) use ($date_statement) {
                $query->where('post_date', '<=', $date_statement);
            })->where('value_num', '<', 0)->get();

        return Jetstream::inertia()->render(request(), 'Reconcile/Index', [
            'account' => $account,
            'starting_balance' => $starting_balance,
            'ending_balance' => $ending_balance,
            'credits' => $credits,
            'debits' => $debits,
        ]);
    }

    /**
     * Save transactions
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Kainotomo\PHMoney\Models\Account  $account
     * @return \Inertia\Response
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'guids' => ['required', 'array']
        ]);

        Split::whereIn('guid', $validated['guids'])->update(['reconcile_state' => 'y']);

        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'splits-updated');
    }
}
