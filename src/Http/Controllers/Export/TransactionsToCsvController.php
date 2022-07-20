<?php

namespace Kainotomo\PHMoney\Http\Controllers\Export;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Kainotomo\PHMoney\Http\Requests\Export\DownloadRequest;
use Kainotomo\PHMoney\Models\Account;

class TransactionsToCsvController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string $account_guid
     * @return \Inertia\Response
     */
    public function index()
    {
        return Jetstream::inertia()->render(request(), 'Export/TransactionsToCsv/Page1', [
            'accounts' =>  Account::getFlatList(false, true)
        ]);
    }

    public function download(DownloadRequest $request)
    {
        $validated = $request->validated();
        $date_start = $this->getStartDate($request);
        $date_end = $this->getEndDate($request);

        $items = DB::connection('phmoney_portfolio')->table('splits')
            ->select(
                DB::raw('1.0*phmprt_splits.value_num/phmprt_splits.value_denom as amount'),
                'accounts.name',
                'accounts.code',
                'transactions.post_date',
                'commodities.mnemonic',
                'commodities.fraction',
            )
            ->whereIn('splits.account_guid', $validated['accounts'])
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

            $items = $items->transform(function ($item, $key) {
                return collect([
                    'amount' => $item->amount,
                    'fraction' => $item->fraction,
                    'mnemonic' => $item->mnemonic,
                    'account_name' => $item->name,
                    'acount_code' => $item->code,
                    'post_date' => $item->post_date,
                ]);
            });

            return response()->streamDownload(function () use ($items) {
                echo $items->toInlineCsv(['amount', 'fraction', 'mnemonic', 'account_name', 'acount_code', 'post_date']);
            }, 'Transactions.csv');

    }
}
