<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Company
{
    public function handle(Request $request, Closure $next)
    {
        /*
        $domain = request()->headers->get('referer');
        $domain = parse_url($domain)['host'];
        $company = \App\Models\Company::whereDomain($domain)
            ->first();
        if (!$company) {
            return response()->json([
                'aaa' => $request->root(),
                'message' => __("Empresa com domínio :domain não encontrado.", [
                    'domain' => $domain
                ], )
            ], 400);
        }*/
        $request->request->add(['company_id' => 1]);
        return $next($request);
    }
}
