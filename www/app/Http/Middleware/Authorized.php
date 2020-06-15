<?php

namespace App\Http\Middleware;

use App\Util\Helper\Response;
use Closure;

class Authorized
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
        $userId = session('userId');

        if(!$userId)
        {
            $data = [
                "data" => "Необходимо авторизоваться"
            ];

            return redirect('user/auth')->with(["data" => $data]);
        }

        return $next($request);
    }
}
