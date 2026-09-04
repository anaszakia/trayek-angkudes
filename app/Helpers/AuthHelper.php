<?php

if (!function_exists('authUser')) {
    function authUser()
    {
        static $user = null;
        static $loadedUserId = null;
        $sessionUserId = session('user_id');

        if ($loadedUserId !== $sessionUserId) {
            $loadedUserId = $sessionUserId;
            $user = $sessionUserId
                ? \App\Models\User::with('roles.permissions', 'passkeys')->find($sessionUserId)
                : null;
        }

        return $user;
    }
}

if (!function_exists('can')) {
    function can(string $permission): bool
    {
        return authUser()?->hasPermission($permission) ?? false;
    }
}