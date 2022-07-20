<?php

namespace Kainotomo\PHMoney;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Kainotomo\PHMoney\Listeners\TeamEventSubscriber;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;

class PHMoneyServiceCms extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'phmoney');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Event::listen(
            TeamCreated::class,
            [TeamEventSubscriber::class, 'handleTeamCreated']
        );
        Event::listen(
            TeamDeleted::class,
            [TeamEventSubscriber::class, 'handleTeamDeleted']
        );

        $this->notRunningInConsole();

    }

    protected function notRunningInConsole() {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
            Console\UpdateCommand::class,
        ]);
    }
}
