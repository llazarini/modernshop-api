<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequestUniqueId
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
        $request->request->add(['request_token' => md5(time() . rand(1,1000) . rand(1,1000))]);
        return $next($request);
    }
}
