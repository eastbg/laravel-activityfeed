<?php

namespace East\LaravelActivityfeed\Models\ActiveModels;

use East\LaravelActivityfeed\Models\ActiveModels\ActivityFeedBaseModel;

/**
 * @property integer $id
 * @property integer $id_user_recipient
 * @property integer $id_user_creator
 * @property integer $id_template
 * @property integer $id_rule
 * @property integer $id_category
 * @property string $created_at
 * @property string $updated_at
 * @property integer $id_user
 * @property string $related_table
 * @property integer $foreign_key
 * @property string $notification_subject
 * @property string $notification_template
 * @property string $email_subject
 * @property string $email_template
 * @property string $digest_subject
 * @property string $digest_template
 * @property string $admin_subject
 * @property string $admin_template
 * @property string $expiry
 * @property boolean $read
 * @property boolean $sent
 * @property boolean $digest
 * @property boolean $digested
 * @property AfCategory $afCategory
 * @property AfTemplate $afTemplate
 * @property User $recipient
 * @property AfRule $afRule
 * @property User $creator
 */
class AfEventsModel extends ActivityFeedBaseModel
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'af_events';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['id_user_recipient', 'id_user_creator', 'id_template', 'id_rule', 'id_category', 'created_at', 'updated_at', 'id_user', 'related_table', 'foreign_key', 'notification_subject', 'notification_template', 'email_subject', 'email_template', 'digest_subject', 'digest_template', 'admin_subject', 'admin_template', 'expiry', 'read', 'sent', 'digest', 'digested'];

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient()
    {
        return $this->belongsTo('East\LaravelActivityfeed\Models\ActiveModels\User', 'id_user_recipient');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function afRule()
    {
        return $this->belongsTo('East\LaravelActivityfeed\Models\ActiveModels\AfRule', 'id_rule');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo('East\LaravelActivityfeed\Models\ActiveModels\User', 'id_user_creator');
    }
}
