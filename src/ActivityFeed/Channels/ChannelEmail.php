<?php

namespace App\ActivityFeed\Channels;

use East\LaravelActivityfeed\ActivityFeed\Channels\ChannelBase;

class ChannelEmail extends ChannelBase {

    public $type = 'cron';

}