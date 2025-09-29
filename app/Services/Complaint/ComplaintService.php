<?php

namespace App\Services\Complaint;

use App\Repositories\Complaint\ComplaintRepository;
use App\Repositories\Complaintattachment\ComplaintattachmentRepository;
use App\Services\AbstractService;

class ComplaintService extends AbstractService
{
    private ComplaintattachmentRepository $complaintattachmentRepository;

    public function __construct(
        ComplaintRepository $repository,
        ComplaintattachmentRepository $complaintattachmentRepository
    ) {
        $this->complaintattachmentRepository = $complaintattachmentRepository;
        parent::__construct($repository);
    }

    public function storeData(array $data)
    {
        return $this->repository->storeData($data);
    }

    public function showFile($id)
    {
        return $this->complaintattachmentRepository->showFile($id);
    }
    public function total()
    {
        return $this->repository->total();
    }
    public function totalForCurrentWeek()
    {
        return $this->repository->totalForCurrentWeek();
    }
    public function totalForLastWeek()
    {
        return $this->repository->totalForLastWeek();
    }
    public function getTopTypes()
    {
        return $this->repository->getTopTypes();
    }
    public function getBycode($code)
    {
        return $this->repository->getBycode($code);
    }
    

    public function countByDate(?string $startDate, ?string $endDate)
    {
        $startDate = $startDate ?? now()->subDays(7)->toDateString();
        $endDate   = $endDate ?? now()->toDateString();
    
        return $this->repository->countByDate($startDate, $endDate);
    }
    
 public function updateStatus(array $data,$id)
    {
        
        return $this->repository->updateStatus( $data,$id);
    }
    
}
