<?php namespace PortOneFive\Essentials\Users\Permissions;

use Illuminate\Database\Eloquent\Model;
use PortOneFive\Essentials\Users\Roles\Role;

class Permission extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['id', 'title', 'permission_group_id'];

    protected $with = ['permissionGroup'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permissionGroup()
    {
        return $this->belongsTo(PermissionGroup::class);
    }
}
