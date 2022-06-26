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

trait AfTraitRuling {


    private function applyRules(AfEvent $record)
    {

        if (!isset($record->afRule->table_name)
            or
            !$record->dbtable or
            !$record->dbkey or
            !$record->afRule->rule_type
        ) {
            return false;
        }

        $targeting = AfHelper::getTargeting($record->afRule->table_name, $record->afRule->targeting);
        $class = config('af-config.af_model_path') . '\\' . $record->afRule->table_name;

        try {
            $obj = $class::find($record->dbkey);
        } catch (\Throwable $exception) {
            Log::error('AF-NOTIFY: Could not find the source class or record ' . $exception->getMessage());
            return false;
        }

        $relations = $targeting['relations'];
        $list = $this->parseThroughRelations($obj,$relations);

        if(!$list){
            Log::error('AF-NOTIFY: Potential problem, rule from '.$record->dbtable .' and targeting '.$record->afRule->targeting.' did not find any target users');
        }

        foreach($list as $item){
            if(isset($item->id)){
                $this->addToUser($item->id,$record);
            }
        }
    }

    /**
     *
     * @param $obj
     * @param $relations
     * @return mixed
     */
    private function parseThroughRelations($obj, $relations){
        $relation = array_shift($relations);

        if(!$relation){
            return $obj;
        }

        if(is_a($obj, 'Illuminate\Database\Eloquent\Collection') OR is_array($obj)) {
            $list = [];

            foreach($obj as $item){
                if($item){
                    $list[] = $item->{$relation};
                }
            }

            return $this->parseThroughRelations($list,$relations);
        }

        $content = $obj->{$relation} ?? null;

        if(is_a($content, 'Illuminate\Database\Eloquent\Collection')) {
            $list = [];
            foreach($content as $item){
                if($item){
                    $list[] = $item;
                }
            }

            return $this->parseThroughRelations($list,$relations);
        } else {
            return $this->parseThroughRelations($content,$relations);
        }
    }


    private function learnMethodType($classname, $method)
    {
        $oReflectionClass = new \ReflectionClass($classname);
        $method = $oReflectionClass->getMethod($method);
        $type = get_class($method->invoke($classname));
        return $type;
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
            Log::error('AF-NOTIFY: '.$exception->getMessage());
        }
    }



}