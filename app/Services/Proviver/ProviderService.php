<?php

namespace App\Services\Proviver;

use App\Repositories\Proviver\ProviderRepository;
use App\Services\AbstractService;
use App\Services\Proviver\grupProveder\grupProvederService;
use App\Services\User\UserService;

class ProviderService extends AbstractService
{
    private $userService;
private $grupProvederService;
    public function __construct(ProviderRepository $repository, UserService $userService, grupProvederService $grupProvederService)
    {
        parent::__construct($repository);
        $this->userService = $userService;
        $this->grupProvederService=$grupProvederService;
    }


    public function index(?int $paginate, ?array $filterParams, ?array $orderByParams, $relationships = [])
    {


        return $this->repository->index($paginate, $filterParams, $orderByParams, $relationships);
    }

    public function store(array $data)
    {
        $provider = $this->repository->store($data);
        $userData = [

            "first_name" => $data['name'],
            "last_name" => $data['name'],
            "email" => $data['email'],
            "role_id" => 2,
            "phone" => $data['phone']
        ];
     $user=   $this->userService->store($userData);
        $grupProvederService=[
               "proveder_id"=>  $provider->id,
               "user_id"=>  $user->id,
        ];
         $this->grupProvederService->store($grupProvederService);

        return $provider;
    }
}
