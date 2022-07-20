<?php

namespace East\LaravelActivityfeed\ActivityFeed\Rules;


use East\LaravelActivityfeed\Interfaces\RuleInterface;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use East\LaravelActivityfeed\Models\ActiveModels\AfUsers;

class RuleBase implements RuleInterface {


    public static $description = '';

    /* Whether to show these fields in the form, used for specific rule configurations */
    public static $operator = true;
    public static $value = true;

    /* These are shown in the rule edit form */
    public static $operator_hint = 'Hint for operator option field';
    public static $value_hint = 'Hint for value option field';

    /* If event within this number of days exists, don't create a new one */
    public static $grace_period = 7;

    public function shouldRun(AfRule $rule) : bool{
        return false;
    }

    public function createEvent(AfRule $rule) : AfEvent {
        return new AfEvent();
    }

    public function canRunUser(AfEvent $event,AfUsers $user) : bool{
        return false;
    }



}