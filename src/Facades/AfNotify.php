<?php

namespace East\LaravelActivityfeed\Facades;

use East\LaravelActivityfeed\Actions\AfTriggerActions;
use East\LaravelActivityfeed\Models\Helpers\AfDataHelper;
use East\LaravelActivityfeed\Models\Helpers\AfNotifyHelper;
use Illuminate\Support\Facades\Facade;


/**
 * Class AfRules
 *
 * @method static AfNotifyHelper add(string $slug)
 * @method static AfNotifyHelper setDbField(string $field)
 * @method static AfNotifyHelper setDbKey(int $key)
 * @method static AfNotifyHelper setDbTable(string $table)
 * @method static AfNotifyHelper setDigestible(bool $digest)
 * @method static AfNotifyHelper setUser(int $id_user)
 *
 * @package App\Facades
 */

class AfNotify extends Facade {


    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'af-notify';
    }



}