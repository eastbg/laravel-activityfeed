<?php

namespace East\LaravelActivityfeed;

use East\LaravelActivityfeed\Actions\AfTriggerActions;
use East\LaravelActivityfeed\Console\Commands\Cache;
use East\LaravelActivityfeed\Console\Commands\Generator;
use East\LaravelActivityfeed\Console\Commands\Install;
use East\LaravelActivityfeed\Console\Commands\Notify;
use East\LaravelActivityfeed\Models\Helpers\AfCaching;
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
            ]);
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/activity-feed.php', 'af_feed');
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
            __DIR__ . '/Rules/RuleTemplate.php' => app_path('ActivityFeed/Rules/RulePost.php'),
        ], 'asset');

        $this->registerRoutes();
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

        $this->app->singleton(AfCaching::class, function () {
            return new AfCaching();
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
                __DIR__ . '/../config/activity-feed.php' => config_path('activity-feed.php'),
            ], 'config');
        }
    }
}
