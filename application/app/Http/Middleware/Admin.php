<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Admin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user->user_type->slug !== 'admin') {
            return response()->json([
                'message' => __('Você não tem acesso à essa área.')
            ], 400);
        }
        return $next($request);
    }
}
