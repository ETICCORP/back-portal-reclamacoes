<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proviver\Provider;
use App\Models\User\User;
use App\Models\Proviver\grupProveder\grupProveder;

class ProviderUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Criar ou encontrar o Provedor Default
        $provider = Provider::firstOrCreate(
            ['nif' => '000000000LA045'], // Critério de busca
            [
                'name'  => 'Provedor Padrão',
                'email' => 'admin@provedor.com',
                'phone' => '000000000'
            ]
        );

        // 2. Buscar todos os usuários
        $users = User::all();

        // 3. Associar cada usuário ao provedor
        foreach ($users as $user) {
            // Usamos firstOrCreate para evitar duplicatas caso o seeder rode duas vezes
            grupProveder::firstOrCreate([
                'proveder_id' => $provider->id,
                'user_id'     => $user->id,
            ]);
        }

        $this->command->info("Vinculados {$users->count()} usuários ao provedor: {$provider->name}");
    }
}