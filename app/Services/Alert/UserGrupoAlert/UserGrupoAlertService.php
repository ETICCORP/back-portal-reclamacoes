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

        // 1. Obter IDs atuais no banco antes de deletar
        $currentIds = $this->repository->findBy(['grup_alert_id' => $grupAlertId])
            ->pluck('user_id')
            ->toArray();

        // 2. IDs enviados pelo Front-end (sanitizados)
        $newIds = collect($data)->pluck('user_id')->unique()->toArray();

        // 3. Cálculo do Diff
        $toAdd    = array_diff($newIds, $currentIds);    // Está no novo, mas não no banco
        $toRemove = array_diff($currentIds, $newIds);   // Está no banco, mas não no novo
        $kept     = array_intersect($currentIds, $newIds); // Está em ambos

        return DB::transaction(function () use ($grupAlertId, $newIds, $toAdd, $toRemove, $kept) {
            $now = now();

            // 4. Execução no Banco
            $this->repository->forceDeleteBy('grup_alert_id', $grupAlertId);

            $payload = collect($newIds)->map(fn($userId) => [
                'grup_alert_id' => $grupAlertId,
                'user_id'       => $userId,
                'created_at'    => $now,
                'updated_at'    => $now,
            ])->toArray();

            if (!empty($payload)) {
                $this->repository->insertMany($payload);
            }

            // 5. Resposta clara e rica
            return [
                'group_id' => $grupAlertId,
                'summary' => [
                    'total_before' => count($toRemove) + count($kept),
                    'total_after'  => count($newIds),
                    'added_count'  => count($toAdd),
                    'removed_count' => count($toRemove),
                    'kept_count'    => count($kept),
                ],
                'details' => [
                    'added'   => array_values($toAdd),
                    'removed' => array_values($toRemove),
                    'kept'    => array_values($kept)
                ]
            ];
        });
    }
}
