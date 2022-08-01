<?php

namespace East\LaravelActivityfeed\Models\ActiveModels;

use East\LaravelActivityfeed\Models\ActiveModelBase;

/**
 * @property integer $id
 * @property integer $id_user_recipient
 * @property integer $id_user_creator
 * @property integer $id_rule
 * @property integer $id_event
 * @property string $created_at
 * @property string $updated_at
 * @property string $expiry
 * @property boolean $sent
 * @property boolean $read
 * @property boolean $digested
 * @property boolean $digestible
 * @property boolean $processed
 * @property User $creator
 * @property AfRule $afRule
 * @property AfEvent $afEvent
 * @property User $recipient
 */
class AfNotification extends ActiveModelBase
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'af_notifications';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['id_user_recipient', 'id_user_creator', 'id_template','id_event',
        'id_rule', 'id_category', 'created_at', 'updated_at', 'channels', 'notification_subject',
        'notification_template', 'email_subject', 'email_template', 'digest_subject', 'digest_template','digestible',
        'admin_subject', 'admin_template', 'expiry', 'sent', 'read', 'digest', 'digested', 'processed', 'popup'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient()
    {
        return $this->belongsTo(\App\ActivityFeed\AfUsersModel::class, 'id_user_recipient');
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
    public function afEvent()
    {
        return $this->belongsTo('East\LaravelActivityfeed\Models\ActiveModels\AfEvent', 'id_event');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(\App\ActivityFeed\AfUsersModel::class, 'id_user_creator');
    }
}
