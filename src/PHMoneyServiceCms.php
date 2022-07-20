<?php

namespace Kainotomo\PHMoney;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Kainotomo\PHMoney\Listeners\TeamEventSubscriber;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;

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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms');
        $this->configureComponents();
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

    /**
     * Configure the Blade components.
     * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
     * @return void
     */
    protected function configureComponents()
    {
        $this->callAfterResolving(BladeCompiler::class, function () {
            $this->registerComponent('layout');
            $this->registerComponent('navigation-menu');
        });
    }

    /**
     * Register the given component.
     *
     * @param  string  $component
     * @return void
     */
    protected function registerComponent(string $component)
    {
        Blade::component('cms::components.' . $component, 'cms-' . $component);
    }

    protected function notRunningInConsole() {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\CmsInstallCommand::class,
            Console\CmsUpdateCommand::class,
        ]);
    }
}
