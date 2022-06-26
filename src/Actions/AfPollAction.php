<?php

namespace East\LaravelActivityfeed\Actions;

use App\Models\Email\Emailer;
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

    public function runPoll()
    {

        $this->sendMessages();
        //$this->runCustomRules();
        //$this->runEvents();
        die();
    }

    private function sendMessages(){
        $records = AfNotification::with('AfEvent','AfEvent.AfRule')->where('processed','=',0)->get();
        foreach($records as $record){
            //try {

            $this->handleNotification($record);
            //$record->processed = 1;
            //$record->save();
            /*            } catch (\Throwable $exception){
                            Log::error('AF-NOTIFY: Could not run custom script '.$exception->getMessage());
                        }{*/
        }
    }

    private function runCustomRules(){
        $records = AfRule::where('rule_script','<>','')->where('enabled','=',1)->get();

        foreach($records as $record){
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

    private function runEvents(){
        $records = AfEvent::where('processed', '=', '0')->with('afRule')->get();
        foreach ($records as $record) {
            if ($record->afRule->to_admins) {
                $this->addToAdmins($record);
            }

            $this->applyRules($record);
            $record->processed = 1;
            $record->save();
        }
    }

}
