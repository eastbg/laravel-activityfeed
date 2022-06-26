<?php

namespace East\LaravelActivityfeed\ActivityFeed\Channels;

use East\LaravelActivityfeed\Interfaces\ChannelInterface;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;

class ChannelBase implements ChannelInterface {


    public function run(AfNotification $notification)
    {
        // TODO: Implement send() method.
    }

}