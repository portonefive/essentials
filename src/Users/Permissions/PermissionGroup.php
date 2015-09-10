<?php namespace PortOneFive\Essentials\Users\Permissions;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['id', 'title'];

    public static function boot()
    {
        parent::boot();

        static::deleting(
            function (self $permissionGroup) {
                foreach ($permissionGroup->permissions as $permission) {
                    /** @var Permission $permission */
                    $permission->delete();
                }
            }
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
