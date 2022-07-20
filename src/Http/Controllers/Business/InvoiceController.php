<?php

namespace Kainotomo\PHMoney\Http\Controllers\Business;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Http\Requests\InvoicePostRequest;
use Kainotomo\PHMoney\Http\Requests\InvoiceRequest;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Base;
use Kainotomo\PHMoney\Models\Billterm;
use Kainotomo\PHMoney\Models\Book;
use Kainotomo\PHMoney\Models\Customer;
use Kainotomo\PHMoney\Models\Employee;
use Kainotomo\PHMoney\Models\Invoice;
use Kainotomo\PHMoney\Models\Job;
use Kainotomo\PHMoney\Models\Slot;
use Kainotomo\PHMoney\Models\Split;
use Kainotomo\PHMoney\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Invoice::with('splits', 'type', 'due_date')->withSum('splits as total_splits', 'value_num');
        if ($request->id) {
            $query->where('id', 'LIKE', '%' . $request->id . '%');
        }
        if ($request->billing_id) {
            $query->where('billing_id', 'LIKE', '%' . $request->billing_id . '%');
        }

        switch ($request->invoice_type) {
            case 'Invoice':
                $ownerType = 'customer';
                $ownerName = 'name';
                break;
            case 'Bill':
                $ownerType = 'vendor';
                $ownerName = 'name';
                break;
            case 'Voucher':
                $ownerType = 'employee';
                $ownerName = 'username';
                break;
        }
        if ($request->owner_name) {
            $query->where(function ($query) use ($request, $ownerType, $ownerName) {
                $query->whereHas($ownerType, function ($query) use ($request, $ownerName) {
                    $query->where($ownerName, 'LIKE', '%' . $request->search . '%');
                });
            });
        }

        if ($request->only_active === 'true') {
            $query->where('active', true);
        }

        if ($request->only_posted === 'true') {
            $query->whereNotNull('date_posted');
        }

        if ($request->only_paid === 'true') {
            $query->whereHas('splits', function ($query) {
                $query->havingRaw('SUM(value_num) = ?', [0]);
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

        return Jetstream::inertia()->render(request(), 'Business/Invoices/Index', [
            'invoices' => $query->paginate(),
        ]);
    }

    /**
     * Get jobs
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function jobs(Request $request) {
        return response([
            'jobs' => Job::select('guid', 'name', 'reference')->where('owner_guid', $request->owner_guid)->get(),
            'billto_jobs' => Job::select('guid', 'name', 'reference')->where('owner_guid', $request->billto_guid)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $invoice = new Invoice([
            'date_opened' => now()->format('Y-m-d'),
            'active' => true,
            'owner_type' => 2,
            'billto_type' => 2,
            'charge_amt_num' => 0,
            'charge_amt_denom' => 1,
        ]);
        $invoice->type = new Slot(['int64_val' => 0]);
        $invoice->customer = null;
        $invoice->vendor = null;
        $invoice->employee = null;
        $invoice->job = null;
        $invoice->billto_customer = null;
        $invoice->billto_job = null;
        return Jetstream::inertia()->render(request(), 'Business/Invoices/Create', [
            'invoice' => $invoice,
            'customers' => Customer::select('guid', 'name')->get(),
            'vendors' => Vendor::select('guid', 'name')->get(),
            'employees' => Employee::select('guid', 'username')->get(),
            'jobs' => Job::select('guid', 'name', 'reference')->where('owner_guid', $request->owner_guid)->get(),
            'billto_jobs' => Job::select('guid', 'name', 'reference')->where('owner_guid', $request->billto_guid)->get(),
            'billterms' => Billterm::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\InvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        // set invoice number
        if (!$request->id) {
            $counter = Slot::where([
                'name' => 'counters/gncInvoice',
                'slot_type' => 1
            ])->first();
            if (!$counter) {
                $obj = Slot::where([
                    'name' => 'counters',
                    'obj_guid' => Book::first()->guid,
                    'slot_type' => 9,
                ])->first();
                if (!$obj) {
                    $obj = Slot::create([
                        'name' => 'counters',
                        'obj_guid' => Book::first()->guid,
                        'slot_type' => 9,
                        'int64_val' => 0,
                        'guid_val' => Base::uuid(),
                        'numeric_val_num' => 0,
                        'numeric_val_denom' => 1,
                    ]);
                }
                $counter = Slot::create([
                    'name' => 'counters/gncInvoice',
                    'obj_guid' => $obj->guid_val,
                    'slot_type' => 1,
                    'int64_val' => 0,
                    'numeric_val_num' => 0,
                    'numeric_val_denom' => 1,
                ]);
            }
            $counter->increment('int64_val');
            $invoice_number = str_pad((string) $counter->int64_val, 6, "0", STR_PAD_LEFT);
            $request->merge(['id' => $invoice_number]);
        }

        // set currency
        if (!$request->currency) {
            $book = Book::with('root_account')->first();
            $parent_account = Account::where(['guid' => $book->root_account_guid])->first();
            $request->merge(['currency' => $parent_account->commodity_guid]);
        }

        // set active
        $request->merge(['active' => true]);
        $request->merge(['date_posted' => null]);

        $invoice = Invoice::create($request->all());
        $invoice->slots()->create([
            'name' => 'credit-note',
            'slot_type' => 1,
            'int64_val' => $request->type,
            'string_val' => null,
            'double_val' => null,
            'guid_val' => null,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
            'gdate_val' => null
        ]);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'invoice-created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Illuminate\Http\Request $request
     * @param  \Kainotomo\PHMoney\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Invoice $invoice)
    {
        $invoice->type;
        $invoice->billterm;
        $invoice->billto_customer;

        return Jetstream::inertia()->render(request(), 'Business/Invoices/Edit', [
            'invoice' => $invoice,
            'customers' => Customer::select('guid', 'name')->get(),
            'vendors' => Vendor::select('guid', 'name')->get(),
            'employees' => Employee::select('guid', 'username')->get(),
            'jobs' => $invoice->job ? Job::select('guid', 'name', 'reference')->where('owner_guid', $invoice->job->owner_guid)->get() : Job::select('guid', 'name', 'reference')->where('owner_guid', $invoice->owner_guid)->get(),
            'billto_jobs' => $invoice->billto_job ? Job::select('guid', 'name', 'reference')->where('owner_guid', $invoice->billto_job->owner_guid)->get() : Job::select('guid', 'name', 'reference')->where('owner_guid', $invoice->billto_guid)->get(),
            'billterms' => Billterm::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\InvoiceRequest  $request
     * @param  \Kainotomo\PHMoney\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        // set invoice number
        if (!$request->id) {
            $counter = Slot::where([
                'name' => 'counters/gncInvoice',
                'slot_type' => 1
            ])->first();
            $counter->increment('int64_val');
            $invoice_number = str_pad((string) $counter->int64_val, 6, "0", STR_PAD_LEFT);
            $request->merge(['id' => $invoice_number]);
        }

        // set currency
        if (!$request->currency) {
            $book = Book::with('root_account')->first();
            $parent_account = Account::where(['guid' => $book->root_account_guid])->first();
            $request->merge(['currency' => $parent_account->commodity_guid]);
        }

        $request->merge(['date_posted' => null]);

        $invoice->update($request->all());
        $invoice->type->update([
            'name' => 'credit-note',
            'slot_type' => 1,
            'int64_val' => $request->type,
            'string_val' => null,
            'double_val' => null,
            'guid_val' => null,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
            'gdate_val' => null
        ]);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'invoice-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Kainotomo\PHMoney\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->slots()->delete();
        $invoice->invoice_entrys()->delete();
        $invoice->bill_entrys()->delete();
        $invoice->lot()->delete();
        $invoice->delete();

        return request()->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'invoice-delete');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Illuminate\Http\Request $request
     * @param  \Kainotomo\PHMoney\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit_post(Request $request, Invoice $invoice)
    {
        $invoice->type;
        $invoice->billterm;
        $request->merge(['owner_guid' => $invoice->owner->guid]);

        $date_due = now()->endOfDay();
        if ($invoice->billterm) {
            $date_due->addDays($invoice->billterm->duedays);
        }

        return Jetstream::inertia()->render(request(), 'Business/Invoices/Post', [
            'invoice' => $invoice,
            'customers' => Customer::select('guid', 'name')->get(),
            'jobs' => Job::select('guid', 'name', 'reference')->where('owner_guid', $request->owner_guid)->get(),
            'billterms' => Billterm::all(),
            'date_due' => $date_due,
            'i_accounts' => Account::getFlatList()->whereIn('type', [Account::RECEIVABLE])->values(),
            'b_accounts' => Account::getFlatList()->whereIn('type', [Account::PAYABLE])->values(),
        ]);
    }

    /**
     * Post the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\InvoicePostRequest  $request
     * @param  \Kainotomo\PHMoney\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function post(InvoicePostRequest $request, Invoice $invoice)
    {
        $validated = $request->validated();
        $account = Account::where('guid', $validated['account_guid'])->first();

        $lot = $invoice->lot()->create([
            'account_guid' => $account->guid,
            'is_closed' => -1
        ]);
        $transaction = $invoice->transaction()->create([
            'currency_guid' => $account->commodity_guid,
            'num' => $invoice->id,
            'post_date' => $validated['date_posted'],
            'enter_date' => now(),
            'description' =>  $invoice->auto_description,
        ]);

        $splits = collect();
        foreach ($invoice->entrys as $entry) {
            $splits[] = new Split([
                'tx_guid' => $transaction->guid,
                'account_guid' => $entry->i_acct ?? $entry->b_acct,
                'memo' => $validated['description'] ?? '',
                'action' => 'invoice',
                'value_num' => $entry->total_num,
                'value_denom' => $entry->denom,
                'quantity_num' => $entry->total_num,
                'quantity_denom' => $entry->denom,
            ]);
            if ($entry->tax) {
                if ($entry->invoice_account) {
                    $entries = $entry->invoice_taxtable->entries;
                }
                if ($entry->bill_account) {
                    $entries = $entry->bill_taxtable->entries;
                }
                foreach ($entries as $taxtable_entry) {
                    $tax_num = ($taxtable_entry->amount_num / $taxtable_entry->amount_denom) * $taxtable_entry->tax_account->commodity_scu;
                    $splits[] = new Split([
                        'tx_guid' => $transaction->guid,
                        'account_guid' => $taxtable_entry->account,
                        'memo' => $validated['description'] ?? '',
                        'action' => 'invoice',
                        'value_num' => -$tax_num,
                        'value_denom' => $taxtable_entry->tax_account->commodity_scu,
                        'quantity_num' => -$tax_num,
                        'quantity_denom' => $taxtable_entry->tax_account->commodity_scu,
                    ]);
                }
            }
        }

        if ($validated['accumulate']) {
            $groups = $splits->groupBy('account_guid');
            $splits = collect();
            foreach ($groups as $account_guid => $group) {
                $sum = $group->sum('value_num');
                $splits[] = new Split([
                    'tx_guid' => $transaction->guid,
                    'account_guid' => $account_guid,
                    'memo' => $validated['description'] ?? '',
                    'action' => 'invoice',
                    'value_num' => $sum,
                    'value_denom' => $group[0]->value_denom,
                    'quantity_num' => $sum,
                    'quantity_denom' => $group[0]->quantity_denom,
                ]);
            }
        }

        $splits[] = new Split([
            'tx_guid' => $transaction->guid,
            'account_guid' => $validated['account_guid'],
            'memo' => $validated['description'] ?? '',
            'action' => 'invoice',
            'value_num' => -$splits->sum('value_num'),
            'value_denom' => $account->commodity_scu,
            'quantity_num' => -$splits->sum('quantity_num'),
            'quantity_denom' => $account->commodity_scu,
            'lot_guid' => $lot->guid
        ]);

        foreach ($splits as $split) {
            $split->save();
        }

        $invoice->update([
            'date_posted' => $validated['date_posted'],
            'post_txn' => $transaction->guid,
            'post_lot' => $lot->guid,
            'post_acc' => $validated['account_guid']
        ]);

        // Create slots
        Slot::create([
            'name' => 'date-posted',
            'obj_guid' => $transaction->guid,
            'slot_type' => 10,
            'int64_val' => 0,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
            'gdate_val' => Carbon::parse($transaction->post_date),
        ]);
        $slot_invoice = Slot::create([
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
            'obj_guid' => $transaction->guid,
            'slot_type' => 9,
            'int64_val' => 0,
            'guid_val' => $slot_invoice->obj_guid,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);
        Slot::create([
            'name' => 'trans-date-due',
            'obj_guid' => $transaction->guid,
            'slot_type' => 6,
            'int64_val' => 0,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
            'timespec_val' => Carbon::parse($invoice->date_opened)->addDays($invoice->billterm->duedays)->endOfDay(),
        ]);
        Slot::create([
            'name' => 'trans-read-only',
            'obj_guid' => $transaction->guid,
            'slot_type' => 4,
            'int64_val' => 0,
            'string_val' => "Generated from an invoice. Try unposting the invoice.",
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);
        Slot::create([
            'name' => 'trans-txn-type',
            'obj_guid' => $transaction->guid,
            'slot_type' => 4,
            'int64_val' => 0,
            'string_val' => "I",
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
        Slot::create([
            'name' => 'title',
            'obj_guid' => $lot->guid,
            'slot_type' => 4,
            'int64_val' => 0,
            'string_vale' => "Invoice " . $invoice->id,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'invoice-posted');
    }

    /**
     * Post the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\Request  $request
     * @param  \Kainotomo\PHMoney\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function unpost(Request $request, Invoice $invoice)
    {
        Slot::where('obj_guid', $invoice->lot->guid)->delete();
        Slot::where('guid_val', $invoice->guid)->delete();
        Slot::where('obj_guid', $invoice->transaction->guid)->delete();
        $invoice->transaction->splits()->delete();
        $invoice->transaction()->delete();
        $invoice->lot()->delete();
        Slot::where('obj_guid', $invoice->post_lot)->orWhere('obj_guid', $invoice->post_trx)
            ->orWhere('guid_val', $invoice->guid)->delete();
        $invoice->update([
            'date_posted' => null,
            'post_lot' => null,
            'post_txn' => null,
            'post_acc' => null,
        ]);


        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'invoice-unposted');
    }
}
