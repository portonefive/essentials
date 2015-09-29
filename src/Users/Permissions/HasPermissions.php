<?php namespace PortOneFive\Essentials\Users\Permissions;

use PortOneFive\Essentials\Users\Roles\Role;

trait HasPermissions
{
    /**
     * @param $permissionGroups
     *
     * @return bool
     */
    public function hasPermissionGroup($permissionGroups)
    {
        $userPermissionGroups = $this->getPermissionGroups();
        $orPermissionGroups   = explode('|', $permissionGroups);

        foreach ($orPermissionGroups as $permissionGroup) {
            $andPermissionGroups = explode(',', $permissionGroup);

            if (count(array_intersect($andPermissionGroups, $userPermissionGroups)) == count($andPermissionGroups)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getPermissionGroups()
    {
        static $permissionGroups = [];

        if (isset($permissionGroups[$this->id])) {
            return $permissionGroups[$this->id];
        }

        $permissionGroups[$this->id] = [];

        foreach ($this->roles as $role) {

            $permissionGroups[$this->id] = array_merge(
                $permissionGroups[$this->id],
                $role->permissions->lists('permission_group_id')->all()
            );
        }

        return $permissionGroups[$this->id] = array_unique($permissionGroups[$this->id]);
    }

    /**
     * @param $permission
     *
     * @return bool
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->getPermissions());
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        static $permissions = [];

        if (isset($permissions[$this->id])) {
            return $permissions[$this->id];
        }

        $permissions[$this->id] = [];

        foreach ($this->roles as $role) {
            $permissions[$this->id] = array_merge($permissions[$this->id], $role->permissions->lists('id')->all());
        }

        return $permissions[$this->id] = array_unique($permissions[$this->id]);
    }
}