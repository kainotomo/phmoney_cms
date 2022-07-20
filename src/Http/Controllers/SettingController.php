<?php

namespace Kainotomo\PHMoney\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Kainotomo\PHMoney\Http\Requests\SettingRequest;
use Kainotomo\PHMoney\Models\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\SettingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SettingRequest $request)
    {
        $validated = $request->validate($request->rules());
        $setting = Setting::create($validated['setting']);
        return $request->wantsJson()
            ? new JsonResponse(['status' => 'settings-stored', 'setting' => $setting], 200)
            : back()->with(['status' => 'settings-stored', 'setting' => $setting]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\SettingRequest  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(SettingRequest $request, Setting $setting)
    {
        $validated = $request->validate($request->rules());
        $setting->update($validated['setting']);
        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'settings-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        $setting->delete();
        return request()->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'settings-delted');
    }
}
