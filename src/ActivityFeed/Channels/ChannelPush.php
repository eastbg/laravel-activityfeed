<?php

namespace App\ActivityFeed\Channels;

use East\LaravelActivityfeed\ActivityFeed\Channels\ChannelBase;

class ChannelPush extends ChannelBase {

    public $type = 'cron';

}