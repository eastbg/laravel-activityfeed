<?php

namespace East\LaravelActivityfeed\Models\ActiveModels;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use East\LaravelActivityfeed\Models\ActivityFeedModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class ActivityFeedBaseModel extends Model
{
    use HasFactory;
    use CrudTrait;

    private $af_obj;
    private $rules;

    public function __construct(array $attributes = [])
    {
        $this->af_obj = App::make(ActivityFeedModel::class);
        $this->rules = $this->af_obj->loadRules();
        parent::__construct($attributes);
    }

    public function save(array $options=[]){




        echo($this->getTable());die();


        parent::save();
    }

}


