<?php

namespace East\LaravelActivityfeed\Facades;

use East\LaravelActivityfeed\Actions\AfRenderActions;
use East\LaravelActivityfeed\Actions\AfTriggerActions;
use Illuminate\Support\Facades\Facade;


/**
 * Class AfRender
 *
 * @method static AfRenderActions setUser(int $id)
 * @method static AfRenderActions getFeed()
 *
 * @package App\Facades
 */

class AfRender extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'af-render';
    }



}