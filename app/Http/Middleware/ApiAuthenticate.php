<?php

namespace App\Http\Middleware;

use App\Models\OnlineUserSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('api')->check()) {
            return response()->json(['mensagem' => 'Usuario não autorizado'], 401);
        }

        $token = request()->bearerToken();

        if ($token) {
            $user = OnlineUserSession::where('token', $token)
                ->first();

            if ($user) {
                $user->last_activity = now();
                $user->save();
            }
        }

        return $next($request);
    }
}
