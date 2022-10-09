<?php

namespace East\LaravelActivityfeed\Models\ActiveModels;

use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Models\ActiveModelBase;
use East\LaravelActivityfeed\Models\ActivityFeedBaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property integer $id
 * @property integer $id_category
 * @property integer $id_template
 * @property string $created_at
 * @property string $updated_at
 * @property string $rule_script
 * @property string $creator_script
 * @property mixed $targeting
 * @property mixed $channels
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
 * @property boolean $to_admins
 * @property boolean $background_job
 * @property boolean $digestible
 * @property boolean $digest_delay
 * @property boolean $enabled
 * @property boolean $popup
 * @property boolean $slug
 * @property AfTemplate $afMasterTemplate
 * @property AfCategory $afCategory
 * @property AfTemplate $afTemplate
 * @property AfEvent[] $afEvents
 * @property AfNotification[] $afNotifications
 */
class AfRule extends ActiveModelBase
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';


    /**
     * @var array
     */
    protected $fillable = ['id_category', 'id_template', 'created_at', 'updated_at', 'rule_script', 'creator_script',
        'targeting', 'channels', 'name', 'description', 'rule_type', 'rule', 'table_name', 'field_name',
        'rule_operator', 'rule_value', 'rule_actions', 'context', 'to_admins', 'background_job',
        'digestible', 'digest_delay', 'enabled', 'popup','slug'];

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
    public function afCategories()
    {
        return $this->belongsTo('East\LaravelActivityfeed\Models\ActiveModels\AfCategory', 'id_category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function afTemplate()
    {
        return $this->belongsTo(AfTemplate::class, 'id_template');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function afTemplates()
    {
        return $this->belongsTo('East\LaravelActivityfeed\Models\ActiveModels\AfTemplate', 'id_template');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function afMasterTemplate()
    {
        return $this->belongsTo('East\LaravelActivityfeed\Models\ActiveModels\AfTemplate', 'id_master_template');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afEvents()
    {
        return $this->hasMany('East\LaravelActivityfeed\Models\ActiveModels\AfEvent', 'id_rule');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afEvent()
    {
        return $this->hasMany(AfEvent::class, 'id_rule');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afNotifications()
    {
        return $this->hasMany('East\LaravelActivityfeed\Models\ActiveModels\AfNotification', 'id_rule');
    }

    public function save(array $options = [])
    {
        Cache::delete('af_rules');
        parent::save($options); // TODO: Change the autogenerated stub
    }


}
