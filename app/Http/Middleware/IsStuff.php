<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsStuff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()->check()&& (auth()->user()->hasRole('stuff') || auth()->user()->hasRole('admin'))){
            return $next($request);
        }
        return abort(401,'ليس لديك صلاحية بالوصول');

    }
}
