<?php

namespace East\LaravelActivityfeed\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityFeedModel
{

    public $id_user;
    public static $random;

    public static function getFeed(){
        if(!self::$random){
            self::$random = rand(12,239329329329);
        }

        return view('af_feed::components.feed',['random' => self::$random]);
    }

    public function loadRules(){

    }

}


