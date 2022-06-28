<?php

namespace East\LaravelActivityfeed\Models\ActiveModels;

use East\LaravelActivityfeed\Models\ActiveModelBase;
use East\LaravelActivityfeed\Models\ActivityFeedBaseModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_user_creator
 * @property integer $id_rule
 * @property string $created_at
 * @property string $updated_at
 * @property integer $dbkey
 * @property string $dbtable
 * @property string $operation
 * @property string $dbfield
 * @property boolean $processed
 * @property boolean $digested
 * @property boolean $digestible
 * @property string $digest_content
 * @property AfRule $afRule
 * @property User $creator
 */
class AfEvent extends ActiveModelBase
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
    protected $fillable = [
        'id_user_creator',
        'id_rule',
        'created_at',
        'updated_at',
        'processed',
        'dbtable',
        'dbkey',
        'operation',
        'digested',
        'digestible',
        'digest_content',
        'dbfield'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function afRule()
    {
        return $this->belongsTo(AfRule::class, 'id_rule');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(\App\ActivityFeed\AfUsersModel::class, 'id_user_creator');
    }
}
