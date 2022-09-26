<?php

namespace East\LaravelActivityfeed\Actions;

use App\Models\Email\Emailer;
use Carbon\Carbon;
use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use East\LaravelActivityfeed\Models\ActiveModels\AfUsers;
use East\LaravelActivityfeed\Models\Helpers\AfCachingHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\App;
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

        /* custom rules

        1. Find all active rules
        2. Check individual rule:
            - are criteria met for the run (asking the custom script)
            - check grace period
            - create event - custom script does this
        3. Process event - get users and check for each
            - are maximum number of notifications already sent?
            - is the grace period exceeded?
            - has the parent rule finished
            - are there any custom limiters provided by the custom scripts
        */
        do {
            $ret = $this->runCustomRules();
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
            } catch (\Throwable $exception) {
                Log::error('AF-NOTIFY: Error handling an event ' . $exception->getMessage());
            }

            $record->processed = 1;
            $record->save();
        }

        //$this->sendMessages();
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

    //$this->addToAdmins($record);

    /*            $this->applyRules($record);
                $record->processed = 1;
                $record->save();*/


    private function sendMessages()
    {
        $records = AfNotification::with('afEvent', 'afEvent.afRule', 'afEvent.afRule.afTemplate')->where('processed', '=', 0)->get();

        foreach ($records as $record) {
            if ($record->afEvent->afRule->digestible and $record->afEvent->afRule->digest_delay) {
                try {
                    $this->handledigestible($record);
                    $record->processed = 1;
                    $record->save();
                } catch (\Throwable $exception) {
                    Log::error('AF-NOTIFY: Could not run custom script ' . $exception->getMessage());
                }
            } else {
                try {
                    $this->handleNotification($record);
                    $record->processed = 1;
                    $record->save();
                } catch (\Throwable $exception) {
                    Log::error('AF-NOTIFY: Could not run custom script ' . $exception->getMessage());
                }
            }
        }
    }

    private function runCustomRules()
    {
        $records = AfRule::where('rule_script', '<>', '')->where('enabled', '=', 1)->get();

        foreach ($records as $record) {
            if (!$object = $this->createCustomRuleObj($record)) {
                continue;
            }

            try {
                if (!$object->shouldRun($record)) {
                    continue;
                }
                if (!$event = $object->createEvent($record)) {
                    continue;
                }
            } catch (\Throwable $exception) {
                // todo: add error display also to rules, not only on templates
                Log::error('AF-NOTIFY: Failed with a custom script on notification ' . $record->name);
                continue;
            }

            $users = $this->getEventTargeting($event);

            foreach ($users as $user) {
                if ($object->canRunUser($event, $user)) {
                    $this->addToUser($user->id, $event);
                }
            }

            $event->processed = 1;
            $event->save();


            //try {
            $this->createCustomRule($record);
            /*            } catch (\Throwable $exception){
                            Log::error('AF-NOTIFY: Could not run custom script '.$exception->getMessage());
                        }

                        try {*/
            $this->runCustomRuleEvents($record);
            /*            } catch (\Throwable $exception){
                            Log::error('AF-NOTIFY: Could not run custom script '.$exception->getMessage());
                        }*/
        }
    }


    private function addToAdmins($record)
    {
        $users = \App\ActivityFeed\AfUsersModel::where('admin', '=', 1)->get();

        foreach ($users as $user) {
            $this->addToUser($user->id, $record);
        }
    }


    private function addToUser(int $id, AfEvent $record)
    {

        // not adding to user that created it
        if ($id == $record->id_user_creator) {
            return false;
        }

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

        if ($record->afRule->afTemplate->email_template) {
            $user = AfUsers::find($id);
            $email = $user->email;
            $content = $record->afRule->afTemplate->email_template . $record->html;
            $subject = $record->afRule->afTemplate->email_subject;

            if ($user) {
                \Mail::html($content, function ($message) use ($email, $subject) {
                    /* @var Message $message */
                    $message
                        ->to($email)
                        ->from('beast@east.fi')
                        ->subject($subject);
                });
            }
        }

    }


}
