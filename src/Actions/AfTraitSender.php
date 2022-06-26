<?php

namespace East\LaravelActivityfeed\Actions;

use App\Models\Email\Emailer;
use Carbon\Carbon;
use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use East\LaravelActivityfeed\Models\Helpers\AfCachingHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/*
 * 1. Create custom rules (takes grace period from the class)
 * 2. Runs unprocessed custom rules, creating notifications
 * 3. Feed items are just entries in the database, external sends are handled by another cron (afpoll:send)
 * */

trait AfTraitSender
{


    public function handleNotification(AfNotification $notification)
    {
        $channels = json_decode($notification->afEvent->AfRule->channels, true);

        if(!$notification->afEvent->afRule->afTemplate->email_subject
        OR !$notification->afEvent->afRule->afTemplate->email_template
        ){
            $notification->afEvent->afRule->afTemplate->error = 'There is a rule configured ('.$notification->afEvent->afRule->name .'), which uses this template & has a channel configured, 
            but is missing email subject or template content';
            $notification->afEvent->afRule->afTemplate->save();
            return false;
        }

        foreach ($channels as $channel) {
            if($channel == 'feed'){ continue; }
            $obj = $this->createChannelObject($channel);
            if($obj){
                $obj->run($notification);
            }
        }
    }

    private function createChannelObject(string $channel)
    {
        $class1 = 'App\ActivityFeed\Channels\Channel' . $channel.'';
        $class2 = 'East\LaravelActivityfeed\ActivityFeed\Channels\Channel' . $channel.'';

        if (class_exists($class1)) {
            $class = $class1;
        } elseif (class_exists($class2)) {
            $class = $class2;
        } else {
            Log::error('AF-NOTIFY: No class found for channel ' . $channel);
            return false;
        }

        /* @var $obj \East\LaravelActivityfeed\ActivityFeed\Channels\ChannelBase */
        $obj = new $class;
        if (!method_exists($obj, 'run')) {
            Log::error('AF-NOTIFY: No custom script method found for rule ' . $channel);
            return false;
        }

        return $obj;
    }


}