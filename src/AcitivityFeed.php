<?php

namespace East\LaravelActivityfeed;

use Illuminate\Support\Facades\Facade;
use East\LaravelActivityfeed\Models\ActivityFeedModel;

class AcitivityFeed extends Facade
{

    public static $random;
    public static $id_user;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'activity-feed';
    }

    public static function getFeed(){
        if(!self::$random){
            self::$random = rand(12,239329329329);
        }

        return view('af_feed::components.feed',['random' => self::$random]);
    }

    public static function setUser($id){
        self::$id_user = $id;
        return new static;
    }


    public static function saveActivity(){

    }

    public static function getActivity(int $id){

    }

    public static function getUserActivities(int $id){

    }

    public static function deleteActivity(int $id){

    }

    public static function markRead(ActivityFeedModel $afc){

    }



}
