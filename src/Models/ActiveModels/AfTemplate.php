<?php

namespace East\LaravelActivityfeed\Models\ActiveModels;

use East\LaravelActivityfeed\Models\ActivityFeedBaseModel;

/**
 * @property integer $id
 * @property integer $id_category
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property string $description
 * @property string $notification_subject
 * @property string $notification_template
 * @property string $email_subject
 * @property string $email_template
 * @property string $digest_subject
 * @property string $digest_template
 * @property string $admin_subject
 * @property string $admin_template
 * @property boolean $enabled
 * @property AfCategory $afCategory
 * @property AfEvent[] $afEvents
 * @property AfNotification[] $afNotifications
 * @property AfRule[] $afRules
 */
class AfTemplate extends ActivityFeedBaseModel
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'af_templates';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['id_category', 'created_at', 'updated_at', 'name', 'description', 'notification_subject', 'notification_template', 'email_subject', 'email_template', 'digest_subject', 'digest_template', 'admin_subject', 'admin_template', 'enabled'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function afCategory()
    {
        return $this->belongsTo('East\LaravelActivityfeed\Models\ActiveModels\AfCategory', 'id_category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afEvents()
    {
        return $this->hasMany('East\LaravelActivityfeed\Models\ActiveModels\AfEvent', 'id_template');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afNotifications()
    {
        return $this->hasMany('East\LaravelActivityfeed\Models\ActiveModels\AfNotification', 'id_template');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afRules()
    {
        return $this->hasMany('East\LaravelActivityfeed\Models\ActiveModels\AfRule', 'id_template');
    }
}
