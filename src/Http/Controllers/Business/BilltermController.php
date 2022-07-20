<?php

namespace Kainotomo\PHMoney\Http\Controllers\Business;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Models\Billterm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Providers\Jetstream\Jetstream;
use Kainotomo\PHMoney\Http\Requests\BilltermRequest;

class BilltermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Billterm::select('pk', 'guid', 'name');

        return Jetstream::inertia()->render(request(), 'Business/Billterms/Index', [
            'billterms' => $query->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $billterm = new Billterm();
        return Jetstream::inertia()->render(request(), 'Business/Billterms/Create', [
            'billterm' => $billterm,
            'type' => ['name' => "Days", 'value' => 'GNC_TERM_TYPE_DAYS'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\BilltermRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BilltermRequest $request)
    {
        Billterm::create($request->all());
        return Redirect::route('business.billterms');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Billterm  $billterm
     * @return \Illuminate\Http\Response
     */
    public function show(Billterm $billterm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Billterm  $billterm
     * @return \Illuminate\Http\Response
     */
    public function edit(Billterm $billterm)
    {
        switch ($billterm->type) {
            case 'GNC_TERM_TYPE_DAYS':
                $type = ['name' => "Days", 'value' => 'GNC_TERM_TYPE_DAYS'];
                break;
            case 'GNC_TERM_TYPE_PROXIMO':
                $type = ['name' => "Proximo", 'value' => 'GNC_TERM_TYPE_PROXIMO'];
                break;

            default:
            $type = ['name' => "Days", 'value' => 'GNC_TERM_TYPE_DAYS'];
                break;
        }

        return Jetstream::inertia()->render(request(), 'Business/Billterms/Edit', [
            'billterm' => $billterm,
            'type' => $type,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\BilltermRequest  $request
     * @param  \Kainotomo\PHMoney\Models\Billterm  $billterm
     * @return \Illuminate\Http\Response
     */
    public function update(BilltermRequest $request, Billterm $billterm)
    {
        $billterm->update($request->all());

        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'billterm-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Kainotomo\PHMoney\Models\Billterm  $billterm
     * @return \Illuminate\Http\Response
     */
    public function destroy(Billterm $billterm)
    {
        $billterm->delete();

        return request()->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'billterm-delete');
    }
}
