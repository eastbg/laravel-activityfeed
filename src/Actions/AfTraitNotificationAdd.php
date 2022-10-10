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
        if(!$event->dbtable OR !isset($event->afRule->id) OR !$event->afRule->id){
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

        $list = [];
        $output = [];

        foreach ($relation_rules as $rule){

            if($list){
                foreach ($list as $list_item){
                    $output[] = $list_item->$rule;
                }

                continue;
            }

            if(!isset($object->$rule)){
                continue;
            }

            if(is_object($object->$rule) AND get_class($object->$rule) == 'Illuminate\Database\Eloquent\Collection'){
                foreach($object->$rule as $item){
                    $list[] = $item;
                }

                continue;
            }

            $object = $object->$rule;

/*
            print_r(get_class($object->$rule));
            $object = $object->$rule;*/
        }

        return $output;
        //print_r($list);
        print_r($output[0]->id_zoho);


        die();



/*
        if(empty($relation_rules)){ return $collection; }
        $pointer = array_shift($relation_rules);
        if(!isset($object->$pointer)){ return $collection; }
        $original_collection = $collection;

        if(!is_string($object->$pointer)){
            $collection = [];

            if(is_object($object->$pointer)){
                echo($pointer);
                $fetch = $this->relationIterator($object->$pointer,$relation_rules,$original_collection,$output);
                if($fetch){ $collection[] = $fetch; }
            } else {
                echo(gettype($object->$pointer));
                $output[] = $object->$pointer;
            }

/*            echo(gettype($object->$pointer));

            foreach($object->$pointer as $obj){
                echo(gettype($object->$pointer));

                if(isset($obj->$pointer)){
                    echo(get_class($obj->$pointer));
                    echo(gettype($object->$pointer));
                }

                $fetch = $this->relationIterator($obj,$relation_rules,$original_collection);
                if($fetch){ $collection[] = $fetch; }
            }*/

/*            return $collection;
        } elseif(!$collection) {
            $return[] = $object->$pointer;
            return $return;
        } else {
            return $object->$pointer;
        }*/
    }



}
