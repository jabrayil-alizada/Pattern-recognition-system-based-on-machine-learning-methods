<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    const USER_TYPE_TEACHER = 'teacher';

    const USER_TYPE_STUDENT = 'student';

    protected $table = 'users';

    protected $guarded = [];
}
