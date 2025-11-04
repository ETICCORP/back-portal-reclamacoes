<?php
namespace App\Services\Complaint\ModelEmail;

use App\Repositories\Complaint\ModelEmail\ModelEmailRepository;
use App\Services\AbstractService;

class ModelEmailService extends AbstractService
{
    public function __construct(ModelEmailRepository $repository)
    {
        parent::__construct($repository);
    }
      public function complaintResponse(array $data)
    {
        return $this->repository->complaintResponse($data);
    }
}