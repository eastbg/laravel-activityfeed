<?php

namespace East\LaravelActivityfeed\Facades;

use East\LaravelActivityfeed\Actions\AfTriggerActions;
use East\LaravelActivityfeed\Models\Helpers\AfDataHelper;
use Illuminate\Support\Facades\Facade;


/**
 * Class AfRules
 *
 * @method static AfDataHelper getTables()
 * @method static AfDataHelper getColumns($table)
 * @method static AfDataHelper getTargeting($table)
 * @method static AfDataHelper getChannels()
 * @method static AfDataHelper getRuleScripts()
 * @method static AfDataHelper getRuleOperators()
 * @method static AfDataHelper flushCaches()
 * @method static AfDataHelper getRules()
 * @method static AfDataHelper getTableRules($table,$rule_type)
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