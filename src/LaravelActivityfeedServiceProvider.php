<?php

namespace East\LaravelActivityfeed;

use App\Models\Email\Emailer;
use East\LaravelActivityfeed\Console\Commands\Notify;
use East\LaravelActivityfeed\Facades\AfRules;
use East\LaravelActivityfeed\Facades\AfTriggger;
use East\LaravelActivityfeed\Models\ActivityFeedBaseModel;
use East\LaravelActivityfeed\Models\ActivityFeedModel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Jenssegers\Agent\Agent;

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
            ]);
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/LaravelActivityfeed.php', 'af_feed');
        $this->publishConfig();

        $this->loadViewsFrom(__DIR__.'/resources/views', 'af_feed');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__ . '/resources/css/af.css' => resource_path('css/af.css'),
        ], 'asset');

        // $this->registerRoutes();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
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
            'namespace'  => "East\LaravelActivityfeed\Http\Controllers",
            'middleware' => 'api',
            'prefix'     => 'api'
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
        $this->app->singleton(ActivityFeed::class, function () {
            return new ActivityFeed();
        });

        $this->app->singleton(\East\LaravelActivityfeed\AfTriggger::class, function () {
            return new \East\LaravelActivityfeed\AfTriggger();
        });

        $this->app->alias('ActivityFeed', ActivityFeed::class);

        $this->app->bind('af-trigger', function () {
            return new \East\LaravelActivityfeed\AfTriggger();
        });

        $this->app->alias('AfTrigger', "\East\LaravelActivityfeed\Facades\AfTriggger");
        $this->app->bind('af-rules', \East\LaravelActivityfeed\AfRules::class);


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
                __DIR__ . '/../config/LaravelActivityfeed.php' => config_path('LaravelActivityfeed.php'),
            ], 'config');
        }


    }
}
