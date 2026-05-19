<?php

namespace Database\Factories;

use App\Enum\ClaimStatus;
use App\Models\Complaint\Complaint;
use App\Models\Complaint\ComplaintDeadline;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Complaint\Complaint>
 */
class ComplaintFactory extends Factory
{
    /**
     * O nome da model correspondente à Factory.
     *
     * @var string
     */
    protected $model = Complaint::class;

    /**
     * Define state padrão para o modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code'             => Complaint::generateCustomRandomCode(), // Usa o método inteligente de código
            'user_id'          => User::inRandomOrder()->first()?->id ?? User::factory(), // Associa a um User existente ou cria um novo
            'policy_number'    => $this->faker->optional(0.7)->bothify('POL-######-??'), // 70% de chance de ter apólice
            'source'           => $this->faker->randomElement(['Portal Web', 'E-mail', 'Presencial', 'Linha de Apoio']),
            'received_at'      => $this->faker->dateTimeBetween('-1 month', 'now'),
            'full_name'        => $this->faker->name(),
            'complainant_role' => $this->faker->randomElement(['Segurado', 'Beneficiário', 'Terceiro', 'Tomador de Seguro']),
            'contact'          => $this->faker->phoneNumber(),
            'email'            => 'carlos.calulo@etic.co.ao',
            'entity'           => $this->faker->company(),
            'description'      => $this->faker->paragraph(3),
            'incidentDateTime' => $this->faker->dateTimeBetween('-2 months', '-2 days')->format('Y-m-d H:i:s'),
            'location'         => $this->faker->city() . ', Angola',
            'type'             => $this->faker->randomElement([1, 2, 3]), // Substitua pelos IDs reais da sua tabela 'type_complaints'
            'representative'   => $this->faker->optional(0.3)->name(), // 30% de chance de ter um representante legal
            'status'           => ClaimStatus::PENDENTE_PT, // Sorteia um dos casos do seu Enum
            'created_at'       => now(),
            'updated_at'       => now(),
        ];
    }

    /**
     * Configura os ganchos (hooks) de ciclo de vida da Factory.
     * Vincula a criação do Prazo de Resposta automaticamente após a Reclamação persistir na BD.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Complaint $complaint) {
            
            // Definimos as datas baseadas na data de receção da reclamação gerada pela factory
            $startDate = Carbon::parse($complaint->received_at);
            
            // Simulação rápida de 15 dias úteis para ambiente de testes locais
            $endDate = $startDate->copy()->addWeekdays(15); 

            // Criamos o registo de prazo espelhado na base de dados
            ComplaintDeadline::create([
                'complaint_id' => $complaint->id,
                'days'         => 15,
                'start_date'   => $startDate,
                'end_date'     => $endDate,
                'status'       => 'Pendente',
                'notified_at'  => null,
            ]);
        });
    }

    /**
     * State específico: Forçar o estado como "Devolvida ao Reclamante" para facilitar os seus testes locais.
     */
    public function devolvida(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ClaimStatus::DEVOLVIDA_RECLAMANTE,
        ]);
    }   

    /**
     * State específico: Forçar o estado como "Pendente".
     */
    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ClaimStatus::PENDING ?? ClaimStatus::PENDENTE_PT, // Fallback dinâmico para o teu Enum
        ]);
    }
}