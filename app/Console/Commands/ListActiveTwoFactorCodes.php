<?php

namespace App\Console\Commands;

use App\Models\User\User;
use Illuminate\Console\Command;

class ListActiveTwoFactorCodes extends Command
{
    /**
     * O nome e a assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'user:2fa-codes';

    /**
     * A descrição do comando.
     *
     * @var string
     */
    protected $description = 'Lista todos os e-mails e códigos 2FA (OTP) que ainda estão ativos e dentro da validade';

    /**
     * Executa o comando no console.
     */
    public function handle()
    {
        // Busca usuários onde o código não é nulo e a data de expiração está no futuro
        $users = User::whereNotNull('two_factor_code')
            ->where('two_factor_expires_at', '>', now())
            ->get(['first_name', 'last_name', 'email', 'two_factor_code', 'two_factor_expires_at']);

        if ($users->isEmpty()) {
            $this->info('Nenhum código 2FA ativo encontrado no momento.');
            return self::SUCCESS;
        }

        // Prepara os cabeçalhos da tabela
        $headers = ['Nome', 'E-mail', 'Código OTP', 'Expira em'];

        // Mapeia os dados para o formato de exibição da tabela
        $data = $users->map(function ($user) {
            return [
                'name'       => $user->first_name . ' ' . $user->last_name,
                'email'      => $user->email,
                'code'       => $user->two_factor_code,
                'expires_at' => $user->two_factor_expires_at->format('d/m/Y H:i:s'),
            ];
        });

        // Renderiza a tabela lindamente no terminal
        $this->table($headers, $data);

        return self::SUCCESS;
    }
}