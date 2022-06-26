<?php

namespace East\LaravelActivityfeed\ActivityFeed\Rules;


use East\LaravelActivityfeed\Interfaces\RuleInterface;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;

class RuleBase implements RuleInterface {

    public static $description = '';

    /* Whether to show these fields in the form, used for specific rule configurations */
    public static $operator = true;
    public static $value = true;

    /* These are shown in the rule edit form */
    public static $operator_hint = 'Hint for operator option field';
    public static $value_hint = 'Hint for value option field';

    /* If even within this number of days exists, don't create a new one */
    public static $grace_period = 7;

    public function run(AfNotification $notification){}



}