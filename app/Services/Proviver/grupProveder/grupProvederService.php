<?php
namespace App\Services\Proviver\grupProveder;

use App\Repositories\Proviver\grupProveder\grupProvederRepository;
use App\Services\AbstractService;

class grupProvederService extends AbstractService
{
    public function __construct(grupProvederRepository $repository)
    {
        parent::__construct($repository);
    }

     public function storeMany(array $data)
    {
        return $this->repository->storeMany($data);
    
    }

      public function getUserProviderIdByUser($idUser)
    {
        return $this->repository->getUserProviderIdByUser($idUser);
    
    }

}