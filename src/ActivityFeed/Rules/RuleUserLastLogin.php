<?php

namespace East\LaravelActivityfeed\ActivityFeed\Rules;

use East\LaravelActivityfeed\Interfaces\RuleInterface;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use East\LaravelActivityfeed\Models\ActiveModels\AfUsers;

class RuleUserLastLogin extends RuleBase {

    public static $description = '';

    public function shouldRun(AfRule $rule) : bool{
        return true;
    }

    public function createEvent(AfRule $rule) : AfEvent {
        return new AfEvent();
    }

    public function canRunUser(AfEvent $event,AfUsers $user) : bool{
        return true;
    }


}