<?php

namespace Tests\Unit\Services\Alert;

use App\Repositories\Alert\UserGrupoAlert\UserGrupoAlertRepository;
use App\Services\Alert\UserGrupoAlert\UserGrupoAlertService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Exception;
use Mockery;

class UserGrupoAlertServiceTest extends TestCase
{
    protected $service;
    protected $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->mock(UserGrupoAlertRepository::class);
        $this->service = new UserGrupoAlertService($this->repositoryMock);
    }

    /**
     * Testa se o service sincroniza os dados corretamente e retorna o resumo detalhado.
     */
    public function test_should_sync_group_users_successfully_and_return_detailed_summary()
    {
        $groupId = 1;
        // Simula que no banco já existe o usuário 10 e 30
        $existingInDb = collect([
            (object)['user_id' => 10],
            (object)['user_id' => 30]
        ]);

        // Front envia 10 (mantido), 20 (novo) e um 10 duplicado. 
        // O usuário 30 deve ser removido.
        $data = [
            ['grup_alert_id' => $groupId, 'user_id' => 10],
            ['grup_alert_id' => $groupId, 'user_id' => 20],
            ['grup_alert_id' => $groupId, 'user_id' => 10], 
        ];

        DB::shouldReceive('transaction')->once()->andReturnUsing(fn($callback) => $callback());

        // 1. Mock da busca inicial para o Diff
        $this->repositoryMock->shouldReceive('findBy')
            ->once()
            ->with(['grup_alert_id' => $groupId])
            ->andReturn($existingInDb);

        // 2. Mock da limpeza
        $this->repositoryMock->shouldReceive('forceDeleteBy')
            ->once()
            ->with('grup_alert_id', $groupId)
            ->andReturn(true);

        // 3. Mock do insert (deve inserir apenas 10 e 20, sem duplicatas)
        $this->repositoryMock->shouldReceive('insertMany')
            ->once()
            ->with(Mockery::on(function ($payload) {
                return count($payload) === 2; // User 10 e 20
            }))
            ->andReturn(true);

        $result = $this->service->syncGroupUsers($data);

        // Asserções do Resumo
        $this->assertIsArray($result);
        $this->assertEquals($groupId, $result['group_id']);
        $this->assertEquals(1, $result['summary']['added_count']);   // User 20
        $this->assertEquals(1, $result['summary']['removed_count']); // User 30
        $this->assertEquals(1, $result['summary']['kept_count']);    // User 10
        
        // Asserções de Detalhes
        $this->assertContains(20, $result['details']['added']);
        $this->assertContains(30, $result['details']['removed']);
        $this->assertContains(10, $result['details']['kept']);
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

    /**
     * Testa se o payload interno enviado ao banco está correto (timestamps e IDs).
     */
    public function test_sync_group_users_produces_correct_payload_structure()
    {
        $groupId = 5;
        $data = [['grup_alert_id' => $groupId, 'user_id' => 10]];

        DB::shouldReceive('transaction')->andReturnUsing(fn($callback) => $callback());
        
        // Simula banco vazio
        $this->repositoryMock->shouldReceive('findBy')->andReturn(collect());
        $this->repositoryMock->shouldReceive('forceDeleteBy')->once();

        $this->repositoryMock->shouldReceive('insertMany')
            ->once()
            ->with(Mockery::on(function ($payload) use ($groupId) {
                $item = $payload[0];
                return $item['user_id'] === 10 && 
                       $item['grup_alert_id'] === $groupId && 
                       isset($item['created_at']);
            }))
            ->andReturn(true);

        $this->service->syncGroupUsers($data);
    }

    /**
     * Testa se o código lida corretamente com o envio de uma lista vazia 
     * (deve remover todos os usuários atuais).
     */
    public function test_should_return_error_on_empty_array_due_to_missing_id()
    {
        // Como o seu código busca o ID em $data[0], um array vazio dispara a exceção de ID não encontrado.
        $this->expectException(Exception::class);
        
        $this->service->syncGroupUsers([]);
    }
}