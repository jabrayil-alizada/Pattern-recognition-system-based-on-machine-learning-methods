<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class UserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userType = session('userType');

        if($userType && $userType === User::USER_TYPE_TEACHER)
        {
            return $next($request);
        }

        $data = [
            "data" => "Только учитель может создавать тест"
        ];

        return redirect('/')->with(["data" => $data]);
    }
}
