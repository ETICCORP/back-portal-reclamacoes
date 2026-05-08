<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsActive
{
    /**
     * Manipula a requisição.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        logs()->info('Verificando se o usuário está ativo', [
            'user_id' => $user ? $user->id : null,
            'email' => $user ? $user->email : null,
            'is_active' => $user ? $user->is_active : null,
        ]);

        // 1. Verifica se existe usuário logado e se ele está inativo
        // Usamos cast (bool) para garantir a comparação correta
        if ($user && !(bool) $user->is_active) {

            // 2. Ações de Segurança: Desloga o usuário imediatamente
            if ($request->bearerToken()) {
                // revoga o token atual
                $user->currentAccessToken()->delete();
                $message = 'Sua conta está inativada. Entre em contato com o suporte.';

                return response()->json([
                    'error' => 'Conta inativa, token revogado',
                    'message' => $message,
                    'code' => 403
                ], 403);
            }
        }

        return $next($request);
    }
}
