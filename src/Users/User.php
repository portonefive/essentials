<?php

namespace PortOneFive\Essentials\Users;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use PortOneFive\Essentials\Eloquent\HasMetaTrait;
use PortOneFive\Essentials\Events\RaisesEvents;
use PortOneFive\Essentials\Users\Permissions\HasPermissions;
use PortOneFive\Essentials\Users\Permissions\Permission;
use PortOneFive\Essentials\Users\Roles\HasRoles;
use PortOneFive\Essentials\Users\Roles\Role;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, RaisesEvents, HasRoles, HasPermissions, HasMetaTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'password'];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function setPasswordAttribute($password)
    {
        /** @var Hasher $hash */
        $hash = app('hash');

        if ( ! empty($password)) {
            $this->attributes['password'] = $hash->needsRehash($password) ? $hash->make($password) : $password;
        }
    }
}