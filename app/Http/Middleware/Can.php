<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Can
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, ...$permissoes)
    {
        if (self::checkPermissions($permissoes)) {
            return $next($request);
        }

        // Se falhou, gera uma mensagem inteligente e amigável com base nas permissões exigidas
        $mensagemErro = self::getFriendlyErrorMessage($permissoes);

        throw new AccessDeniedHttpException($mensagemErro);
    }

    private function checkPermissions(array $permissions): bool
    {
        foreach ($permissions as $permissionSet) {
            if (str_contains($permissionSet, '|')) {
                $orPermissions = explode('|', $permissionSet);
                $hasAtLeastOne = false;

                foreach ($orPermissions as $permission) {
                    if (self::check($permission)) {
                        $hasAtLeastOne = true;
                        break;
                    }
                }

                if (!$hasAtLeastOne) {
                    return false; // Falhou na verificação "ou"
                }
            } else {
                // Verifica se o usuário tem a permissão individual
                if (!self::check($permissionSet)) {
                    return false;
                }
            }
        }

        return true; // O usuário possui todas as permissões necessárias
    }

    /**
     * Verifica se o usuário da requisição atual tem a permissão informada.
     */
    public static function check(?string $rule): bool
    {
        if ($rule == null) {
            return false;
        }

        if (Auth::user()) {
            return Auth::user()->can($rule);
        }

        return false;
    }

    /**
     * Analisa as permissões em falta e gera uma frase contextualizada.
     */
    private static function getFriendlyErrorMessage(array $permissions): string
    {
        // Se houver múltiplas regras separadas por vírgula no middleware, analisa a primeira que bloqueou
        $firstRuleSet = $permissions[0] ?? '';

        // Se for um grupo com operador "OU" (ex: usuario-edit|usuario-change-password), limpa para analisar
        if (str_contains($firstRuleSet, '|')) {
            $parts = explode('|', $firstRuleSet);
            $ruleToCheck = $parts[0]; // Avalia a operação base do grupo
        } else {
            $ruleToCheck = $firstRuleSet;
        }

        // Descobre se a string termina com uma das operações conhecidas
        if (str_ends_with($ruleToCheck, '-show')) {
            return 'Você não tem permissão para acessar este recurso.';
        }

        if (str_ends_with($ruleToCheck, '-create') || 
            str_ends_with($ruleToCheck, '-edit')   || 
            str_ends_with($ruleToCheck, '-delete') ||
            str_ends_with($ruleToCheck, '-change-password')) {
            return 'Você não tem permissão para realizar esta operação.';
        }

        // Mensagem genérica de fallback para segurança
        return 'Acesso negado. O seu perfil não possui os privilégios necessários.';
    }
}