<?php

namespace East\LaravelActivityfeed\Facades;

use East\LaravelActivityfeed\Actions\AfRenderActions;
use East\LaravelActivityfeed\Actions\AfTriggerActions;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplate;
use Illuminate\Support\Facades\Facade;


/**
 * Class AfRender
 *
 * @method static AfRenderActions setUser(int $id)
 * @method static AfRenderActions getFeed()
 * @method static AfRenderActions getMessage(AfNotification $notification)
 * @method static AfRenderActions mockVarReplacer($data,$id,$template)
 * @method static AfRenderActions renderTemplate(AfTemplate $template,$vars=[],$type='email-')
 * @method static AfRenderActions eventObjectReplacement(AfEvent $event_obj,$vars=[])
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