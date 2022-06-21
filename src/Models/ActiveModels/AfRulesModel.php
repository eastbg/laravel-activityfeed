<?php

namespace East\LaravelActivityfeed\Models\ActiveModels;

use East\LaravelActivityfeed\Models\ActiveModels\ActivityFeedBaseModel;

/**
 * @property integer $id
 * @property integer $id_category
 * @property integer $id_template
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property string $description
 * @property string $rule_type
 * @property string $rule
 * @property string $table_name
 * @property string $field_name
 * @property string $rule_operator
 * @property string $rule_value
 * @property string $rule_actions
 * @property string $context
 * @property boolean $background_job
 * @property boolean $digestible
 * @property boolean $enabled
 * @property AfCategory $afCategory
 * @property AfTemplate $afTemplate
 * @property AfEvent[] $afEvents
 */
class AfRulesModel extends ActivityFeedBaseModel
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'af_rules';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['id_category', 'id_template', 'created_at', 'updated_at', 'name', 'description', 'rule_type', 'rule', 'table_name', 'field_name', 'rule_operator', 'rule_value', 'rule_actions', 'context', 'background_job', 'digestible', 'enabled'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function afCategory()
    {
        return $this->belongsTo('East\LaravelActivityfeed\Models\ActiveModels\AfCategory', 'id_category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function afTemplate()
    {
        return $this->belongsTo('East\LaravelActivityfeed\Models\ActiveModels\AfTemplate', 'id_template');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afEvents()
    {
        return $this->hasMany('East\LaravelActivityfeed\Models\ActiveModels\AfEvent', 'id_rule');
    }
}
