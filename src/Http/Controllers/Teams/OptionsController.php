<?php

namespace Kainotomo\PHMoney\Http\Controllers\Teams;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Models\Base;
use Kainotomo\PHMoney\Models\Book;
use Kainotomo\PHMoney\Models\Slot;
use Kainotomo\PHMoney\Models\Taxtable;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kainotomo\PHMoney\Http\Requests\Teams\DatabaseUploadRequest;
use Kainotomo\PHMoney\Http\Requests\Teams\LoadSampleRequest;
use Kainotomo\PHMoney\Http\Requests\Teams\OptionsRequest;
use Kainotomo\PHMoney\Models\Setting;

class OptionsController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  Illuminate\Http\Request  $request
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Team $team)
    {
        $user = $request->user();
        return response([
            'team' => $team,
            'permissions' => [
                'canAddTeamMembers' => Gate::check('addTeamMember', $team),
                'canDeleteTeam' => Gate::check('delete', $team),
                'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
                'canUpdateTeam' => Gate::check('update', $team),
            ],
            'options' => Slot::getOptions(),
            'taxtables' => Taxtable::where('team_id', $request->user()->current_team_id)->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\OptionsRequest  $request
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function store(OptionsRequest $request, Team $team)
    {
        $validated = $request->validate($request->rules());

        Setting::updateOrCreate(
            [
                'type' => "AccountingPeriod"
            ],
            [
                'params' => [
                    'date_start' => $validated['options']['accounting_period']['date_start'],
                    'date_end' => $validated['options']['accounting_period']['date_end'],
                ],
            ]
        );

        $book = Book::first();

        $option = Slot::firstOrCreate([
            'name' => 'options',
            'slot_type' => 9,
        ], [
            'obj_guid' => $book->guid,
            'guid_val' => Base::uuid(),
        ]);

        $optionBusiness = Slot::firstOrCreate([
            'name' => 'options/Business',
            'slot_type' => 9,
            'obj_guid' => $option->guid_val,
        ], [
            'guid_val' => Base::uuid(),
        ]);

        Slot::updateOrCreate([
            'name' => 'options/Business/Company Name',
            'slot_type' => 4,
            'obj_guid' => $optionBusiness->guid_val,
        ], [
            'string_val' => $validated['options']['business']['company_name'],
        ]);

        Slot::updateOrCreate([
            'name' => 'options/Business/Company Address',
            'slot_type' => 4,
            'obj_guid' => $optionBusiness->guid_val,
        ], [
            'string_val' => $validated['options']['business']['company_address'],
        ]);

        Slot::updateOrCreate([
            'name' => 'options/Business/Company Contact Person',
            'slot_type' => 4,
            'obj_guid' => $optionBusiness->guid_val,
        ], [
            'string_val' => $validated['options']['business']['company_contact_person'],
        ]);

        Slot::updateOrCreate([
            'name' => 'options/Business/Company Phone Number',
            'slot_type' => 4,
            'obj_guid' => $optionBusiness->guid_val,
        ], [
            'string_val' => $validated['options']['business']['company_phone_number'],
        ]);

        Slot::updateOrCreate([
            'name' => 'options/Business/Company Fax Number',
            'slot_type' => 4,
            'obj_guid' => $optionBusiness->guid_val,
        ], [
            'string_val' => $validated['options']['business']['company_fax_number'],
        ]);

        Slot::updateOrCreate([
            'name' => 'options/Business/Company Email Address',
            'slot_type' => 4,
            'obj_guid' => $optionBusiness->guid_val,
        ], [
            'string_val' => $validated['options']['business']['company_email_address'],
        ]);

        Slot::updateOrCreate([
            'name' => 'options/Business/Company Website URL',
            'slot_type' => 4,
            'obj_guid' => $optionBusiness->guid_val,
        ], [
            'string_val' => $validated['options']['business']['company_website_url'],
        ]);

        Slot::updateOrCreate([
            'name' => 'options/Business/Company ID',
            'slot_type' => 4,
            'obj_guid' => $optionBusiness->guid_val,
        ], [
            'string_val' => $validated['options']['business']['company_id'],
        ]);

        Slot::updateOrCreate([
            'name' => 'options/Business/Default Customer TaxTable',
            'slot_type' => 5,
            'obj_guid' => $optionBusiness->guid_val,
        ], [
            'guid_val' => isset($validated['options']['business']['default_customer_taxtable']) ? $validated['options']['business']['default_customer_taxtable']['guid'] : null,
        ]);

        Slot::updateOrCreate([
            'name' => 'options/Business/Default Vendor TaxTable',
            'slot_type' => 5,
            'obj_guid' => $optionBusiness->guid_val,
        ], [
            'guid_val' => isset($validated['options']['business']['default_vendor_taxtable']) ? $validated['options']['business']['default_vendor_taxtable']['guid'] : null,
        ]);

        $optionTax = Slot::firstOrCreate([
            'name' => 'options/Tax',
            'slot_type' => 9,
            'obj_guid' => $option->guid_val,
        ], [
            'guid_val' => Base::uuid(),
        ]);

        Slot::updateOrCreate([
            'name' => 'options/Tax/Tax Number',
            'slot_type' => 4,
            'obj_guid' => $optionTax->guid_val,
        ], [
            'string_val' => $validated['options']['tax']['tax_number'],
        ]);

        $counters = Slot::firstOrCreate([
            'name' => 'counters',
            'slot_type' => 9,
        ], [
            'obj_guid' => $book->guid,
            'guid_val' => Base::uuid(),
        ]);

        Slot::updateOrCreate([
            'name' => 'counters/gncBill',
            'slot_type' => 1,
            'obj_guid' => $counters->guid_val,
        ], [
            'int64_val' => $validated['options']['counters']['bill'],
        ]);

        Slot::updateOrCreate([
            'name' => 'counters/gncVendor',
            'slot_type' => 1,
            'obj_guid' => $counters->guid_val,
        ], [
            'int64_val' => $validated['options']['counters']['vendor'],
        ]);

        Slot::updateOrCreate([
            'name' => 'counters/gncInvoice',
            'slot_type' => 1,
            'obj_guid' => $counters->guid_val,
        ], [
            'int64_val' => $validated['options']['counters']['invoice'],
        ]);

        Slot::updateOrCreate([
            'name' => 'counters/gncJob',
            'slot_type' => 1,
            'obj_guid' => $counters->guid_val,
        ], [
            'int64_val' => $validated['options']['counters']['job'],
        ]);

        Slot::updateOrCreate([
            'name' => 'counters/gncEmployee',
            'slot_type' => 1,
            'obj_guid' => $counters->guid_val,
        ], [
            'int64_val' => $validated['options']['counters']['employee'],
        ]);

        $counterFormats = Slot::firstOrCreate([
            'name' => 'counter_formats',
            'slot_type' => 9,
        ], [
            'obj_guid' => $book->guid,
            'guid_val' => Base::uuid(),
        ]);

        Slot::updateOrCreate([
            'name' => 'counter_formats/gncBill',
            'slot_type' => 4,
            'obj_guid' => $counterFormats->guid_val,
        ], [
            'string_val' => $validated['options']['counter_formats']['bill'],
        ]);

        Slot::updateOrCreate([
            'name' => 'counter_formats/gncVendor',
            'slot_type' => 4,
            'obj_guid' => $counterFormats->guid_val,
        ], [
            'string_val' => $validated['options']['counter_formats']['vendor'],
        ]);

        Slot::updateOrCreate([
            'name' => 'counter_formats/gncInvoice',
            'slot_type' => 4,
            'obj_guid' => $counterFormats->guid_val,
        ], [
            'string_val' => $validated['options']['counter_formats']['invoice'],
        ]);

        Slot::updateOrCreate([
            'name' => 'counter_formats/gncJob',
            'slot_type' => 4,
            'obj_guid' => $counterFormats->guid_val,
        ], [
            'string_val' => $validated['options']['counter_formats']['job'],
        ]);

        Slot::updateOrCreate([
            'name' => 'counter_formats/gncEmployee',
            'slot_type' => 4,
            'obj_guid' => $counterFormats->guid_val,
        ], [
            'string_val' => $validated['options']['counter_formats']['employee'],
        ]);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'team-options-updated');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Models\Team $team
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Team $team)
    {
        Base::mariadb2sqlite($team->id);
        return Storage::download("import/sqlite/$team->id.sqlite");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\DatabaseUploadRequest  $request
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function upload(DatabaseUploadRequest $request, Team $team)
    {
        $validated = $request->validate($request->rules());

        Storage::putFileAs(
            'import/sqlite',
            $validated['sqlite_file'],
            "$team->id.sqlite"
        );
        Base::sqlite2mariadb($team->id);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'sqlite-file-uploaded');
    }

    /**
     * Get all templates
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function samples(Team $team)
    {
        return response(['samples' => Storage::allFiles('samples')]);
    }

    /**
     * Load sample
     *
     * @param  \Kainotomo\PHMoney\Http\Requests\LoadSampleRequest  $request
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function loadSample(LoadSampleRequest $request, Team $team)
    {
        $from = $request->validated('sample');
        $to = "import/sqlite/$team->id.sqlite";
        if (!Storage::copy($from, $to)) {
            return response('Failed to load sample', 404);
        }

        Base::sqlite2mariadb($team->id);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'sample-file-loaded');
    }
}
