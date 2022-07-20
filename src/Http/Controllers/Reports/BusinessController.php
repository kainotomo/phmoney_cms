<?php

namespace Kainotomo\PHMoney\Http\Controllers\Reports;

use Kainotomo\PHMoney\Http\Controllers\ReportController;
use Kainotomo\PHMoney\Models\Commodity;
use Kainotomo\PHMoney\Models\Customer;
use Kainotomo\PHMoney\Models\Employee;
use Kainotomo\PHMoney\Models\Invoice;
use Kainotomo\PHMoney\Models\Job;
use Kainotomo\PHMoney\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;
use Kainotomo\PHMoney\Models\Setting;

class BusinessController extends ReportController
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function customer_report(Request $request)
    {
        $date_start = $this->getStartDate($request);
        $date_end = $this->getEndDate($request);

        $invoices = collect();
        if ($request->customer) {
            $customer = is_string($request->customer) ? json_decode($request->customer, true) : $request->customer;
            $guids = Job::where('owner_guid', $customer['guid'])->pluck('guid');
            $guids[] = $customer['guid'];
            $invoices = Invoice::with('splits', 'invoice_entrys', 'type', 'due_date')
                ->select('pk', 'guid', 'date_opened', 'id', 'notes', 'post_txn')
                ->where('active', true)
                ->whereIn('owner_guid', $guids)
                ->whereNotNull('post_acc')
                ->whereHas('invoice_entrys', function ($query) use ($date_start, $date_end) {
                    $query->where('date', '>=', $date_start);
                    $query->where('date', '<=', $date_end);
                })->get();

            $invoices = $invoices->transform(function ($invoice, $key) {
                $sale = $invoice->invoice_entrys->sum('sale');
                $tax = $invoice->invoice_entrys->sum('tax');
                return collect([
                    'pk' => $invoice->pk,
                    'guid' => $invoice->guid,
                    'date_opened' => $invoice->date_opened->toDateString(),
                    'due_date' => $invoice->due_date ? (new Carbon($invoice->due_date->timespec_val))->toDateString() : null,
                    'id' => $invoice->id,
                    'type' => $invoice->type->int64_val ? 'Credit Note' : 'Invoice',
                    'notes' => $invoice->notes,
                    'commodity' => $invoice->invoice_entrys->first()->invoice_account->commodity,
                    'sale' => $sale,
                    'tax' => $tax,
                    'debits' => $sale + $tax,
                    'credits' => null,
                ]);
            });

            $balance = 0;
            foreach ($invoices as $invoice) {
                $balance += $invoice['debits'];
                $invoice['balance'] = $balance;
            }
        }

        return Jetstream::inertia()->render(request(), 'Reports/Business/CustomerReport', [
            'print' => $request->print == 'true' ? true :  false,
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Customer Report",
            'company' => $request->company ?? null,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'currency' => Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'customers' => Customer::select('guid', 'name')->where('active', 1)->get(),
            'invoices' => $invoices,
            'totals' => [
                'sale' => $invoices->sum('sale'),
                'tax' => $invoices->sum('tax'),
                'debits' => $invoices->sum('debits'),
                'credits' => $invoices->sum('credits'),
                'balance' => $invoices->sum('debits'),
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function customer_summary(Request $request)
    {
        $date_start = $this->getStartDate($request);
        $date_end = $this->getEndDate($request);

        $invoices_all = Invoice::with('splits', 'invoice_entrys', 'type', 'due_date', 'customer', 'job')
            ->select('guid', 'date_opened', 'id', 'notes', 'post_txn', 'owner_guid')
            ->where('active', true)
            ->whereNotNull('post_acc')
            ->whereHas('invoice_entrys', function ($query) use ($date_start, $date_end) {
                $query->where('date', '>=', $date_start);
                $query->where('date', '<=', $date_end);
            })->get();


        $invoices = collect();
        $customers = Customer::with('jobs')->get();
        foreach ($customers as $customer) {
            $guids = array_merge($customer->jobs->pluck('guid')->toArray(), [$customer->guid]);
            $invoices_customer = $invoices_all->whereIn('owner_guid', $guids);
            if ($invoices_customer->count()) {
                $invoices_customer = $invoices_customer->transform(function ($invoice, $key) {
                    return collect([
                        'sale' => $invoice->invoice_entrys->sum('sale'),
                        'expense' => $invoice->invoice_entrys->sum('tax'),
                    ]);
                });
                $sales = $invoices_customer->sum('sale');
                $expenses = $invoices_customer->sum('expense');
                $invoices[] = [
                    'guid' => $customer->guid,
                    'name' => $customer->name,
                    'sales' => $sales,
                    'expenses' => $expenses,
                    'profits' => $sales + $expenses,
                    'markup' => round($expenses / $sales * 100) + 100,
                ];
            }
        }

        return Jetstream::inertia()->render(request(), 'Reports/Business/CustomerSummary', [
            'print' => $request->print == 'true' ? true :  false,
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Customer Summary",
            'company' => $request->company ?? null,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'currency' => Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'invoices' => $invoices,
            'totals' => [
                'sales' => $invoices->sum('sales'),
                'expenses' => $invoices->sum('expenses'),
                'profits' => $invoices->sum('profits'),
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function employee_report(Request $request)
    {
        $date_start = $this->getStartDate($request);
        $date_end = $this->getEndDate($request);

        $bills = collect();
        if ($request->employee) {
            $employee = is_string($request->employee) ? json_decode($request->employee, true) : $request->employee;
            $guids = Job::where('owner_guid', $employee['guid'])->pluck('guid');
            $guids[] = $employee['guid'];
            $bills = Invoice::with('splits', 'bill_entrys', 'type', 'due_date')
                ->select('pk', 'guid', 'date_opened', 'id', 'notes', 'post_txn')
                ->where('active', true)
                ->whereIn('owner_guid', $guids)
                ->whereNotNull('post_acc')
                ->whereHas('bill_entrys', function ($query) use ($date_start, $date_end) {
                    $query->where('date', '>=', $date_start);
                    $query->where('date', '<=', $date_end);
                })->get();

            $bills = $bills->transform(function ($bill, $key) {
                $sale = $bill->bill_entrys->sum('sale');
                $tax = $bill->bill_entrys->sum('tax');
                return collect([
                    'pk' => $bill->pk,
                    'guid' => $bill->guid,
                    'date_opened' => $bill->date_opened->toDateString(),
                    'due_date' => $bill->due_date ? (new Carbon($bill->due_date->timespec_val))->toDateString() : null,
                    'id' => $bill->id,
                    'type' => $bill->type->int64_val ? 'Credit Note' : 'Invoice',
                    'notes' => $bill->notes,
                    'commodity' => $bill->bill_entrys->first()->bill_account->commodity,
                    'sale' => -$sale,
                    'tax' => $tax,
                    'debits' => null,
                    'credits' => -$sale + $tax,
                ]);
            });

            $balance = 0;
            foreach ($bills as $bill) {
                $balance += $bill['debits'];
                $bill['balance'] = $balance;
            }
        }

        return Jetstream::inertia()->render(request(), 'Reports/Business/EmployeeReport', [
            'print' => $request->print == 'true' ? true :  false,
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Employee Report",
            'company' => $request->company ?? null,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'currency' => Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'employees' => Employee::select('pk', 'guid', 'username')->where('active', 1)->get(),
            'bills' => $bills,
            'totals' => [
                'sale' => $bills->sum('sale'),
                'tax' => $bills->sum('tax'),
                'debits' => $bills->sum('debits'),
                'credits' => $bills->sum('credits'),
                'balance' => $bills->sum('debits'),
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setting_id
     * @return \Inertia\Response
     */
    public function vendor_report(Request $request)
    {
        $date_start = $this->getStartDate($request);
        $date_end = $this->getEndDate($request);

        $bills = collect();
        if ($request->vendor) {
            $vendor = is_string($request->vendor) ? json_decode($request->vendor, true) : $request->vendor;
            $guids = Job::where('owner_guid', $vendor['guid'])->pluck('guid');
            $guids[] = $vendor['guid'];
            $bills = Invoice::with('splits', 'bill_entrys', 'type', 'due_date')
                ->select('pk', 'guid', 'date_opened', 'id', 'notes', 'post_txn')
                ->where('active', true)
                ->whereIn('owner_guid', $guids)
                ->whereNotNull('post_acc')
                ->whereHas('bill_entrys', function ($query) use ($date_start, $date_end) {
                    $query->where('date', '>=', $date_start);
                    $query->where('date', '<=', $date_end);
                })->get();

            $bills = $bills->transform(function ($bill, $key) {
                $sale = $bill->bill_entrys->sum('sale');
                $tax = $bill->bill_entrys->sum('tax');
                return collect([
                    'pk' => $bill->pk,
                    'guid' => $bill->guid,
                    'date_opened' => $bill->date_opened->toDateString(),
                    'due_date' => $bill->due_date ? (new Carbon($bill->due_date->timespec_val))->toDateString() : null,
                    'id' => $bill->id,
                    'type' => $bill->type->int64_val ? 'Credit Note' : 'Invoice',
                    'notes' => $bill->notes,
                    'commodity' => $bill->bill_entrys->first()->bill_account->commodity,
                    'sale' => -$sale,
                    'tax' => -$tax,
                    'debits' => null,
                    'credits' => -$sale - $tax,
                ]);
            });

            $balance = 0;
            foreach ($bills as $bill) {
                $balance += $bill['debits'];
                $bill['balance'] = $balance;
            }
        }

        return Jetstream::inertia()->render(request(), 'Reports/Business/VendorReport', [
            'print' => $request->print == 'true' ? true :  false,
            'settings' => Setting::where('type', $request->decodedPath())->get(),
            'type' => $request->decodedPath(),
            'title' => $request->title ?? "Vendor Report",
            'company' => $request->company ?? null,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'currency' => Commodity::where('namespace', Commodity::CURRENCY)->first(),
            'vendors' => Vendor::select('guid', 'name')->where('active', 1)->get(),
            'bills' => $bills,
            'totals' => [
                'sale' => $bills->sum('sale'),
                'tax' => $bills->sum('tax'),
                'debits' => $bills->sum('debits'),
                'credits' => $bills->sum('credits'),
                'balance' => $bills->sum('debits'),
            ],
        ]);
    }
}
