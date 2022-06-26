<?php

namespace East\LaravelActivityfeed\Interfaces;

use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;

interface ChannelInterface {

    public function run(AfNotification $notification);

}