<?php

namespace App\Repositories\Alert\AlertUser;

use App\Models\Alert\AlertUser\AlertUser;
use App\Repositories\AbstractRepository;
use Illuminate\Http\JsonResponse;

class AlertUserRepository extends AbstractRepository
{
    public function __construct(AlertUser $model)
    {
        parent::__construct($model);
    }

    public function countActiveAlertsByUser($userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->whereHas('alert', function ($q) {
                $q->where('is_read', 1);
            })
            ->count();
    }

    public function countInactiveAlertsByUser($userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->whereHas('alert', function ($q) {
                $q->where('is_read', 0);
            })
            ->count();
    }
    public function getUsersWithAlerts()
    {
        return $this->model
            ->distinct()
            ->pluck('user_id');
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

        // dispara evento em tempo real para cada utilizador
        foreach ($data as $item) {
            $user = \App\Models\User::find($item['user_id']);
            if ($user) {
                event(new \App\Events\AlertCreated($user));
            }
        }

        return $inserted;
    }





    public function getActiveAlertsForAuthenticatedUser()
    {
        $alerts = $this->model
            ->where('user_id', auth()->id())
            ->whereHas('alert', fn ($q) => $q->where('is_read', 1))
            ->with('alert:id,name,is_active,level') 
            ->get(['id', 'alert_id', 'is_read']); 
    
        $data= [
            'total'  => $alerts->count(),
            'alerts' => $alerts,
        ];
        return $data;
    }
    
    
}
