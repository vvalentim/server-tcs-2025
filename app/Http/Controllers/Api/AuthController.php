<?php

namespace App\Http\Controllers\Api;

use App\Http\Middleware\ApiAuthenticate;
use App\Models\OnlineUserSession;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller implements HasMiddleware
{
    /**
     * Get a JWT via given credentials.
     */
    public function login()
    {
        $token = Auth::attempt([
            'email' => request('email'),
            'password' => request('senha'),
        ]);

        if (!$token) {
            return response()->json(['mensagem' => 'Credenciais incorretas'], 401);
        }

        OnlineUserSession::create([
            'user_id' => Auth::id(),
            'token' => $token,
            'last_activity' => now()
        ]);

        return response()->json([
            'token' => $token,
        ]);
    }


    /**
     * Logout the current authenticated user and invalidate it's token.
     */
    public function logout()
    {
        $token = request()->bearerToken();

        if ($token) {
            $session = OnlineUserSession::where('token', $token)
                ->first();

            if ($session) {
                $session->delete();
            }
        }

        Auth::logout();

        return response()->json(['mensagem' => 'Logout realizado com sucesso']);
    }

    public static function middleware(): array
    {
        return [
            new Middleware(ApiAuthenticate::class, except: ['login']),
        ];
    }
}
