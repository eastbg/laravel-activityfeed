<?php

namespace East\LaravelActivityfeed\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityFeedSaver
{

    public $id_user;

    /* @var $obj \East\LaravelActivityfeed\Models\ActivityFeedModel */
    public $obj;

    public function __construct(){
        $this->obj = new ActivityFeedModel();
    }

    public function setUser($id){
        $this->obj->id_user = $id;
        return $this;
    }

    public function save(){

    }






}


