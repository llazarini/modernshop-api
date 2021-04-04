<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Get the URL
     * @param string $defaultUrl
     * @return mixed|string
     */
    protected function getRedirectUrl($defaultUrl = '/admin/dashboard', $id = 0) {
        $redirect = request()->get('redirect');

        if($redirect) {
            return str_replace('{id}', $id, $redirect);
        }

        return $defaultUrl;
    }
}
