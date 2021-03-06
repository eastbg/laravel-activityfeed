<?php

namespace East\LaravelActivityfeed\Facades;

use East\LaravelActivityfeed\Actions\AfTriggerActions;
use Illuminate\Support\Facades\Facade;


/**
 * Class AfRules
 *
 * @method static AfTriggerActions setUser(int $id)
 *
 * @package App\Facades
 */

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