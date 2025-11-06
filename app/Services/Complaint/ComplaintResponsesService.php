<?php
namespace App\Services\Complaint;

use App\Repositories\Complaint\ComplaintResponsesRepository;
use App\Services\AbstractService;

class ComplaintResponsesService extends AbstractService
{
    public function __construct(ComplaintResponsesRepository $repository)
    {
        parent::__construct($repository);
    }
      public function complaintResponse(array $data)
    {
        return $this->repository->complaintResponse($data);
    }
   public function sendEmailResponse($id)
    {
        return $this->repository->sendEmailResponse($id);
    }
    public function byManth()
    {
        return $this->repository->byManth();
    }

    
    
}