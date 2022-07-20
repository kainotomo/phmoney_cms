<?php

namespace Kainotomo\PHMoney\Http\Controllers\Business;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Http\Requests\EmployeeRequest;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Base;
use Kainotomo\PHMoney\Models\Billterm;
use Kainotomo\PHMoney\Models\Book;
use Kainotomo\PHMoney\Models\Commodity;
use Kainotomo\PHMoney\Models\Employee;
use Kainotomo\PHMoney\Models\Slot;
use Kainotomo\PHMoney\Models\Taxtable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Employee::select('pk', 'guid', 'addr_name', 'id', 'addr_addr1', 'addr_addr2', 'addr_phone');

        if ($request->username) {
            $query->where('username', 'LIKE', '%' . $request->username . '%');
        }
        if ($request->id) {
            $query->where('id', 'LIKE', '%' . $request->id . '%');
        }
        if ($request->addr_name) {
            $query->where('addr_name', 'LIKE', '%' . $request->addr_name . '%');
        }

        return Jetstream::inertia()->render(request(), 'Business/Employees/Index', [
            'employees' => $query->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employee = new Employee();
        $book = Book::with('root_account')->first();
        $parent_account = Account::where(['guid' => $book->root_account_guid])->first();
        $employee->commodity = $parent_account->commodity;
        $employee->currency = $parent_account->commodity_guid;
        $employee->active = true;

        return Jetstream::inertia()->render(request(), 'Business/Employees/Create', [
            'employee' => $employee,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList()->where('type', Account::CREDIT)->values(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\EmployeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        // set employee number
        if (!$request->id) {
            $counter = Slot::where([
                'name' => 'counters/gncEmployee',
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
                    'name' => 'counters/gncEmployee',
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

        Employee::create($request->all());
        return Redirect::route('business.employees');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        return Jetstream::inertia()->render(request(), 'Business/Employees/Edit', [
            'employee' => $employee,
            'currencies' => Commodity::where('namespace', Commodity::CURRENCY)->get(),
            'accounts' => Account::getFlatList()->where('type', Account::CREDIT)->values(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\EmployeeRequest  $request
     * @param  \Kainotomo\PHMoney\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        // set employee number
        if (!$request->id) {
            $counter = Slot::where([
                'name' => 'counters/gncEmployee',
                'slot_type' => 1
            ])->first();
            $counter->increment('int64_val');
            $invoice_number = str_pad((string) $counter->int64_val, 6, "0", STR_PAD_LEFT);
            $request->merge(['id' => $invoice_number]);
        }

        $employee->update($request->all());

        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'employee-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Kainotomo\PHMoney\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return request()->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'employee-delete');
    }
}
