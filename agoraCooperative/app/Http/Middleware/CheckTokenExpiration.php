<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
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
                        'message' => 'Votre session a expiré après 2 heures d\'inactivité. Veuillez vous reconnecter.',
                    ], 401);
                }
            }
        }

        return $next($request);
    }
}
