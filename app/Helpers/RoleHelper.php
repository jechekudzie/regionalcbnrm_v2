<?php

// app/Helpers/RoleHelper.php
use App\Models\User;
use App\Models\Organisation;

if (!function_exists('getFirstCommonRoleWithOrganization')) {
    function getFirstCommonRoleWithOrganization(User $user, Organisation $organisation)
    {
        return $user->roles->first(function ($userRole) use ($organisation) {
            if (!empty($userRole->name)) {
                return $organisation->roles->contains('name', $userRole->name);
            }
        });
    }
}

