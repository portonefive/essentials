<?php namespace PortOneFive\Essentials\Users\Permissions;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['id', 'title'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
