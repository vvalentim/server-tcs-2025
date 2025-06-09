<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ApiAuthenticate;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Store a new user.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['nome'],
            'email' => $validated['email'],
            'password' => $validated['senha']
        ]);

        if (!$user) {
            return response()->json([
                'mensagem' => 'Erro interno do servidor',
                'erro' => 'Não foi possível criar o usuário',
            ], 500);
        }

        return response()->json(['mensagem' => 'Sucesso ao cadastrar usuário'], 201);
    }

    /**
     * Show the profile for the current authenticated user.
     */
    public function show()
    {
        $user = User::find(Auth::id());

        return response()->json([
            'mensagem' => 'Successo ao buscar usuário',
            'usuario' => [
                'nome' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    /**
     * Edit the current authenticated user.
     */
    public function edit(EditUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::find(Auth::id());

        $user->update([
            'name' => $validated['nome'],
            'password' => $validated['senha']
        ]);

        return response()->json([
            'mensagem' => 'Successo ao salvar o usuário',
            'usuario' => [
                'nome' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    /**
     * Delete the current authenticated user.
     */
    public function destroy()
    {
        $user = User::find(Auth::id());

        $user->delete();

        Auth::logout();

        return response()->json(['mensagem' => 'Sucesso ao excluir usuário'], 200);
    }

    /**
     * List all users (including soft deleted).
     */
    public function list()
    {
        return User::withTrashed()->get();
    }

    public static function middleware(): array
    {
        return [
            new Middleware(ApiAuthenticate::class, except: ['store', 'list']),
        ];
    }
}
