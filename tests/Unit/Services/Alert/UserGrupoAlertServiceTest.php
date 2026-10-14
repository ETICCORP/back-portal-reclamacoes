<?php

namespace Tests\Unit\Services\Alert;

use App\Repositories\Alert\UserGrupoAlert\UserGrupoAlertRepository;
use App\Services\Alert\UserGrupoAlert\UserGrupoAlertService;
use Exception;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

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
    public function test_sync_removes_users_omitted_from_the_submitted_group_list()
    {
        $groupId = 1;
        $existingInDb = collect([
            (object) ['user_id' => 23],
            (object) ['user_id' => 24],
            (object) ['user_id' => 25],
            (object) ['user_id' => 26],
        ]);
        $data = [
            ['grup_alert_id' => $groupId, 'user_id' => 23],
            ['grup_alert_id' => $groupId, 'user_id' => 24],
        ];

        DB::shouldReceive('transaction')->once()->andReturnUsing(fn ($callback) => $callback());

        $this->repositoryMock->shouldReceive('findBy')
            ->once()
            ->with(['grup_alert_id' => $groupId])
            ->andReturn($existingInDb);

        $this->repositoryMock->shouldReceive('removeUsersNotInGroup')
            ->once()
            ->with($groupId, [23, 24])
            ->andReturn(2);

        $result = $this->service->syncGroupUsers($data);

        $this->assertSame($groupId, $result['group_id']);
        $this->assertSame(4, $result['summary']['total_before']);
        $this->assertSame(2, $result['summary']['total_after']);
        $this->assertSame(2, $result['summary']['removed_count']);
        $this->assertSame(2, $result['summary']['kept_count']);
        $this->assertSame([], $result['details']['added']);
        $this->assertSame([25, 26], $result['details']['removed']);
        $this->assertSame([23, 24], $result['details']['kept']);
    }

    /**
     * Testa a exceção quando o ID do grupo não é fornecido.
     */
    public function test_should_throw_exception_when_group_id_missing()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Dados inválidos: id do grupo não encontrado.');

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

        DB::shouldReceive('transaction')->andReturnUsing(fn ($callback) => $callback());

        // Simula banco vazio
        $this->repositoryMock->shouldReceive('findBy')->andReturn(collect());

        $this->repositoryMock->shouldReceive('removeUsersNotInGroup')
            ->once()
            ->with($groupId, [10])
            ->andReturn(0);

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
