<?php

namespace App\ActivityFeed;

use East\LaravelActivityfeed\Models\ActiveModelBase;

/**
 * @property integer $id
 * @property integer $id_zoho_account
 * @property string $name
 * @property string $id_zoho
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
 * @property string $Mobile
 * @property string $Title
 * @property string $Gift_address
 * @property boolean $admin
 * @property Account $account
 * @property AfEvent[] $afEvents
 * @property AfNotification[] $afNotifications
 * @property Cv[] $cvs
 * @property EmailIdentity[] $emailIdentities
 * @property EmailJob[] $emailJobs
 * @property Email[] $emails
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
    protected $fillable = ['id_zoho_account', 'name', 'id_zoho', 'invitation', 'email', 'email_verified_at', 'password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token', 'current_team_id', 'profile_photo_path', 'created_at', 'updated_at', 'Mobile', 'Title', 'Gift_address', 'admin'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Database\Account', 'id_zoho_account', 'id_zoho');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afEvents()
    {
        return $this->hasMany('App\Models\Database\AfEvent', 'id_user_creator');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afNotifications()
    {
        return $this->hasMany('App\Models\Database\AfNotification', 'id_user_recipient');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cvs()
    {
        return $this->hasMany('App\Models\Database\Cv', 'id_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailIdentities()
    {
        return $this->hasMany('App\Models\Database\EmailIdentity', 'id_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailJobs()
    {
        return $this->hasMany('App\Models\Database\EmailJob', 'id_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails()
    {
        return $this->hasMany('App\Models\Database\Email', 'id_user');
    }
}
