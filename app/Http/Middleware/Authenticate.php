<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        // MultiAuthGuards

        if (!Auth::guard("cashier")->check()) {
            return route('cashier.login');
        }


        if (!Auth::guard("management")->check()) {
            return route('management.login');
        }

        
        if (!Auth::guard("admin")->check()) {
            return route('admin.login');
        }


        if (!Auth::guard("web")->check()) {
            return route('login');
        }
        return route('login');
    }
}
