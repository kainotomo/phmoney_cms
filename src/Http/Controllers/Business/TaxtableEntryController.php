<?php

namespace Kainotomo\PHMoney\Http\Controllers\Business;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Http\Requests\TaxtableEntryRequest;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Taxtable;
use Kainotomo\PHMoney\Models\TaxtableEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class TaxtableEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Kainotomo\PHMoney\Models\Taxtable $taxtable
     * @return \Illuminate\Http\Response
     */
    public function index(Taxtable $taxtable)
    {
        $query = $taxtable->entries()->select('pk', 'account');

        return Jetstream::inertia()->render(request(), 'Business/Taxtables/Entrys/Index', [
            'taxtableentrys' => $query->paginate(),
            'taxtable' => $taxtable,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Kainotomo\PHMoney\Models\Taxtable $taxtable
     * @return \Illuminate\Http\Response
     */
    public function create(Taxtable $taxtable)
    {
        $taxtableentry = new TaxtableEntry();
        return Jetstream::inertia()->render(request(), 'Business/Taxtables/Entrys/Create', [
            'taxtableentry' => $taxtableentry,
            'taxtable' => $taxtable,
            'accounts' => Account::getFlatList(),
            'type' => ['name' => "Percent %", 'value' => 1]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Kainotomo\PHMoney\Models\Taxtable $taxtable
     * @param  \Kainotomo\PHMoney\Http\Requests\TaxtableEntryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Taxtable $taxtable, TaxtableEntryRequest $request)
    {
        $taxtable->entries()->create($request->all());
        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'taxtableentry-created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Kainotomo\PHMoney\Models\TaxtableEntry  $taxtableentry
     * @param Kainotomo\PHMoney\Models\Taxtable $taxtable
     * @return \Illuminate\Http\Response
     */
    public function show(TaxtableEntry $taxtableentry, Taxtable $taxtable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Kainotomo\PHMoney\Models\Taxtable $taxtable
     * @param  \Kainotomo\PHMoney\Models\TaxtableEntry  $taxtableentry
     * @return \Illuminate\Http\Response
     */
    public function edit(Taxtable $taxtable, TaxtableEntry $taxtableentry )
    {
        switch ($taxtableentry->type) {
            case 1:
                $type = ['name' => "Percent %", 'value' => 2];
                break;
            case 2:
                $type = ['name' => "Value €", 'value' => 1];
                break;

            default:
                $type = ['name' => "Percent %", 'value' => 2];
                break;
        }

        return Jetstream::inertia()->render(request(), 'Business/Taxtables/Entrys/Edit', [
            'taxtableentry' => $taxtableentry,
            'taxtable' => $taxtable,
            'accounts' => Account::getFlatList(),
            'type' => $type
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\TaxtableEntryRequest  $request
     * @param  \Kainotomo\PHMoney\Models\Taxtable  $taxtable
     * @param  \Kainotomo\PHMoney\Models\TaxtableEntry  $taxtableentry
     * @return \Illuminate\Http\Response
     */
    public function update(TaxtableEntryRequest $request, Taxtable $taxtable, TaxtableEntry $taxtableentry)
    {
        $taxtableentry->update($request->all());

        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'taxtableentry-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Kainotomo\PHMoney\Models\Taxtable  $taxtable
     * @param  \Kainotomo\PHMoney\Models\TaxtableEntry  $taxtableentry
     * @return \Illuminate\Http\Response
     */
    public function destroy(Taxtable $taxtable, TaxtableEntry $taxtableentry)
    {
        if ($taxtable->entries->count() == 1) {
            Validator::make([], [
                'delete_error' => ['required'],
            ], [
                'required' => "You cannot remove the last entry from the tax table. Try deleting the tax table if you want to do that.",
            ])->validateWithBag('taxtableentry-delete');
        }
        $taxtableentry->delete();

        return request()->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'taxtableentry-delete');
    }
}
