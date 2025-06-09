<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ApiAuthenticate;
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
            return response()->json(['error' => 'Credenciais incorretas'], 401);
        }

        return response()->json([
            'token' => $token,
        ]);
    }


    /**
     * Logout the current authenticated user and invalidate it's token.
     */
    public function logout()
    {
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
