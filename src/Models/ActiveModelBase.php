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

        $attributes = $this->getAttributes();

        foreach($attributes as $key=>$attribute){
            if(is_array($attribute)){
                $this->setAttribute($key,json_encode($attribute));
            }
        }

        parent::save($options);
    }

    /**
     * @param Model $object
     * @return bool|mixed
     */
    private function getRelationType($object, $method)
    {
        if(!method_exists($object, $method)){ return false; }
        $oReflectionClass = new \ReflectionClass($object);
        $method = $oReflectionClass->getMethod($method);
        $type = get_class($method->invoke($object));
        if(!$type){ return false; }
        $type = explode('\\', $type);
        return is_array($type) ? end($type) : false;
    }

}


