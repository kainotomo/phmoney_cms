<?php

namespace Kainotomo\PHMoney\Http\Controllers\Business;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Http\Requests\VendorRequest;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Base;
use Kainotomo\PHMoney\Models\Billterm;
use Kainotomo\PHMoney\Models\Book;
use Kainotomo\PHMoney\Models\Commodity;
use Kainotomo\PHMoney\Models\Slot;
use Kainotomo\PHMoney\Models\Vendor;
use Kainotomo\PHMoney\Models\Taxtable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Vendor::select('pk', 'guid', 'name', 'id', 'addr_addr1', 'addr_addr2', 'addr_phone');
        if ($request->name) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->id) {
            $query->where('id', 'LIKE', '%' . $request->id . '%');
        }
        if ($request->addr_name) {
            $query->where('addr_name', 'LIKE', '%' . $request->addr_name . '%');
        }

        return Jetstream::inertia()->render(request(), 'Business/Vendors/Index', [
            'vendors' => $query->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $vendor = new Vendor();
        $book = Book::with('root_account')->first();
        $parent_account = Account::where(['guid' => $book->root_account_guid])->first();
        $vendor->commodity = $parent_account->commodity;
        $vendor->currency = $parent_account->commodity_guid;
        $vendor->active = true;
        return Jetstream::inertia()->render(request(), 'Business/Vendors/Create', [
            'vendor' => $vendor,
            'billterms' => Billterm::all(),
            'taxtables' => Taxtable::all(),
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'tax_inc' => ['name' => "Use Global", 'value' => 3]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\VendorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VendorRequest $request)
    {
        // set vendor number
        if (!$request->id) {
            $counter = Slot::where([
                'name' => 'counters/gncVendor',
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
                    'name' => 'counters/gncVendor',
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

        Vendor::create($request->all());
        return Redirect::route('business.vendors');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        switch ($vendor->tax_inc) {
            case 1:
                $tax_inc = ['name' => "Yes", 'value' => 1];
                break;
            case 2:
                $tax_inc = ['name' => "No", 'value' => 2];
                break;

            default:
                $tax_inc = ['name' => "Use Global", 'value' => 3];
                break;
        }
        return Jetstream::inertia()->render(request(), 'Business/Vendors/Edit', [
            'vendor' => $vendor,
            'billterms' => Billterm::all(),
            'taxtables' => Taxtable::all(),
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'tax_inc' => $tax_inc
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\VendorRequest  $request
     * @param  \Kainotomo\PHMoney\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(VendorRequest $request, Vendor $vendor)
    {
        // set vendor number
        if (!$request->id) {
            $counter = Slot::where([
                'name' => 'counters/gncVendor',
                'slot_type' => 1
            ])->first();
            $counter->increment('int64_val');
            $invoice_number = str_pad((string) $counter->int64_val, 6, "0", STR_PAD_LEFT);
            $request->merge(['id' => $invoice_number]);
        }

        $vendor->update($request->all());

        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'vendor-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Kainotomo\PHMoney\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return request()->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'vendor-delete');
    }
}
