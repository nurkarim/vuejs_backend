<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AdminAuth
{

    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('api')->check() && $request->user()->type == 0) {
            return $next($request);
        } else {
            $message = ["message" => "Permission Denied"];
            return response($message, 401);
        }
    }
}
