<?php

namespace East\LaravelActivityfeed\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class AfRules
 *
 * @method static \East\LaravelActivityfeed\AfRules setUser(int $id)
 * @method static \East\LaravelActivityfeed\AfRules getFeed()
 *
 * @package App\Facades
 */

class AfRules extends Facade {


    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'af-rules';
    }



}