<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Pour une API, on ne redirige jamais vers une page de login
        // Le middleware retournera automatiquement une erreur 401
        if (! $request->expectsJson()) {
            return null;
        }
    }
}
