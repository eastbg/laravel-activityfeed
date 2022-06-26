<?php

namespace App\ActivityFeed\Channels;

use East\LaravelActivityfeed\ActivityFeed\Channels\ChannelBase;
use East\LaravelActivityfeed\Interfaces\ChannelInterface;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;

class ChannelEmail extends ChannelBase implements ChannelInterface{

    public $type = 'cron';

    public function send(AfNotification $notification)
    {
        // TODO: Implement send() method.
    }

}