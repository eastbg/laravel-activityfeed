<?php

namespace East\LaravelActivityfeed\Facades;

use Illuminate\Support\Facades\Facade;

class AfTriggger extends Facade {


    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'af-trigger';
    }



}