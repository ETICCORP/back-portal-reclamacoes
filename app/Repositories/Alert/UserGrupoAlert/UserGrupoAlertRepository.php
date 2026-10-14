<?php

namespace App\Repositories\Alert\UserGrupoAlert;

use App\Models\Alert\UserGrupoAlert\UserGrupoAlert;
use App\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;

class UserGrupoAlertRepository extends AbstractRepository
{
    public function __construct(UserGrupoAlert $model)
    {
        parent::__construct($model);
    }

    public function storeMany($data)
    {
        $now = now();

        // insere na pivot alert_user
        $inserted = $this->model->insert(
            collect($data)->map(function ($item) use ($now) {
                return array_merge($item, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            })->toArray()
        );

        return $inserted;
    }

    public function removeUsersNotInGroup(int $grupAlertId, array $userIds): int
    {
        return $this->model->newQuery()
            ->withTrashed()
            ->where('grup_alert_id', $grupAlertId)
            ->where(function (Builder $query) use ($userIds): void {
                $query->whereNotIn('user_id', $userIds)
                    ->orWhereNotNull('deleted_at');
            })
            ->forceDelete();
    }
}
