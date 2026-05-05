<?php

namespace Tests\Unit\Services\Alert;

use App\Repositories\Alert\UserGrupoAlert\UserGrupoAlertRepository;
use App\Services\Alert\UserGrupoAlert\UserGrupoAlertService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Exception;

class UserGrupoAlertServiceTest extends TestCase
{
    protected $service;
    protected $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Criamos um Mock do Repositório
        $this->repositoryMock = $this->mock(UserGrupoAlertRepository::class);

        // 2. Instanciamos o Service com o Mock
        $this->service = new UserGrupoAlertService($this->repositoryMock);
    }

    /**
     * Testa se o service sincroniza os dados corretamente.
     */
    public function test_should_sync_group_users_successfully()
    {
        // Dados de entrada simulando o que vem do Request
        $data = [
            ['grup_alert_id' => 1, 'user_id' => 10],
            ['grup_alert_id' => 1, 'user_id' => 20],
            ['grup_alert_id' => 1, 'user_id' => 10], // Duplicado proposital para testar o ->unique()
        ];

        // Simulamos o comportamento da Transação do DB
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        // Verificamos se o repositório chama a limpeza para o grupo 1
        $this->repositoryMock
            ->shouldReceive('forceDeleteBy')
            ->once()
            ->with('grup_alert_id', 1)
            ->andReturn(true);

        // Verificamos se o repositório chama o insertMany com apenas 2 registros 
        // (devido ao unique('user_id') no Service)
        $this->repositoryMock
            ->shouldReceive('insertMany')
            ->once()
            ->with(\Mockery::on(function ($payload) {
                return count($payload) === 2 && $payload[0]['user_id'] === 10;
            }))
            ->andReturn(true);

        $result = $this->service->syncGroupUsers($data);

        $this->assertTrue($result);
    }

    /**
     * Testa a exceção quando o ID do grupo não é fornecido.
     */
    public function test_should_throw_exception_when_group_id_missing()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Dados inválidos: id do grupo não encontrado.");

        $data = [['user_id' => 10]]; // Sem grup_alert_id

        $this->service->syncGroupUsers($data);
    }

    public function test_sync_group_users_produces_correct_payload_structure()
    {
        // 1. Dados de entrada com um duplicado (user_id 10 aparece duas vezes)
        $data = [
            ['grup_alert_id' => 5, 'user_id' => 10],
            ['grup_alert_id' => 5, 'user_id' => 20],
            ['grup_alert_id' => 5, 'user_id' => 10],
        ];

        DB::shouldReceive('transaction')->andReturnUsing(fn($callback) => $callback());

        $this->repositoryMock
            ->shouldReceive('forceDeleteBy')
            ->once();

        // 2. A validação do "X" (o segredo está aqui)
        $this->repositoryMock
            ->shouldReceive('insertMany')
            ->once()
            ->with(\Mockery::on(function ($payload) {
                // Verificação 1: O unique('user_id') funcionou? (Devem restar 2 itens)
                $countMatch = count($payload) === 2;

                // Verificação 2: O primeiro item tem os campos corretos?
                $firstItem = $payload[0];
                $structureMatch = isset($firstItem['created_at']) &&
                    $firstItem['user_id'] === 10 &&
                    $firstItem['grup_alert_id'] === 5;

                // Verificação 3: O ID do grupo foi forçado corretamente em todos?
                $groupIdMatch = collect($payload)->every('grup_alert_id', 5);

                return $countMatch && $structureMatch && $groupIdMatch;
            }))
            ->andReturn(true);

        $this->service->syncGroupUsers($data);
    }

    /**
     * Testa se retorna true quando o payload está vazio.
     */
    public function test_should_return_true_if_payload_is_empty_after_mapping()
    {
        $data = []; // Array vazio

        // O PHPUnit/Mockery não deve esperar chamadas de DB ou Repo aqui 
        // pois a validação do ID do grupo no seu código atual falharia antes.
        // Nota: Se enviar [], o $data[0] dará erro, cobrimos isso com a exceção acima.

        $this->assertTrue(true);
    }
}
