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

trait AfTraitNotificationAdd
{

    /**
     * We take rules and determine the "path" to user table through relations
     * @param AfEvent $event
     * @return false|void
     */
    private function handleEvent(AfEvent $event)
    {
        $to_admins = $event->afRule->to_admins ?? null;

        // add to admins
        if($to_admins){ $this->addToAdmins($event); }
        $list = $this->getEventTargeting($event);

        foreach($list as $user){

            // not to add a duplicate to admins
            if(isset($user->admin) AND $user->admin AND $to_admins){
                continue;
            }

            $this->addToUser($user->id,$event);
        }
    }

    private function getEventTargeting(AfEvent $event) {
        $targeting = $event->afRule->targeting ?? null;;

        if (!$targeting) {
            return false;
        }

        $rule = AfHelper::getTargeting($event->afRule->table_name, $targeting);
        $class = AfHelper::getTableClass($event->dbtable);
        $obj = $class::find($event->dbkey);

        // get the user records
        return $this->relationIterator($obj, $rule['relations']);
    }

    /**
     * Parses the relations "route" to users table. Definitions are in af-database-targeting.php
     * @param $object
     * @param $relation_rules
     * @param $collection
     * @return array|mixed
     */
    private function relationIterator($object, $relation_rules, $collection = [])
    {
        if(empty($relation_rules)){ return $collection; }
        $pointer = array_shift($relation_rules);
        if(!isset($object->$pointer)){ return $collection; }
        $original_collection = $collection;

        if(get_class($object->$pointer) == 'Illuminate\Database\Eloquent\Collection'){
            $collection = [];

            foreach($object->$pointer as $obj){
                $fetch = $this->relationIterator($obj,$relation_rules,$original_collection);
                if($fetch){ $collection[] = $fetch; }
            }

            return $collection;
        } else {
            return $object->$pointer;
        }

    }



}