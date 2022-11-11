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

trait AfTraitCustomRule
{

    /** Launched by cron:AfPollAction - creates events
     * @param AfRule $rule
     * @return false|void
     */

    public function createCustomRule(AfRule $rule)
    {

        $obj = $this->createCustomRuleObj($rule);
        if (!$obj) {
            return false;
        }

        // see if event exists for this
        $check = AfEvent::where('id_rule', '=', $rule->id)
            ->whereTime('created_at', '>', Carbon::now()->subDays($obj::$grace_period))
            ->get();

        // event exists, exit
        if (!$check->isEmpty()) {
            return false;
        }

        // if no event, create it
        $this->createEvent($rule);
    }


    /** Launched by cron:AfPollAction - processes the events
     * @param AfRule $rule
     * @return void
     */
    public function runCustomRuleEvents(AfRule $rule)
    {

        return true;

        // todo: implement logic for custom rules
        $obj = $this->createCustomRuleObj($rule);

        // see if event exists for this
        $check = AfEvent::where('id_rule', '=', $rule->id)->where('processed', '=', 0)->with('afRule')->get();

        foreach ($check as $event) {
            $this->handleEvent($event);
        }
    }


    private function createEvent(AfRule $rule)
    {
        $event = new AfEvent();
        $event->id_rule = $rule->id;
        $event->dbtable = null;
        $event->dbkey = $this->id;
        $event->operation = 'Custom';
        $event->dbfield = null;
        $event->digestible = $rule->digestible;

        try {
            $event->save();
        } catch (\Throwable $e) {
            Log::log('error', $e->getMessage());
        }
    }

    /**
     * @param AfRule $rule
     * @return \East\LaravelActivityfeed\ActivityFeed\Rules\RuleBase|false
     */
    private function createCustomRuleObj(AfRule $rule)
    {
        $rule_name = $rule->name;
        $class1 = 'App\ActivityFeed\Rules\\' . $rule->rule_script;
        $class2 = 'East\LaravelActivityfeed\ActivityFeed\Rules\\' . $rule->rule_script;

        if (class_exists($class1)) {
            $class = $class1;
        } elseif (class_exists($class2)) {
            $class = $class2;
        } else {
            Log::error('AF-NOTIFY: No class found for rule ' . $rule_name);
            return false;
        }

        /* @var $obj \East\LaravelActivityfeed\ActivityFeed\Rules\RuleBase */
        $obj = new $class;
        if (!method_exists($obj, 'run')) {
            Log::error('AF-NOTIFY: No custom script method found for rule ' . $rule_name);
            return false;
        }
        return $obj;
    }


}