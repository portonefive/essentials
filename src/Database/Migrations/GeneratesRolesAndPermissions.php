<?php namespace PortOneFive\Essentials\Database\Migrations;

use PortOneFive\Essentials\Users\Permissions\PermissionGroup;
use PortOneFive\Essentials\Users\Roles\Role;

trait GeneratesRolesAndPermissions
{
    protected function createRolesAndPermissions()
    {
        $this->createRoles();
        $this->createPermissionGroups();
        $this->createPermissions();
        $this->assignPermissionsToRoles();
    }

    protected function dropRolesAndPermissions()
    {
        $this->dropPermissions();
        $this->dropPermissionGroups();
        $this->dropRoles();
    }

    protected function createRoles()
    {
        if (empty($this->roles)) {
            return;
        }

        foreach ($this->roles as $id => $title) {
            \PortOneFive\Essentials\Users\Roles\Role::forceCreate(['id' => $id, 'title' => $title]);
        }
    }

    protected function dropRoles()
    {
        if (empty($this->roles)) {
            return;
        }

        \PortOneFive\Essentials\Users\Roles\Role::destroy(array_keys($this->roles));
    }

    protected function createPermissionGroups()
    {
        if (empty($this->permissionGroups)) {
            return;
        }

        foreach ($this->permissionGroups as $id => $title) {
            \PortOneFive\Essentials\Users\Permissions\PermissionGroup::forceCreate(['id' => $id, 'title' => $title]);
        }
    }

    protected function dropPermissionGroups()
    {
        if (empty($this->permissionGroups)) {
            return;
        }

        \PortOneFive\Essentials\Users\Permissions\PermissionGroup::destroy(array_keys($this->permissionGroups));
    }


    protected function createPermissions()
    {
        if (empty($this->permissions)) {
            return;
        }

        foreach ($this->permissions as $groupId => $permissions) {

            /** @var PermissionGroup $permissionGroup */
            $permissionGroup = \PortOneFive\Essentials\Users\Permissions\PermissionGroup::findOrFail($groupId);

            foreach ($permissions as $id => $title) {
                $permissionGroup->permissions()->create(['id' => $id, 'title' => $title]);
            }
        }
    }

    protected function dropPermissions()
    {
        if (empty($this->permissions)) {
            return;
        }

        foreach ($this->permissions as $groupId => $permissions) {
            \PortOneFive\Essentials\Users\Permissions\Permission::destroy(array_keys($permissions));
        }
    }

    protected function assignPermissionsToRoles()
    {
        if (empty($this->permissionRoles)) {
            return;
        }

        foreach ($this->permissionRoles as $roleId => $permissionIds) {
            /** @var Role $role */
            $role = \PortOneFive\Essentials\Users\Roles\Role::findOrFail($roleId);
            $role->permissions()->attach($permissionIds);
        }
    }
}
