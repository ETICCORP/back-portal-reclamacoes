<?php
namespace App\Repositories\Proviver\grupProveder;

use App\Models\Proviver\grupProveder\grupProveder;
use App\Repositories\AbstractRepository;

class grupProvederRepository extends AbstractRepository
{
    public function __construct(grupProveder $model)
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

    public function getUserProviderIdByUser($idUser){
         $inserted = $this->model::where('user_id',$idUser)->first();

         return  $inserted->proveder_id;
    }
}