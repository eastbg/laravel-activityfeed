<?php

namespace East\LaravelActivityfeed\Facades;

use East\LaravelActivityfeed\Actions\AfRenderActions;
use East\LaravelActivityfeed\Actions\AfTriggerActions;
use East\LaravelActivityfeed\Models\Helpers\AfTemplateHelper;
use Illuminate\Support\Facades\Facade;


/**
 * Class AfRender
 *
 * @method static AfTemplateHelper compileTemplates()
 *
 * @package App\Facades
 */

class AfTemplating extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'af-templating';
    }



}