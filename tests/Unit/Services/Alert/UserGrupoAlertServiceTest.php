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
    public function test_should_add_group_users_successfully_without_removing_existingones()
    {
        $groupId = 1;
        // Simula que no banco já existe o usuário 10 e 30
        $existingInDb = collect([
            (object)['user_id' => 10],
            (object)['user_id' => 30]
        ]);

        // Front envia o usuário 20 para adicionar. 
        // Os usuários 10 e 30 devem ser mantidos, e o 20 adicionado.
        $data = [
            ['grup_alert_id' => $groupId, 'user_id' => 20],
        ];

        DB::shouldReceive('transaction')->once()->andReturnUsing(fn($callback) => $callback());

        // 1. Mock da busca inicial
        $this->repositoryMock->shouldReceive('findBy')
            ->once()
            ->with(['grup_alert_id' => $groupId])
            ->andReturn($existingInDb);

        // 2. Mock do insert (deve inserir apenas o 20)
        $this->repositoryMock->shouldReceive('insertMany')
            ->once()
            ->with(Mockery::on(function ($payload) {
                return count($payload) === 1 && $payload[0]['user_id'] === 20;
            }))
            ->andReturn(true);

        $result = $this->service->syncGroupUsers($data);

        // Asserções do Resumo
        $this->assertIsArray($result);
        $this->assertEquals($groupId, $result['group_id']);
        $this->assertEquals(1, $result['summary']['added_count']);   // User 20
        $this->assertEquals(0, $result['summary']['removed_count']); // Nenhum removido
        $this->assertEquals(3, $result['summary']['total_after']);   // 10, 30 e 20
        
        // Asserções de Detalhes
        $this->assertContains(20, $result['details']['added']);
        $this->assertEmpty($result['details']['removed']);
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