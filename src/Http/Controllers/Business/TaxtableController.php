<?php

namespace Kainotomo\PHMoney\Http\Controllers\Business;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Http\Requests\TaxtableRequest;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Taxtable;
use Kainotomo\PHMoney\Models\TaxtableEntry;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class TaxtableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Taxtable::select('pk', 'guid', 'name');

        return Jetstream::inertia()->render(request(), 'Business/Taxtables/Index', [
            'taxtables' => $query->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $taxtable = new Taxtable();
        $taxtableentry = new TaxtableEntry();
        return Jetstream::inertia()->render(request(), 'Business/Taxtables/Create', [
            'taxtable' => $taxtable,
            'taxtableentry' => $taxtableentry,
            'accounts' => Account::getFlatList(),
            'type' => ['name' => "Percent %", 'value' => 1]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\TaxtableRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaxtableRequest $request)
    {
        $taxtable = Taxtable::create($request->all());
        $taxtable->entries()->create($request->taxtableentry);
        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'taxtable-created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Taxtable  $taxtable
     * @return \Illuminate\Http\Response
     */
    public function show(Taxtable $taxtable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\Taxtable  $taxtable
     * @return \Illuminate\Http\Response
     */
    public function edit(Taxtable $taxtable)
    {
        return Jetstream::inertia()->render(request(), 'Business/Taxtables/Edit', [
            'taxtable' => $taxtable,
            'taxtableentry' => $taxtable->entries->first(),
            'accounts' => Account::getFlatList(),
            'type' => ['name' => "Percent %", 'value' => 1]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\TaxtableRequest  $request
     * @param  \Kainotomo\PHMoney\Models\Taxtable  $taxtable
     * @return \Illuminate\Http\Response
     */
    public function update(TaxtableRequest $request, Taxtable $taxtable)
    {
        $taxtable->update($request->all());

        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'taxtable-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Kainotomo\PHMoney\Models\Taxtable  $taxtable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Taxtable $taxtable)
    {
        $taxtable->entries()->delete();
        $taxtable->delete();

        return request()->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'taxtable-delete');
    }
}
