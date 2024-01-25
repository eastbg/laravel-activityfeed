<?php

namespace East\LaravelActivityfeed\Actions;

use App\Models\Email\Emailer;
use Carbon\Carbon;
use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use East\LaravelActivityfeed\Models\Helpers\AfCachingHelper;
use Illuminate\Database\Eloquent\Collection;
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

trait AfTraitNotificationAdd
{

    /**
     * We take rules and determine the "path" to user table through relations
     * @param AfEvent $event
     * @return false|void
     */
    private function handleEvent(AfEvent $event)
    {
        if(!$event->dbtable OR !isset($event->afRule->id) OR !$event->afRule->id){
            return true;
        }

        //messages to admins only
        if($event->afRule->to_admins) {
            return true;
        }

        $rule = AfHelper::getTargeting($event->dbtable,$event->afRule->id);
        $class = AfHelper::getTableClass($event->dbtable);
        $obj = $class::find($event->dbkey);

        // get the user records
        $list = $this->relationIterator($obj, $rule['relations']);

        foreach($list as $user){

            if(!isset($user->id)){
                continue;
            }

            // not to add a duplicate to admins
            if(isset($user->admin) AND $user->admin == 1 AND $event->afRule->to_admins){
                continue;
            }

            $this->addToUser($user->id,$event);
        }
    }

    /**
     * Parses the relations "route" to users table. Definitions are in af-database-targeting.php
     * @param $object
     * @param $relation_rules
     * @param $collection
     * @return array|mixed
     */
    private function relationIterator($object,$relation_rules)
    {

        $last = array_pop($relation_rules);
        $output = [];
        $items = [];

        foreach($relation_rules as $rule){
            if(is_a($object, 'Illuminate\Database\Eloquent\Collection')) {
                foreach ($object as $item) {
                    $rule_result = $item->{$rule};
                    if(is_a($rule_result, 'Illuminate\Database\Eloquent\Collection')) {
                        foreach($rule_result as $res) {
                            $items[] = $res;
                        }
                    } else {
                        $items[] = $rule_result;
                    }
                }
            } else {
                $object = $object->{$rule};
            }
        }

        $result = !empty($items) ? $items : $object;

        foreach ($result as $item) {
            if (isset($item->{$last})) {
                $output[] = $item->{$last};
            }
        }

        return $output;
    }



}
