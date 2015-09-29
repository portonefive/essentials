<?php

namespace PortOneFive\Essentials\Auth\Access;

use Illuminate\Auth\Access\Gate as BaseGate;

class Gate extends BaseGate
{

    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param  string      $ability
     * @param  array|mixed $arguments
     *
     * @return bool
     */
    public function check($ability, $arguments = [])
    {
        if ( ! $user = $this->resolveUser()) {
            return false;
        }

        $arguments = is_array($arguments) ? $arguments : [$arguments];

        foreach (explode('|', $ability) as $orPermission) {
            $andPermissions = explode(',', $orPermission);

            foreach ($andPermissions as $andKey => $andPermission) {
                if ($this->checkAbility($andPermission, $arguments, $user)) {
                    unset($andPermissions[$andKey]);
                }
            }

            if (count($andPermissions) == 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $ability
     * @param $arguments
     * @param $user
     *
     * @return bool|mixed|void
     */
    protected function checkAbility($ability, $arguments, $user)
    {
        if ( ! is_null($result = $this->callBeforeCallbacks($user, $ability, $arguments))) {
            return $result;
        }

        $callback = $this->resolveAuthCallback($user, $ability, $arguments);

        return call_user_func_array($callback, array_merge([$user], $arguments));
    }
}