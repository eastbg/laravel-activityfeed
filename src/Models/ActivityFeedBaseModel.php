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

class ActivityFeedBaseModel extends Model
{
    use HasFactory;
    use CrudTrait;
    use TraitAfBaseModel;

    private $af_obj;
    private $rules;

    public function __construct(array $attributes = [])
    {
        $this->af_obj = App::make(ActivityFeedModel::class);
        $this->rules = $this->af_obj->loadRules();
        parent::__construct($attributes);
    }


    public function delete()
    {
        // get any custom rules
        $rules = AfHelper::getTableRules($this->getTable(), 'Delete');

        if ($rules) {
            foreach ($rules as $rule) {
                $operation = 'deleted';
                if ($this->saveRule($rule, $operation)) {
                    parent::delete();
                    return true;
                }

            }
        }

        return parent::delete(); // TODO: Change the autogenerated stub
    }

    /**
     * There can be multiple rules that apply, but we will generate only one event.
     * The rules are checked in the following order and only the first rule applied:
     * - Custom script
     * - Delete record
     * - New record
     * - Field value
     * - Field change
     * - Record change
     *
     * @param array $options
     * @return bool|void
     */
    public function save(array $options = [])
    {
        if (!isset(auth()->user()->id)) {
            parent::save();
            return;
        }

        // get any custom rules
        $rules = AfHelper::getTableRules($this->getTable(), 'Custom script');

        if ($rules) {
            foreach ($rules as $rule) {
                if ($this->ruleCustomRule($rule)) {
                    if ($this->saveRule($rule, 'Custom')) {
                        parent::save();
                        return;
                    }
                }
            }
        }

        // create new record rule
        if (!$this->exists) {
            $rules = AfHelper::getTableRules($this->getTable(), 'New Record');
            $operation = 'created';
            foreach ($rules as $rule) {
                if ($this->saveRule($rule, $operation)) {
                    parent::save();
                    return;
                }
            }
        }

        // field value equals ...
        $rules = AfHelper::getTableRules($this->getTable(), 'Field value');
        $operation = 'field value set to';

        foreach ($rules as $rule) {
            $field = $rule->field_name;
            if (isset($this->$field) and $this->$field == $rule->field_value) {
                if ($this->saveRule($rule, $operation)) {
                    parent::save();
                    return;
                }
            }
        }

        if ($this->exists) {
            $rules = AfHelper::getTableRules($this->getTable(), 'Record change');
            $operation = 'updated';
        } else {
            $rules = AfHelper::getTableRules($this->getTable(), 'New record');
            $operation = 'created';
        }

        // better have it saved so that we have the ID
        parent::save();

        if ($rules) {
            foreach ($rules as $rule) {
                $this->saveRule($rule, $operation);
            }
        }

    }

}


