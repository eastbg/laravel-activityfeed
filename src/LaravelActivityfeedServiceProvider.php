<?php

namespace East\LaravelActivityfeed;

use East\LaravelActivityfeed\Actions\AfTriggerActions;
use East\LaravelActivityfeed\Console\Commands\AfPoll;
use East\LaravelActivityfeed\Console\Commands\Cache;
use East\LaravelActivityfeed\Console\Commands\Generator;
use East\LaravelActivityfeed\Console\Commands\Install;
use East\LaravelActivityfeed\Console\Commands\Notify;
use East\LaravelActivityfeed\Facades\AfNotify;
use East\LaravelActivityfeed\Models\Helpers\AfCachingHelper;
use East\LaravelActivityfeed\Models\Helpers\AfDataHelper;
use East\LaravelActivityfeed\Models\Helpers\AfNotifyHelper;
use East\LaravelActivityfeed\Models\Helpers\AfTemplateHelper;
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

/*        $this->mergeConfigFrom(__DIR__ . '/../config/af-config.php', 'af_feed');
        $this->mergeConfigFrom(__DIR__ . '/../config/af-database-targeting.php', 'af_feed');*/
        $this->publishConfig();

        $this->loadViewsFrom(__DIR__.'/resources/views', 'af_feed');
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        $this->publishes([
            __DIR__ . '/resources/css/af.css' => resource_path('css/af.css'),
            __DIR__ . '/resources/js/af.js' => public_path('js/af.js'),
        ], 'asset');

        $this->publishAssetDirectory(__DIR__.'/resources/css/');
        $this->publishAssetDirectory(__DIR__.'/resources/js/');
        $this->publishAssetDirectory(__DIR__.'/resources/views/');

        $path = str_replace('\\','/',config('af-config.af_model_path'));

        //echo($path);die();

        $this->publishes([
            __DIR__ . '/ActivityFeed/Rules/RuleTemplate.php' => app_path('ActivityFeed/Rules/RulePost.php'),
            __DIR__ . '/ActivityFeed/Creators/CreatorTemplate.php' => app_path('ActivityFeed/Creators/TeamToUser.php'),
            __DIR__ . '/ActivityFeed/Channels/ChannelTemplate.php' => app_path('ActivityFeed/Channels/Email.php'),

            // todo: verify that this goes right
            __DIR__ . '/ActivityFeed/AfUsersModel.php' => config('af-config.af_model_path').'/AfUsersModel.php'
        ], 'templates');

        $this->registerRoutes();

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('afpoll:run')->everyMinute();
        });

    }

    private function publishAssetDirectory($from){
        $files = $this->getFileList($from);
        $publish = [];

        foreach($files as $file){
            $file = substr($file,strpos($file,'src/')+4);
            $source = __DIR__ .'/'.$file;
            $publish[$source] = app_path('../'.$file);
        }

        if($publish){
            $this->publishes($publish, 'asset');
        }
    }

    private function getFileList($from,& $output=[]){
        $files = scandir($from);

        foreach ($files as $name) {
            $path = $from.$name;

            if($name == '.' OR $name == '..') {
                continue;
            }if(is_dir($path)){
                $output = $this->getFileList($path.'/',$output);
            } elseif(stristr($name,'.php') OR stristr($name,'.css') OR stristr($name,'.js')) {
                $output[] = $path;
            }
        }

        return $output;
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

        $this->app->bind('af-templating', function () {
            return new AfTemplateHelper();
        });

        $this->app->bind('af-helper', function () {
            return new AfDataHelper();
        });

        $this->app->bind('af-notify', function () {
            return new AfNotifyHelper();
        });

        $this->app->singleton(AfCachingHelper::class, function () {
            return new AfCachingHelper();
        });

        $this->app->singleton(AfDataHelper::class, function () {
            return new AfDataHelper();
        });

        $this->app->singleton(AfNotifyHelper::class, function () {
            return new AfNotifyHelper();
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
                __DIR__ . '/../config/af-database-targeting.php' => config_path('af-database-targeting.php'),
            ], 'config');
        }
    }
}
