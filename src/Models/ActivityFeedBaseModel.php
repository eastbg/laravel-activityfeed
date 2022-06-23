<?php

namespace East\LaravelActivityfeed\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
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

    public function save(array $options = [])
    {
        if ($this->exists) {
            $rules = AfHelper::getTableRules($this->getTable(), 'Record change');
        } else {
            $rules = AfHelper::getTableRules($this->getTable(), 'New Record');
        }

        if ($rules and isset(auth()->user()->id)) {
            foreach ($rules as $rule) {
                $this->saveRule($rule);
            }
        }

        parent::save();
    }

    private function saveRule($rule)
    {

        $check = AfEvent::where('id_rule', '=', $rule->id)
            ->whereTime('created_at', '>', Carbon::now()->subSeconds(config('af-config.repeat_events_grace')))
            ->get();

        if($check->isNotEmpty()){
            return true;
        }

        $event = new AfEvent();
        $event->id_user_creator = auth()->user()->id;
        $event->id_rule = $rule->id;
        $event->save();
    }

}


