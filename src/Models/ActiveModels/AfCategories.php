<?php

namespace East\LaravelActivityfeed\Models\ActiveModels;

use East\LaravelActivityfeed\Models\ActivityFeedBaseModel;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property string $icon
 * @property string $description
 * @property string $ui_placement
 * @property boolean $enabled
 * @property AfEvent[] $afEvents
 * @property AfNotification[] $afNotifications
 * @property AfRule[] $afRules
 * @property AfTemplate[] $afTemplates
 */
class AfCategories extends ActivityFeedBaseModel
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
    protected $fillable = ['created_at', 'updated_at', 'name', 'icon', 'description', 'ui_placement', 'enabled'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afEvents()
    {
        return $this->hasMany('East\LaravelActivityfeed\Models\ActiveModels\AfEvent', 'id_category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afNotifications()
    {
        return $this->hasMany('East\LaravelActivityfeed\Models\ActiveModels\AfNotification', 'id_category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afRules()
    {
        return $this->hasMany('East\LaravelActivityfeed\Models\ActiveModels\AfRule', 'id_category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afTemplates()
    {
        return $this->hasMany('East\LaravelActivityfeed\Models\ActiveModels\AfTemplate', 'id_category');
    }
}
