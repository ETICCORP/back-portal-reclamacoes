<?php

namespace Database\Factories;

use App\Enum\ClaimStatus;
use App\Models\Complaint\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'code'             => Complaint::generateCustomRandomCode(), // Usa o seu método inteligente de código
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
            'status'           => $this->faker->randomElement(ClaimStatus::cases()), // Sorteia um dos casos do seu Enum
            'created_at'       => now(),
            'updated_at'       => now(),
        ];
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
            'status' => ClaimStatus::PENDING, // Ajuste para o nome exato do seu case (ex: PENDING ou PENDENTE)
        ]);
    }
}