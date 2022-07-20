<?php

namespace Kainotomo\PHMoney\Http\Controllers\Business;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Http\Requests\EntryRequest;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Entry;
use Kainotomo\PHMoney\Models\Invoice;
use Kainotomo\PHMoney\Models\Taxtable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @param Kainotomo\PHMoney\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Invoice $invoice)
    {
        $invoice->billterm;
        $invoice->paid = $invoice->splits()->havingRaw('SUM(value_num) = ?', [0])->count() === 0 ? false : true;
        $invoice_entrys = $invoice->invoice_entrys()->with('invoice_account', 'invoice_taxtable')->paginate();
        $bill_entrys = $invoice->bill_entrys()->with('bill_account', 'bill_taxtable')->paginate();
        return Jetstream::inertia()->render(request(), 'Business/Invoices/Entrys/Index', [
            'invoice' => $invoice,
            'entrys' => $invoice_entrys->total() ? $invoice_entrys : $bill_entrys,
            'discount_types' => [
                0 => [
                    'value' => 'VALUE',
                    'name' => '€',
                    'description' => 'Monetary Value'
                ],
                1 => [
                    'value' => 'PERCENT',
                    'name' => '%',
                    'description' => 'Percentage'
                ],
            ],
            'discount_hows' => [
                0 => [
                    'value' => 'PRETAX',
                    'name' => '<',
                    'description' => 'Tax computed after discount is applied'
                ],
                1 => [
                    'value' => 'SAMETIME',
                    'name' => '=',
                    'description' => 'Discount and tax both applied on pretax value'
                ],
                2 => [
                    'value' => 'POSTTAX',
                    'name' => '>',
                    'description' => 'Discount computed after tax is applied'
                ],
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Kainotomo\PHMoney\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function create(Invoice $invoice)
    {
        $entry = new Entry([
            'date' => now()->format('Y-m-d'),
            'date_entered' => now()->format('Y-m-d'),
            'action' => null,
            'i_taxtable' => null,
            'i_taxable' => false,
            'i_taxincluded' => false,
            'quantity_num' => 1000000000,
            'quantity_denom' => 1000000000,
            'i_price_num' => 0,
            'i_price_denom' => 1000000000,
            'i_discount_num' => 0,
            'i_discount_denom' => 1000000000,
            'i_disc_type' => 'PERCENT',
            'i_disc_how' => 'PRETAX',
            'b_taxtable' => null,
            'b_taxable' => false,
            'b_taxincluded' => false,
            'b_price_num' => 0,
            'b_price_denom' => 1000000000,
            'billable' => false,
        ]);
        $entry->invoice_account = null;
        $entry->bill_account = null;
        return Jetstream::inertia()->render(request(), 'Business/Invoices/Entrys/Create', [
            'invoice' => $invoice,
            'entry' => $entry,
            'actions' => ['Hours', 'Material', 'Project'],
            'i_accounts' => Account::getFlatList()->whereIn('type', [Account::INCOME, Account::CREDIT])->values(),
            'b_accounts' => Account::getFlatList()->whereIn('type', [Account::EXPENSE])->values(),
            'taxtables' => Taxtable::get(),
            'discount_types' => [
                0 => [
                    'value' => 'VALUE',
                    'name' => '€',
                    'description' => 'Monetary Value'
                ],
                1 => [
                    'value' => 'PERCENT',
                    'name' => '%',
                    'description' => 'Percentage'
                ],
            ],
            'discount_hows' => [
                0 => [
                    'value' => 'PRETAX',
                    'name' => '<',
                    'description' => 'Tax computed after discount is applied'
                ],
                1 => [
                    'value' => 'SAMETIME',
                    'name' => '=',
                    'description' => 'Discount and tax both applied on pretax value'
                ],
                2 => [
                    'value' => 'POSTTAX',
                    'name' => '>',
                    'description' => 'Discount computed after tax is applied'
                ],
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\EntryRequest  $request
     * @param Kainotomo\PHMoney\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function store(EntryRequest $request, Invoice $invoice)
    {
        if ($request->i_acct) {
            $invoice->invoice_entrys()->create($request->validated());
        }

        if ($request->b_acct) {
            $invoice->bill_entrys()->create($request->validated());
        }

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'entry-created');
    }

    /**
     * Display the specified resource.
     *
     * @param Kainotomo\PHMoney\Models\Invoice $invoice
     * @param  \Kainotomo\PHMoney\Models\Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice, Entry $entry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Kainotomo\PHMoney\Models\Invoice $invoice
     * @param  \Kainotomo\PHMoney\Models\Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice, Entry $entry)
    {
        $entry->invoice_account;
        $entry->bill_account;
        return Jetstream::inertia()->render(request(), 'Business/Invoices/Entrys/Edit', [
            'invoice' => $invoice,
            'entry' => $entry,
            'actions' => ['Hours', 'Material', 'Project'],
            'i_accounts' => Account::getFlatList()->whereIn('type', [Account::INCOME, Account::CREDIT])->values(),
            'b_accounts' => Account::getFlatList()->whereIn('type', [Account::EXPENSE])->values(),
            'taxtables' => Taxtable::get(),
            'discount_types' => [
                0 => [
                    'value' => 'VALUE',
                    'name' => '€',
                    'description' => 'Monetary Value'
                ],
                1 => [
                    'value' => 'PERCENT',
                    'name' => '%',
                    'description' => 'Percentage'
                ],
            ],
            'discount_hows' => [
                0 => [
                    'value' => 'PRETAX',
                    'name' => '<',
                    'description' => 'Tax computed after discount is applied'
                ],
                1 => [
                    'value' => 'SAMETIME',
                    'name' => '=',
                    'description' => 'Discount and tax both applied on pretax value'
                ],
                2 => [
                    'value' => 'POSTTAX',
                    'name' => '>',
                    'description' => 'Discount computed after tax is applied'
                ],
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\EntryRequest  $request
     * @param Kainotomo\PHMoney\Models\Invoice $invoice
     * @param  \Kainotomo\PHMoney\Models\Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function update(EntryRequest $request, Invoice $invoice, Entry $entry)
    {
        $entry->update($request->validated());

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'entry-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Kainotomo\PHMoney\Models\Invoice $invoice
     * @param  \Kainotomo\PHMoney\Models\Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice, Entry $entry)
    {
        $entry->delete();

        return request()->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'entry-delete');
    }
}
