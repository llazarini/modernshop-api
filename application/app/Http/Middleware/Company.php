<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Company
{
    public function handle(Request $request, Closure $next)
    {
        $company = \App\Models\Company::whereDomain($request->header('Site'))
            ->first();
        if (!$company) {
            return response()->json([
                'message' => __("Empresa com domínio :domain não encontrado.", [
                    'domain' => $request->header('Site')
                ])
            ], 400);
        }
        $request->request->add(['company_id' => $company->id]);
        return $next($request);
    }
}
