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

class AfPollAction extends Model
{
    use HasFactory;
    use AfTraitCustomRule;
    use AfTraitRuling;
    use AfTraitSender;
    use AfTraitDigestibles;

    public static $regular_event_statuses = [''];

    public function runPoll()
    {

        do {
            $ret = $this->runDigestibles();
        } while ($ret);

        // run normal events
        $records = AfEvent::where('processed', '=', '0')
            ->with('afRule', 'afRule.afTemplate','afRule.afTemplate.afParent')
            ->get();

        foreach($records as $record){
            $this->handleEvent($record);
            $this->addToAdmins($record);
        }

        //$this->runCustomRules();
        //$this->sendMessages();
        die();
    }

    /**
     * @return bool
     */
    private function runDigestibles() : bool
    {

        $records = AfEvent::where('digestible', '=', '1')->where(
            'digested', '=', '0')->with('afRule', 'afRule.afTemplate','afRule.afTemplate.afParent')->get();

        foreach ($records as $record) {
            $timing = Carbon::now()->addSeconds($record->afRule->digest_delay)->toDateTimeString();
            if ($record->digestible and $record->created < $timing) {
                $this->handleDigestible($record);
                $record->processed = 1;
                $record->save();
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


}
