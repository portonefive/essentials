<?php namespace PortOneFive\Essentials\Users\Roles;

trait HasRoles
{
    /**
     * @param $roles
     *
     * @return bool
     */
    public function isRole($roles)
    {
        $userRoles = $this->roles->pluck('id')->all();
        $orRoles   = explode('|', $roles);

        foreach ($orRoles as $role) {
            $andRoles = explode(',', $role);

            if (count(array_intersect($andRoles, $userRoles)) == count($andRoles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id');
    }

}
