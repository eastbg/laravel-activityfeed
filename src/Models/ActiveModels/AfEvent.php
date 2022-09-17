<?php

namespace East\LaravelActivityfeed\Models\ActiveModels;

use East\LaravelActivityfeed\Models\ActiveModelBase;
use East\LaravelActivityfeed\Models\ActivityFeedBaseModel;
use Illuminate\Database\Eloquent\Collection;
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
 * @property AfTemplate $afTemplate
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
    public static $constraints;
    public $foreignKey;
    public $otherKey;

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(AfNotification::class, 'id_event');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function afTemplate()
    {
        return $this->hasOneThrough(AfTemplate::class, AfRule::class,'id_template','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Candidates_X_Technologies()
    {
        return $this->belongsTo(\App\Models\Zoho\Modules\BaseModelsCandidatesXTechnology::class, 'id_user_creator');
    }

    // todo: dynamic relationship in place
/*    public function related(){
        Order::resolveRelationUsing('customer', function ($orderModel) {
            return $orderModel->belongsTo(Customer::class, 'customer_id');
        });
    }*/

    // Override the addConstraints method for the lazy loaded relationship.
    // If the foreign key of the model is 0, change the foreign key to the
    // model's own key, so it will load itself as the related model.

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            // For belongs to relationships, which are essentially the inverse of has one
            // or has many relationships, we need to actually query on the primary key
            // of the related models matching on the foreign key that's on a parent.
            $table = $this->related->getTable();

            $key = $this->parent->{$this->foreignKey} == 0 ? $this->otherKey : $this->foreignKey;

            $this->query->where($table.'.'.$this->otherKey, '=', $this->parent->{$key});
        }
    }

    // Override the match method for the eager loaded relationship.
    // Most of this is copied from the original method. The custom
    // logic is in the elseif.

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        $foreign = $this->foreignKey;

        $other = $this->otherKey;

        // First we will get to build a dictionary of the child models by their primary
        // key of the relationship, then we can easily match the children back onto
        // the parents using that dictionary and the primary key of the children.
        $dictionary = [];

        foreach ($results as $result) {
            $dictionary[$result->getAttribute($other)] = $result;
        }

        // Once we have the dictionary constructed, we can loop through all the parents
        // and match back onto their children using these keys of the dictionary and
        // the primary key of the children to map them onto the correct instances.
        foreach ($models as $model) {
            if (isset($dictionary[$model->$foreign])) {
                $model->setRelation($relation, $dictionary[$model->$foreign]);
            }
            // If the foreign key is 0, set the relation to a copy of the model
            elseif($model->$foreign == 0) {
                // Make a copy of the model.
                // You don't want recursion in your relationships.
                $copy = clone $model;

                // Empty out any existing relationships on the copy to avoid
                // any accidental recursion there.
                $copy->setRelations([]);

                // Set the relation on the model to the copy of itself.
                $model->setRelation($relation, $copy);
            }
        }

        return $models;
    }
}
