<?php

namespace East\LaravelActivityfeed;

use East\LaravelActivityfeed\Actions\AfTriggerActions;
use East\LaravelActivityfeed\Console\Commands\AfPoll;
use East\LaravelActivityfeed\Console\Commands\Cache;
use East\LaravelActivityfeed\Console\Commands\Generator;
use East\LaravelActivityfeed\Console\Commands\Install;
use East\LaravelActivityfeed\Console\Commands\Notify;
use East\LaravelActivityfeed\Models\Helpers\AfCaching;
use East\LaravelActivityfeed\Models\Helpers\AfData;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaravelActivityfeedServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->commands([
                Notify::class,
                Install::class,
                Generator::class,
                Cache::class,
                AfPoll::class,
            ]);
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/af-config.php', 'af_feed');
        $this->mergeConfigFrom(__DIR__ . '/../config/af-database-targeting.php', 'af_feed');
        $this->publishConfig();

        $this->loadViewsFrom(__DIR__.'/Resources/views', 'af_feed');
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        $this->publishes([
            __DIR__ . '/Resources/css/af.css' => resource_path('css/af.css'),
        ], 'asset');

        $this->publishes([
            __DIR__ . '/Resources/js/af.js' => public_path('js/af.js'),
        ], 'asset');

        $this->publishes([
            __DIR__ . '/Resources/views/backpack/widgets/js.blade.php' => resource_path('views/vendor/backpack/base/widgets/js.blade.php'),
        ], 'asset');

        $this->publishes([
            __DIR__ . '/Resources/views/backpack/views/template-create-form.blade.php' => resource_path('views/backpack/views/template-create-form.blade.php'),
        ], 'asset');

        $this->publishes([
            __DIR__ . '/ActivityFeed/Rules/RuleTemplate.php' => app_path('ActivityFeed/Rules/RulePost.php'),
        ], 'asset');

        $this->publishes([
            __DIR__ . '/ActivityFeed/Creators/CreatorTemplate.php' => app_path('ActivityFeed/Creators/TeamToUser.php'),
        ], 'asset');

        $this->publishes([
            __DIR__ . '/ActivityFeed/Channels/ChannelTemplate.php' => app_path('ActivityFeed/Channels/Email.php'),
        ], 'asset');


        $this->registerRoutes();

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('afpoll:run')->everyMinute();
        });

    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/Routes/ActivityFeedRoutes.php');
        });
    }

    /**
    * Get route group configuration array.
    *
    * @return array
    */
    private function routeConfiguration()
    {
        return [
/*            'namespace'  => "East\LaravelActivityfeed\Http\Backpack",
            'middleware' => 'admin',
            'prefix'     => config('backpack.base.route_prefix', 'admin'),*/
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register facade

        $this->app->bind('af-trigger', function () {
            return new AfTriggerActions();
        });

        $this->app->alias('AfTrigger', "\East\LaravelActivityfeed\Facades\AfTriggger");

        $this->app->bind('af-render', function () {
            return new \East\LaravelActivityfeed\Actions\AfRenderActions();
        });

        $this->app->bind('af-helper', function () {
            return new AfData();
        });

        $this->app->singleton(AfCaching::class, function () {
            return new AfCaching();
        });

        $this->app->singleton(AfData::class, function () {
            return new AfData();
        });

    }

    /**
     * Publish Config
     *
     * @return void
     */
    public function publishConfig()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/af-config.php' => config_path('af-config.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../config/af-database-targeting.php' => config_path('af-database-targeting.php'),
            ], 'config');
        }
    }
}
