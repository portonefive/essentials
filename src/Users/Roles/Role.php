<?php namespace PortOneFive\Essentials\Users\Roles;

use Illuminate\Database\Eloquent\Model;
use PortOneFive\Essentials\Users\Permissions\Permission;

class Role extends Model
{

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['id', 'title'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('auth.model'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}