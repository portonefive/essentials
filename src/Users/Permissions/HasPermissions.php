<?php namespace PortOneFive\Essentials\Users\Permissions;

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
                $role->permissions()->lists('permission_group_id')->all()
            );
        }

        return $permissionGroups[$this->id] = array_unique($permissionGroups[$this->id]);
    }

    /**
     * @param $permissions
     *
     * @return bool
     */
    public function can($permissions)
    {
        $userPermissions = $this->getPermissions();
        $orPermissions   = explode('|', $permissions);

        foreach ($orPermissions as $permission) {
            $andPermissions = explode(',', $permission);

            if (count(array_intersect($andPermissions, $userPermissions)) == count($andPermissions)) {
                return true;
            }
        }

        return false;
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
            $permissions[$this->id] = array_merge($permissions[$this->id], $role->permissions()->lists('id')->all());
        }

        return $permissions[$this->id] = array_unique($permissions[$this->id]);
    }

}