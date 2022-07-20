<?php

namespace East\LaravelActivityfeed\Interfaces;

use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use East\LaravelActivityfeed\Models\ActiveModels\AfUsers;

interface RuleInterface {

    public function shouldRun(AfRule $rule) : bool;
    public function createEvent(AfRule $rule) : AfEvent;
    public function canRunUser(AfEvent $event,AfUsers $user) : bool;

}