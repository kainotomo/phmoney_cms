<?php

namespace Kainotomo\PHMoney\Http\Controllers\Business;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Http\Requests\JobRequest;
use Kainotomo\PHMoney\Models\Base;
use Kainotomo\PHMoney\Models\Book;
use Kainotomo\PHMoney\Models\Customer;
use Kainotomo\PHMoney\Models\Job;
use Kainotomo\PHMoney\Models\Slot;
use Kainotomo\PHMoney\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Job::with('rate')->select('pk', 'guid', 'name', 'id', 'active', 'owner_guid');
        if ($request->name) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->id) {
            $query->where('id', 'LIKE', '%' . $request->id . '%');
        }
        if ($request->customer_name) {
            $query->where(function ($query) use ($request) {
                $query->whereHas('customer', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->customer_name . '%');
                });
            });
        }
        if ($request->vendor_name) {
            $query->where(function ($query) use ($request) {
                $query->whereHas('vendor', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->vendor_name . '%');
                });
            });
        }

        if ($request->only_active === 'true') {
            $query->where('active', true);
        }

        return Jetstream::inertia()->render(request(), 'Business/Jobs/Index', [
            'jobs' => $query->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $job = new Job();
        $job->active = true;
        $job->owner_type = 2;
        $job->rate = new Slot([
            'name' => 'job-rate',
            'slot_type' => 3,
            'numeric_val_num' => 0,
            'numeric_val_denom' => 1,
        ]);
        return Jetstream::inertia()->render(request(), 'Business/Jobs/Create', [
            'job' => $job,
            'customers' => Customer::select('guid', 'name')->get(),
            'vendors' => Vendor::select('guid', 'name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\JobRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JobRequest $request)
    {
        // set job number
        if (!$request->id) {
            $counter = Slot::where([
                'name' => 'counters/gncJob',
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
                    'name' => 'counters/gncJob',
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

        $job = Job::create($request->all());
        $job->rate()->create([
            'name' => 'job-rate',
            'slot_type' => 3,
            'int64_val' => 0,
            'string_val' => null,
            'double_val' => null,
            'guid_val' => null,
            'numeric_val_num' => $request->rate_num,
            'numeric_val_denom' => $request->rate_denom,
            'gdate_val' => null
        ]);
        return Redirect::route('business.jobs');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function show(Job $job)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function edit(Job $job)
    {
        $job->rate;
        return Jetstream::inertia()->render(request(), 'Business/Jobs/Edit', [
            'job' => $job,
            'customers' => Customer::select('guid', 'name')->get(),
            'vendors' => Vendor::select('guid', 'name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\JobRequest  $request
     * @param  \Kainotomo\PHMoney\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(JobRequest $request, Job $job)
    {
        // set job number
        if (!$request->id) {
            $counter = Slot::where([
                'name' => 'counters/gncJob',
                'slot_type' => 1
            ])->first();
            $counter->increment('int64_val');
            $invoice_number = str_pad((string) $counter->int64_val, 6, "0", STR_PAD_LEFT);
            $request->merge(['id' => $invoice_number]);
        }

        $job->update($request->all());
        $job->rate->update([
            'numeric_val_num' => $request->rate_num,
            'numeric_val_denom' => $request->rate_denom,
        ]);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'job-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Kainotomo\PHMoney\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        $job->rate->delete();
        $job->delete();

        return request()->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'job-delete');
    }
}
