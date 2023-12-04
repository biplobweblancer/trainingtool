<?php

namespace App\Http\Middleware\TrainingMonitoring;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get th e path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {

        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
