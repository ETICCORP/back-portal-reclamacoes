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
    public function syncGroupUsers(array $data): array
    {
        $grupAlertId = $data[0]['grup_alert_id'] ?? null;

        if (! $grupAlertId) {
            throw new \Exception('Dados inválidos: id do grupo não encontrado.');
        }

        return DB::transaction(function () use ($grupAlertId, $data) {
            $currentIds = $this->repository->findBy(['grup_alert_id' => $grupAlertId])
                ->pluck('user_id')
                ->toArray();

            $inputIds = collect($data)->pluck('user_id')->filter()->unique()->values()->toArray();
            $toAdd = array_values(array_diff($inputIds, $currentIds));
            $toRemove = array_values(array_diff($currentIds, $inputIds));
            $kept = array_values(array_intersect($currentIds, $inputIds));
            $finalIds = $inputIds;

            $now = now();

            if ($toRemove !== []) {
                $this->repository->removeUsersFromGroup($grupAlertId, $toRemove);
            }

            if ($toAdd !== []) {
                $payload = collect($toAdd)->map(fn ($userId) => [
                    'grup_alert_id' => $grupAlertId,
                    'user_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->toArray();

                $this->repository->insertMany($payload);
            }

            return [
                'group_id' => $grupAlertId,
                'summary' => [
                    'total_before' => count($currentIds),
                    'total_after' => count($finalIds),
                    'added_count' => count($toAdd),
                    'removed_count' => count($toRemove),
                    'kept_count' => count($kept),
                ],
                'details' => [
                    'added' => $toAdd,
                    'removed' => $toRemove,
                    'kept' => $kept,
                ],
            ];
        });
    }
}
