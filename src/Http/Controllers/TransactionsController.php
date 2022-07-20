<?php

namespace Kainotomo\PHMoney\Http\Controllers;

use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Base;
use Kainotomo\PHMoney\Models\Split;
use Kainotomo\PHMoney\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;
use Kainotomo\PHMoney\Http\Requests\SplitRequest;
use Kainotomo\PHMoney\Http\Requests\TransactionRequest;
use Kainotomo\PHMoney\Rules\SplitsTotal;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Kainotomo\PHMoney\Models\Account $account
     * @return \Inertia\Response
     */
    public function index(Request $request, Account $account)
    {
        $accounts = Account::getFlatList();

        $splits = Split::getForAccount($account, $request);

        return Jetstream::inertia()->render(request(), 'Transactions/Index', [
            'account' => $account,
            'accounts' => $accounts,
            'splits' => $splits,
            'new_guid' => Base::uuid(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Kainotomo\PHMoney\Models\Account $account
     * @return \Inertia\Response
     */
    public function create(Account $account)
    {
        $transaction = new Transaction([
            'currency_guid' => $account->commodity_guid,
            'post_date' => now()->format('Y-m-d'),
            'enter_date' => now()->format('Y-m-d'),
        ]);
        $transaction->guid = Base::uuid();

        $splits = collect();

        $split = new Split([
            'account_guid' => $account->guid,
            'action' => null,
            'tx_guid' => $transaction->guid,
            'value_num' => 0,
            'value_denom' => $account->commodity_scu,
            'quantity_num' => 0,
            'quantity_denom' => $account->commodity_scu,
            'reconcile_date' => null,
            'reconcile_state' => 'n',
            'lot_guid' => null,
            'memo' => null,
        ]);
        $split->account = $account;
        $split->guid = Base::uuid();
        $split->transaction = $transaction;
        $split->credit = null;
        $split->debit = null;
        $split->precision = 2;
        $split->amount = 0;
        $split->shares = 0;
        $split->precision_shares = 4;
        $split->price = 0;
        $split->error_message = null;
        $splits[] = $split;

        $split = new Split([
            'account_guid' => null,
            'action' => null,
            'tx_guid' => $transaction->guid,
            'value_num' => 0,
            'value_denom' => $account->commodity_scu,
            'quantity_num' => 0,
            'quantity_denom' => $account->commodity_scu,
            'reconcile_date' => null,
            'reconcile_state' => 'n',
            'lot_guid' => null,
            'memo' => null,
        ]);
        $split->account = null;
        $split->guid = Base::uuid();
        $split->transaction = $transaction;
        $split->credit = null;
        $split->debit = null;
        $split->precision = 2;
        $split->amount = 0;
        $split->shares = 0;
        $split->precision_shares = 4;
        $split->price = 0;
        $split->error_message = null;
        $splits[] = $split;

        $split = new Split([
            'account_guid' => null,
            'action' => null,
            'tx_guid' => $transaction->guid,
            'value_num' => 0,
            'value_denom' => $account->commodity_scu,
            'quantity_num' => 0,
            'quantity_denom' => $account->commodity_scu,
            'reconcile_date' => null,
            'reconcile_state' => 'n',
            'lot_guid' => null,
            'memo' => null,
        ]);
        $split->account = null;
        $split->guid = Base::uuid();
        $split->transaction = $transaction;
        $split->credit = null;
        $split->debit = null;
        $split->precision = 2;
        $split->amount = 0;
        $split->shares = 0;
        $split->precision_shares = 4;
        $split->price = 0;
        $split->error_message = null;

        return Jetstream::inertia()->render(request(), 'Transactions/Create', [
            'account' => $account,
            'transaction' => $transaction,
            'splits' => $splits,
            'accounts' => Account::getFlatList(),
            'reconcile_states' => ["y", "c", "n"],
            'new_split' => $split,
            'submit_route' => "transactions.store",
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Kainotomo\PHMoney\Models\Account $account
     * @param  \Kainotomo\PHMoney\Http\Requests\SettingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionRequest $request, Account $account)
    {
        $validatedTransaction = $request->validated();
        $validatedSplits = [];
        foreach ($validatedTransaction['splits'] as $split) {
            $validator = new SplitRequest($split);
            $validatedSplits[] = $validator->validate($validator->rules());
        }

        Validator::make(['splits' => $validatedSplits], [
            'splits' => ['required', new SplitsTotal],
        ])->validate();

        $validatedTransaction['num'] = $validatedTransaction['num'] ?? '';
        $transaction = Transaction::create($validatedTransaction);
        foreach ($validatedSplits as $split) {
            $split['memo'] = $split['memo'] ?? '';
            $split['action'] = $split['action'] ?? '';
            $transaction->splits()->create($split);
        }

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'transaction-created');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Kainotomo\PHMoney\Models\Account $account
     * @param \Kainotomo\PHMoney\Models\Transaction $transaction
     * @return \Inertia\Response
     */
    public function edit(Account $account, Transaction $transaction)
    {

        $split = new Split([
            'account_guid' => null,
            'action' => null,
            'tx_guid' => $transaction->guid,
            'value_num' => 0,
            'value_denom' => $account->commodity_scu,
            'quantity_num' => 0,
            'quantity_denom' => $account->commodity_scu,
            'reconcile_date' => null,
            'reconcile_state' => 'n',
            'lot_guid' => null,
            'memo' => null,
        ]);
        $split->account = null;
        $split->guid = Base::uuid();
        $split->transaction = $transaction;
        $split->credit = null;
        $split->debit = null;
        $split->precision = 2;
        $split->amount = 0;
        $split->shares = 0;
        $split->precision_shares = 4;
        $split->price = 0;
        $split->error_message = null;

        return Jetstream::inertia()->render(request(), 'Transactions/Edit', [
            'account' => $account,
            'transaction' => $transaction,
            'splits' => Split::getForTransaction($transaction),
            'accounts' => Account::getFlatList(),
            'reconcile_states' => ["y", "c", "n"],
            'new_split' => $split,
            'submit_route' => "transactions.update",
        ]);
    }

    /**
     * Duplicate a resource.
     *
     * @param \Kainotomo\PHMoney\Models\Account $account
     * @param \Kainotomo\PHMoney\Models\Transaction $transaction
     * @return \Inertia\Response
     */
    public function duplicate(Account $account, Transaction $transaction)
    {
        $transaction_new = $transaction->replicate()->fill([
            'guid' => Base::uuid(),
        ]);
        $transaction_new->save();

        foreach ($transaction->splits as $split) {
            $split_new = $split->replicate()->fill([
                'tx_guid' => $transaction_new->guid,
                'guid' => Base::uuid(),
            ]);
            $split_new->save();
        }

        $splits = Split::getForAccount($account, new Request());

        return Jetstream::inertia()->render(request(), 'Transactions/Index', [
            'account' => $account,
            'accounts' => Account::getFlatList(),
            'splits' => $splits,
            'new_guid' => Base::uuid(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Kainotomo\PHMoney\Http\Requests\TransactionRequest  $request
     * @param \Kainotomo\PHMoney\Models\Account $account
     * @param \Kainotomo\PHMoney\Models\Transaction $transaction
     * @return \Inertia\Response
     */
    public function update(TransactionRequest $request, Account $account, Transaction $transaction)
    {
        $validatedTransaction = $request->validated();
        $validatedSplits = [];
        foreach ($validatedTransaction['splits'] as $split) {
            $validator = new SplitRequest($split);
            $validatedSplits[] = $validator->validate($validator->rules());
        }

        Validator::make(['splits' => $validatedSplits], [
            'splits' => ['required', new SplitsTotal],
        ])->validate();

        $validatedTransaction['num'] = $validatedTransaction['num'] ?? '';
        $transaction->update($validatedTransaction);

        $split_guids = (collect($validatedSplits))->pluck('guid');
        $transaction->splits()->whereNotIn('guid', $split_guids)->delete();

        foreach ($validatedSplits as $split) {
            $split['memo'] = $split['memo'] ?? '';
            $split['action'] = $split['action'] ?? '';
            $transaction->splits()->updateOrCreate(
                ['guid' => $split['guid']],
                $split
            );
        }

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'transaction-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Kainotomo\PHMoney\Models\Account $account
     * @param \Kainotomo\PHMoney\Models\Transaction $transaction
     * @return \Inertia\Response
     */
    public function destroy(Account $account, Transaction $transaction)
    {
        $transaction->splits()->delete();
        $transaction->delete();

        return request()->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'transaction-delete');
    }
}
