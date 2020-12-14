<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SuperAdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('api')->check() && $request->user()->user_type == 1) {
            return $next($request);
        } else {
            $message = ["message" => "Permission Denied"];
            return response($message, 401);
        }
    }
}
