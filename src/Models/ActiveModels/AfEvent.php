<?php

namespace East\LaravelActivityfeed\Models\ActiveModels;

use East\LaravelActivityfeed\Models\ActivityFeedBaseModel;

/**
 * @property integer $id
 * @property integer $id_user_creator
 * @property integer $id_rule
 * @property boolean $processed
 * @property AfRule $afRule
 * @property User $user
 */
class AfEvent extends ActivityFeedBaseModel
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
    protected $fillable = ['id_user_creator', 'id_template', 'id_rule', 'id_category', 'created_at', 'updated_at', 'targeting', 'expiry', 'processed', 'admins', 'digest', 'digested', 'to_admins', 'background_job', 'popup'];

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
    public function user()
    {
        return $this->belongsTo('East\LaravelActivityfeed\Models\ActiveModels\User', 'id_user_creator');
    }
}
