<?php

namespace Kainotomo\PHMoney\Http\Controllers\Business;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Http\Requests\CustomerRequest;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Billterm;
use Kainotomo\PHMoney\Models\Book;
use Kainotomo\PHMoney\Models\Commodity;
use Kainotomo\PHMoney\Models\Customer;
use Kainotomo\PHMoney\Models\Taxtable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Customer::select('pk', 'guid', 'name', 'id', 'addr_addr1', 'addr_addr2', 'addr_phone');

        if ($request->name) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->id) {
            $query->where('id', 'LIKE', '%' . $request->id . '%');
        }
        if ($request->addr_name) {
            $query->where('addr_name', 'LIKE', '%' . $request->addr_name . '%');
        }
        if ($request->shipaddr_name) {
            $query->where('shipaddr_name', 'LIKE', '%' . $request->shipaddr_name . '%');
        }

        return Jetstream::inertia()->render(request(), 'Business/Customers/Index', [
            'customers' => $query->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customer = new Customer();
        $book = Book::with('root_account')->first();
        $parent_account = Account::where(['guid' => $book->root_account_guid])->first();
        $customer->commodity = $parent_account->commodity;
        $customer->currency = $parent_account->commodity_guid;
        $customer->active = true;
        return Jetstream::inertia()->render(request(), 'Business/Customers/Create', [
            'customer' => $customer,
            'billterms' => Billterm::all(),
            'taxtables' => Taxtable::all(),
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'tax_included' => ['name' => "Use Global", 'value' => 3]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\CustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        Customer::create($request->all());
        return Redirect::route('business.customers');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        switch ($customer->tax_included) {
            case 1:
                $tax_included = ['name' => "Yes", 'value' => 1];
                break;
            case 2:
                $tax_included = ['name' => "No", 'value' => 2];
                break;

            default:
                $tax_included = ['name' => "Use Global", 'value' => 3];
                break;
        }
        return Jetstream::inertia()->render(request(), 'Business/Customers/Edit', [
            'customer' => $customer,
            'billterms' => Billterm::all(),
            'taxtables' => Taxtable::all(),
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'tax_included' => $tax_included
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\CustomerRequest  $request
     * @param  \Kainotomo\PHMoney\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->all());

        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'customer-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Kainotomo\PHMoney\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return request()->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'customer-delete');
    }
}
