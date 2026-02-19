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
  

    DB::table('type_complaints')->truncate(); // opcional

    DB::table('type_complaints')->insert([
        [
            'name' => 'Informação insuficiente ou pouco clara',
            'description' => 'Falta de clareza ou insuficiência de informações prestadas ao cliente.',
            'level' => 'Baixo',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Erro nos dados da apólice',
            'description' => 'Erros nos dados constantes da apólice.',
            'level' => 'Médio',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Coberturas incorrectas',
            'description' => 'Coberturas contratadas que não correspondem ao acordado.',
            'level' => 'Alto',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Alteração unilateral de condições',
            'description' => 'Alteração de condições contratuais sem consentimento do cliente.',
            'level' => 'Alto',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Cancelamento indevido',
            'description' => 'Cancelamento da apólice sem fundamento legal.',
            'level' => 'Alto',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Dificuldade na actualização de dados',
            'description' => 'Entraves na actualização de dados do segurado.',
            'level' => 'Baixo',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Débito indevido',
            'description' => 'Cobrança realizada sem autorização ou base contratual.',
            'level' => 'Alto',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Cobrança em duplicado',
            'description' => 'Cobrança repetida do mesmo valor.',
            'level' => 'Médio',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Cálculo incorrecto do prémio',
            'description' => 'Erro no cálculo do valor do prémio do seguro.',
            'level' => 'Médio',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Falta de emissão de recibo',
            'description' => 'Não emissão de recibo após pagamento.',
            'level' => 'Baixo',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Recusa de indemnização',
            'description' => 'Recusa indevida de pagamento de indemnização.',
            'level' => 'Alto',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Valor indemnizatório insuficiente',
            'description' => 'Indemnização paga em valor inferior ao devido.',
            'level' => 'Alto',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Exigência excessiva de documentos',
            'description' => 'Solicitação excessiva ou desproporcional de documentos.',
            'level' => 'Médio',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Falta de comunicação sobre o estado do processo',
            'description' => 'Ausência de comunicação sobre o andamento do processo.',
            'level' => 'Médio',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Encerramento injustificado do processo',
            'description' => 'Encerramento do processo sem justificação válida.',
            'level' => 'Alto',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Outro',
            'description' => 'Outras situações não especificadas.',
            'level' => 'Baixo',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Situação não enquadrada nas categorias anteriores',
            'description' => 'Situações fora das categorias existentes.',
            'level' => 'Baixo',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
}

    
}
