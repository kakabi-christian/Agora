<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMemberActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && !$user->est_actif) {
            return response()->json([
                'message' => 'Votre compte est désactivé. Veuillez contacter un administrateur.'
            ], 403);
        }

        return $next($request);
    }
}
