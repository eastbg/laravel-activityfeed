<?php

namespace East\LaravelActivityfeed\Interfaces;

use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;

interface RuleInterface {

    public function run(AfNotification $notification);

}