<?php
namespace App\Services\Complaint\Proviver;

use App\Repositories\Complaint\Proviver\ComplaintProviderRepository;
use App\Services\AbstractService;

class ComplaintProviderService extends AbstractService
{
    public function __construct(ComplaintProviderRepository $repository)
    {
        parent::__construct($repository);
    }
 public function forwardComplaint($data)
    {
        return $this->repository->forwardComplaint($data);
    }
    
    public function forward()
    {
        return $this->repository->forward();
    }

    public function providersManth()
    {
        return $this->repository->providersManth();
    }
}