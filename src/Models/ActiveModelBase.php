<?php

namespace East\LaravelActivityfeed\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActiveModelBase extends Model
{
    use HasFactory;
    use CrudTrait;



    public function save(array $options = []){

        /*$attributes = $this->getAttributes();

        print_r(request()->all());

        print_r($this->attributes);die();

        foreach($attributes as $key=>$attribute){
            if(is_array($attribute)){
                print_r($key);die();
            }
        }*/


        parent::save($options);
    }


}

