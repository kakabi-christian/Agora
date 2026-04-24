<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenExpiration
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

        if ($user) {
            $token = $user->currentAccessToken();
            
            if ($token && $token->last_used_at) {
                $lastUsed = $token->last_used_at;
                $twoHoursAgo = now()->subHours(2);

                if ($lastUsed->lt($twoHoursAgo)) {
                    $token->delete();
                    
                    return response()->json([
                        'message' => 'Votre session a expiré après 2 heures d\'inactivité. Veuillez vous reconnecter.'
                    ], 401);
                }
            }
        }

        return $next($request);
    }
}
