<?php

namespace App\Util\Helper;

use App\Models\User;

class CheckUser {

    public static function isTeacher() : bool
    {
        return (session('userType') === User::USER_TYPE_TEACHER);
    }

    public static function isStudent() : bool
    {
        return (session('userType') === User::USER_TYPE_STUDENT);
    }

}
