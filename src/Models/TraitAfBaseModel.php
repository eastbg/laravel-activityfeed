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

trait TraitAfBaseModel
{


    private function ruleChangeAndCreate()
    {

    }

    private function ruleCustomRule($rules)
    {

        return false;
    }


    /**
     * @param AfRule $rule
     * @param string $operation
     * @return bool|void
     */

    private function saveRule(AfRule $rule, string $operation)
    {

        if ($rule->digestible and $rule->digest_delay) {
            return $this->handleDigestable($rule, $operation);
        }

        $check = AfEvent::where('id_rule', '=', $rule->id)
            ->whereTime('created_at', '>', Carbon::now()->subSeconds(config('af-config.repeat_events_grace')))
            ->get();

        if ($check->isNotEmpty()) {
            return false;
        }

        return $this->saveIndividualRule($rule, $operation);
    }

    private function handleDigestable(AfRule $rule, string $operation)
    {
        return $this->saveIndividualRule($rule, $operation);
    }

    /** Protecting against duplicate entries for non-digestible events
     * @param AfRule $rule
     * @param string $operation
     * @return bool
     */
    private function saveIndividualRule(AfRule $rule, string $operation)
    {
        $event = new AfEvent();
        $event->id_user_creator = auth()->user()->id;
        $event->id_rule = $rule->id;
        $event->dbtable = $this->getTable();
        $event->dbkey = $this->id;
        $event->operation = $operation;
        $event->dbfield = $rule->field_name;
        $event->digestible = $rule->digestible;

        try {
            $event->save();
        } catch (\Throwable $e) {
            Log::log('error', $e->getMessage());
            return false;
        }

        return true;
    }

}

