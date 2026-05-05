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
        // 1. Extração e Validação do ID do Grupo
        $grupAlertId = $data[0]['grup_alert_id'] ?? null;

        if (!$grupAlertId) {
            throw new \Exception("Dados inválidos: id do grupo não encontrado.");
        }

        return DB::transaction(function () use ($data, $grupAlertId) {
            $now = now();

            // 2. Limpeza do estado atual (Reutilizando Repository)
            $this->repository->forceDeleteBy('grup_alert_id', $grupAlertId);

            // 3. Preparação do novo estado (Payload)
            $payload = collect($data)
                ->map(function ($item) use ($grupAlertId, $now) {
                    return [
                        'grup_alert_id' => $grupAlertId,
                        'user_id'       => $item['user_id'],
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                })
                ->unique('user_id') // Evita erro de duplicidade no banco
                ->toArray();

            // 4. Inserção em Massa (Reutilizando Repository)
            if (empty($payload)) {
                return true;
            }

            return $this->repository->insertMany($payload);
        });
    }
}
