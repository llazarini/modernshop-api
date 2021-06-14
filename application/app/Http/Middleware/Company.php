<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Company
{
    public function handle(Request $request, Closure $next)
    {
        $company = \App\Models\Company::whereDomain($request->getHost())
            ->first();
        if (!$company) {
            return response()->json([
                'message' => __("Company not found")
            ], 400);
        }
        $request->request->add(['company_id' => $company->id]);
        return $next($request);
    }
}
