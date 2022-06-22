<?php

namespace East\LaravelActivityfeed\Facades;

use East\LaravelActivityfeed\Actions\AfTriggerActions;
use East\LaravelActivityfeed\Models\Helpers\AfData;
use Illuminate\Support\Facades\Facade;


/**
 * Class AfRules
 *
 * @method static AfData getTables()
 * @method static AfData getColumns($table)
 *
 * @package App\Facades
 */

class AfHelper extends Facade {


    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'af-helper';
    }



}