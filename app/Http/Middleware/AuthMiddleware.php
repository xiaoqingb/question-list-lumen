<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;

class AuthMiddleware
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
        session_start();
        if(isset($_COOKIE['user'])){
            $_SESSION['id'] = explode('::',$_COOKIE['user'])[0];
            $_SESSION['name'] = explode('::',$_COOKIE['user'])[1];
        }
        if (!$_SESSION['name']){
            return[
                'code'=>'0001',
                'msg'=>'您还未登录'
            ];
        }

        return $next($request);
    }
}
