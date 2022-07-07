<?php

namespace App\ActivityFeed;

use East\LaravelActivityfeed\Models\ActiveModelBase;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;

/**
 * @property integer $id
 * @property string $name
 * @property string $invitation
 * @property string $email
 * @property string $email_verified_at
 * @property string $password
 * @property string $two_factor_secret
 * @property string $two_factor_recovery_codes
 * @property string $remember_token
 * @property integer $current_team_id
 * @property string $profile_photo_path
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $admin
 * @property AfEvent[] $afEvents
 * @property AfNotification[] $afNotifications
 */
class AfUsersModel extends ActiveModelBase
{
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';
    protected $table = 'users';

    /**
     * @var array
     */
    protected $fillable = ['name', 'invitation', 'email', 'email_verified_at', 'password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token', 'current_team_id', 'profile_photo_path', 'created_at', 'updated_at', 'admin'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afEvents()
    {
        return $this->hasMany(AfEvent::class, 'id_user_creator');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afNotifications()
    {
        return $this->hasMany(AfNotification::class, 'id_user_recipient');
    }
}
