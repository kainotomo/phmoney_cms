<?php

namespace Kainotomo\PHMoney\Http\Controllers\Business;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Http\Requests\PaymentRequest;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Base;
use Kainotomo\PHMoney\Models\Invoice;
use Kainotomo\PHMoney\Models\Slot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Invoice::with('splits', 'type', 'due_date')->withCount(['splits as amount' => function ($query) {
            $query->select(DB::raw('sum(1.0*value_num/value_denom)'));
        }]);
        if ($request->search) {
            $query->where('id', 'LIKE', '%' . $request->search . '%');
            $query->orWhere('billing_id', 'LIKE', '%' . $request->search . '%');
            $query->orWhere(function ($query) use ($request) {
                $query->whereHas('customer', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->search . '%');
                });
            });
        }

        if ($request->invoice_type === 'Invoice') {
            $query->whereHas('customer', function ($query) {
                $query->havingRaw('COUNT(*) > ?', [0]);
            });
            $query->orWhere(function ($query) {
                $query->whereHas('job', function ($query) {
                    $query->where('owner_type', 2)
                    ->havingRaw('COUNT(*) > ?', [0]);
                });
            });
        }

        if ($request->invoice_type === 'Bill') {
            $query->whereHas('vendor', function ($query) {
                $query->havingRaw('COUNT(*) > ?', [0]);
            });
            $query->orWhere(function ($query) {
                $query->whereHas('job', function ($query) {
                    $query->where('owner_type', 4)
                    ->havingRaw('COUNT(*) > ?', [0]);
                });
            });
        }

        if ($request->invoice_type === 'Voucher') {
            $query->whereHas('employee', function ($query) {
                $query->havingRaw('COUNT(*) > ?', [0]);
            });
        }

        $query->where('active', true);
        $query->whereNotNull('date_posted');
        $query->whereHas('splits', function ($query) {
            $query->havingRaw('SUM(value_num) != ?', [0]);
        });

        return Jetstream::inertia()->render(request(), 'Business/Payment/Index', [
            'transfer_accounts' => Account::getFlatList()->whereIn('type', array_merge(Account::ASSETS, Account::LIABILITYS))->values(),
            'items' => $query->paginate(),
            'i_accounts' => Account::getFlatList()->whereIn('type', [Account::RECEIVABLE])->values(),
            'b_accounts' => Account::getFlatList()->whereIn('type', [Account::PAYABLE])->values(),
        ]);
    }

    /**
     * Post the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\PaymentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentRequest $request)
    {
        $validated = $request->validated();
        $invoice = Invoice::where('guid', $validated['item_guid'])->first();
        $post_account = Account::where('guid', $validated['post_account_guid'])->first();
        $lot = $invoice->lot;
        $transaction = $invoice->transaction()->create([
            'currency_guid' => $post_account->commodity_guid,
            'num' => $validated['num'] ?? '',
            'post_date' => $validated['post_date'],
            'enter_date' => now(),
            'description' => $invoice->auto_description,
        ]);

        $transfer_account = Account::where('guid', $validated['transfer_account_guid'])->first();
        $denom = $post_account->commodity_scu;
        $num = $validated['credit'] > 0 ? $validated['credit'] * $denom : -$validated['debit'] * $denom;
        $transaction->splits()->create([
            'account_guid' => $transfer_account->guid,
            'memo' => $validated['memo'] ?? '',
            'action' => 'Payment',
            'reconcile_state' => 'n',
            'value_num' => $num,
            'value_denom' => $denom,
            'quantity_num' => $num,
            'quantity_denom' => $denom,
        ]);
        $transaction->splits()->create([
            'account_guid' => $post_account->guid,
            'memo' => $validated['memo'] ?? '',
            'action' => 'Payment',
            'reconcile_state' => 'n',
            'value_num' => -$num,
            'value_denom' => $denom,
            'quantity_num' => -$num,
            'quantity_denom' => $denom,
            'lot_guid' => $lot->guid,
        ]);

        // Create slots
        $slot_owner = Slot::create([
            'name' => 'gncOwner/owner-guid',
            'obj_guid' => Base::uuid(),
            'slot_type' => 5,
            'int64_val' => 0,
            'guid_val' => $invoice->owner_guid,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);
        $slot_owner = Slot::create([
            'name' => 'gncOwner/owner-type',
            'obj_guid' => $slot_owner->obj_guid,
            'slot_type' => 1,
            'int64_val' => $invoice->owner_type,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);
        Slot::create([
            'name' => 'gncOwner',
            'obj_guid' => $transaction->guid,
            'slot_type' => 9,
            'int64_val' => 0,
            'guid_val' => $slot_owner->obj_guid,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);
        $invoice_slot = Slot::create([
            'name' => 'gncInvoice/invoice-guid',
            'obj_guid' => Base::uuid(),
            'slot_type' => 5,
            'int64_val' => 0,
            'guid_val' => $invoice->guid,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);
        Slot::create([
            'name' => 'gncInvoice',
            'obj_guid' => $lot->guid,
            'slot_type' => 9,
            'int64_val' => 0,
            'guid_val' => $invoice_slot->obj_guid,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'payment-posted');
    }
}
