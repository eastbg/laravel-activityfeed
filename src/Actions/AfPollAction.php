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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AfPollAction extends Model
{
    use HasFactory;
    use AfTraitCustomRule;
    use AfTraitRuling;
    use AfTraitSender;
    use AfTraitDigestibles;
    use AfTraitNotificationAdd;

    public static $regular_event_statuses = [''];

    public function runPoll()
    {

        // digestable events
        do {
            $ret = $this->runDigestibles();
        } while ($ret);

        // run normal events
        $records = AfEvent::where('processed', '=', '0')
            ->with('afRule', 'afRule.afTemplate', 'afRule.afTemplate.afParent')
            ->get();

        foreach ($records as $record) {
            // means it's digestable, but not yet digested, so we skip it
            if ($record->digestible and !$record->digested) {
                continue;
            }

            try {
                $this->handleEvent($record);
                if($record->afRule->to_admins) {
                    $this->addToAdmins($record);
                }
            } catch (\Throwable $exception) {
                Log::error('AF-NOTIFY: failed to handle event ' . $exception->getMessage());
            }

            $record->processed = 1;
            $record->save();
        }

        $this->runCustomRules();
        $this->sendMessages();
    }

    /**
     * @return bool
     */
    private function runDigestibles(): bool
    {

        $records = AfEvent::where('digestible', '=', '1')->where(
            'digested', '=', '0')->with('afRule', 'afRule.afTemplate', 'afRule.afTemplate.afParent')->get();

        foreach ($records as $record) {
            $timing = Carbon::now()->addSeconds($record->afRule->digest_delay)->toDateTimeString();
            if ($record->digestible and $record->created < $timing) {
                $this->handleDigestible($record);
                return true;
            }
        }

        return false;
    }

    // this will use the custom channel classes to send notifications
    private function sendMessages()
    {
        $records = AfNotification::with('afEvent', 'afEvent.afRule', 'afEvent.afRule.afTemplate')->where('sent', '=', 0)->get();

        foreach ($records as $record) {
            $channels = json_decode($record->afEvent->afRule->channels);

            if ($channels) {
                foreach ($channels as $channel) {
                    $class = 'App\ActivityFeed\Channels\Channel' . $channel;

                    if (!class_exists($class)) {
                        $class = 'East\LaravelActivityfeed\ActivityFeed\Channels' . $channel;
                    }

                    if (!class_exists($class)) {
                        Log::error('AF-NOTIFY: Notification class does not exist ' . $channel);
                        continue;
                    }

                    try {
                        $obj = new $class;
                        $obj->send($record);
                        Log::info('AF-NOTIFY: Message sent. Channel ' . $channel . ' to user ' . $record->id_user_recipient);

                    } catch (\Throwable $exception) {
                        Log::error('AF-NOTIFY: Error sending message ' . $exception->getMessage());
                    }
                }
            }

            $record->sent = 1;
            $record->save();
        }
    }

    private function runCustomRules()
    {

        return true;

        // todo: this needs to be implemented properly with safeguards
        $records = AfRule::where('rule_script', '<>', '')->where('enabled', '=', 1)->get();

        foreach ($records as $record) {
            try {
                $this->createCustomRule($record);
            } catch (\Throwable $exception) {
                Log::error('AF-NOTIFY: Could not run custom script ' . $exception->getMessage());
            }

            try {
                $this->runCustomRuleEvents($record);
            } catch (\Throwable $exception) {
                Log::error('AF-NOTIFY: Could not run custom script ' . $exception->getMessage());
            }
        }
    }


    private function addToAdmins($record)
    {
        $users = self::getAdmins();

        foreach ($users as $user) {
            $this->addToUser($user, $record);
        }
    }


    private function addToUser(int $id, AfEvent $record)
    {
        $admins = self::getAdmins();
        // not adding to user that created it unless it's admin
        if ($id == $record->id_user_creator && !in_array($id,$admins)) {
            return false;
        }

        Cache::delete('notifications-'.$id);

        $obj = new AfNotification();
        $obj->id_user_recipient = $id;
        $obj->id_user_creator = $record->id_user_creator;
        $obj->id_rule = $record->id_rule;
        $obj->id_event = $record->id;

        try {
            $obj->save();
        } catch (\Throwable $exception) {
            Log::error('AF-NOTIFY: ' . $exception->getMessage());
        }
    }

    private function getAdmins()
    {
        $users = \App\ActivityFeed\AfUsersModel::where('admin', '=', 1)->get();
        $ids = [];
        foreach($users as $user) {
            $ids[] = $user->id;
        }
        return $ids;
    }
}
