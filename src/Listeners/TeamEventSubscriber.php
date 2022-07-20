<?php

namespace Kainotomo\PHMoney\Listeners;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Kainotomo\PHMoney\Models\Base;
use Kainotomo\PHMoney\Models\Setting;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;

class TeamEventSubscriber
{
    /**
     * Handle user login events.
     */
    public function handleTeamCreated($event) {
        $team_id = $event->team->id;
        DB::connection('phmoney_portfolio')->table('settings')->insert([
            'team_id' => $team_id,
            'guid' => Base::uuid(),
            'type' => "AccountingPeriod",
            'name' => "Accounting Period",
            'params' => json_encode([
                'date_start' => [
                    'filter_date' => now()->firstOfYear()->toDateString(),
                    'date_type' => "filter_list",
                    'list_date' => [
                        'id' => "start_of_this_year",
                        'name' => "Start of this year"
                    ],
                ],
                'date_end' => [
                    'filter_date' => now()->toDateString(),
                    'date_type' => "filter_list",
                    'list_date' => [
                        'id' => "today",
                        'name' => "Today"
                    ],
                ],
            ]),
        ]);
        Storage::copy("samples/business_accounts.gnucash", "import/sqlite/$team_id.sqlite");
        Base::sqlite2mariadb($team_id);
    }

    /**
     * Handle user logout events.
     */
    public function handleTeamDeleted($event) {
        Base::deleteMariadb($event->team->id);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        return [
            TeamCreated::class => 'handleTeamCreated',
            TeamDeleted::class => 'handleTeamDeleted',
        ];
    }
}
