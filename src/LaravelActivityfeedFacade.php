<?php

namespace East\LaravelActivityfeed;

use Illuminate\Support\Facades\Facade;

class LaravelActivityfeedFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-activityfeed';
    }
}
