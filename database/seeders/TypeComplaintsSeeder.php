<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeComplaintsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('type_complaints')->insert([
            [
                'name' => 'Assédio',
                'description' => 'Denúncias relacionadas a assédio moral ou sexual.',
                'level' => 'Alto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Corrupção',
                'description' => 'Denúncias de práticas corruptas ou ilegais.',
                'level' => 'Alto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Discriminação',
                'description' => 'Denúncias relacionadas a discriminação por raça, gênero, orientação sexual ou idade.',
                'level' => 'Médio',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Violência Física',
                'description' => 'Denúncias de agressões físicas em ambiente de trabalho ou público.',
                'level' => 'Alto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fraude',
                'description' => 'Denúncias de fraudes financeiras, acadêmicas ou administrativas.',
                'level' => 'Alto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Violação de Dados',
                'description' => 'Denúncias relacionadas a vazamento ou uso indevido de informações pessoais.',
                'level' => 'Alto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Má Conduta Profissional',
                'description' => 'Denúncias de comportamento antiético ou incompetência profissional.',
                'level' => 'Médio',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Abuso de Poder',
                'description' => 'Denúncias de exploração ou uso indevido de autoridade.',
                'level' => 'Alto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Outros',
                'description' => 'Denúncias que não se enquadram nas categorias acima.',
                'level' => 'Baixo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
