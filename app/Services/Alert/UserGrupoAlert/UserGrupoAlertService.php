<?php

namespace App\Services\Alert\UserGrupoAlert;

use App\Repositories\Alert\UserGrupoAlert\UserGrupoAlertRepository;
use App\Services\AbstractService;
use Illuminate\Support\Facades\DB;

class UserGrupoAlertService extends AbstractService
{
    public function __construct(UserGrupoAlertRepository $repository)
    {
        parent::__construct($repository);
    }

    public function storeMany(array $data)
    {
        return $this->repository->storeMany($data);
    }

    /**
     * Sincroniza os usuários do grupo (Lógica unificada para Store e Update)
     */
    public function syncGroupUsers(array $data)
    {
        $grupAlertId = $data[0]['grup_alert_id'] ?? null;

        if (!$grupAlertId) {
            throw new \Exception("Dados inválidos: id do grupo não encontrado.");
        }

        // 1. Obter IDs atuais (Estado Atual)
        $currentIds = $this->repository->findBy(['grup_alert_id' => $grupAlertId])
            ->pluck('user_id')
            ->toArray();

        // 2. IDs desejados (Estado Final vindo do Front)
        $newIds = collect($data)->pluck('user_id')->filter()->unique()->toArray();

        // 3. Cálculo do Diferencial (Diff)
        $toAdd    = array_values(array_diff($newIds, $currentIds));    // Novos
        $toRemove = array_values(array_diff($currentIds, $newIds));   // Excluídos
        $kept     = array_values(array_intersect($currentIds, $newIds)); // Mantidos

        return DB::transaction(function () use ($grupAlertId, $newIds, $toAdd, $toRemove, $kept, $currentIds) {
            $now = now();

            // 4. Remover apenas quem saiu (Otimização de Performance)
            if (!empty($toRemove)) {
                $this->repository->getModel()
                    ::where('grup_alert_id', $grupAlertId)
                    ->whereIn('user_id', $toRemove)
                    ->forceDelete();
            }

            // 5. Inserir apenas quem entrou
            if (!empty($toAdd)) {
                $payload = collect($toAdd)->map(fn($userId) => [
                    'grup_alert_id' => $grupAlertId,
                    'user_id'       => $userId,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ])->toArray();

                $this->repository->insertMany($payload);
            }

            // 6. Retorno preciso do que aconteceu
            return [
                'group_id' => $grupAlertId,
                'summary' => [
                    'total_before'  => count($currentIds),
                    'total_after'   => count($newIds),
                    'added_count'   => count($toAdd),
                    'removed_count' => count($toRemove),
                    'kept_count'    => count($kept),
                ],
                'details' => [
                    'added'   => $toAdd,
                    'removed' => $toRemove,
                    'kept'    => $kept
                ]
            ];
        });
    }
}
