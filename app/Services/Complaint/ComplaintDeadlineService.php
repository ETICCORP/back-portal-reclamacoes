<?php
namespace App\Services\Complaint;

use App\Repositories\Complaint\ComplaintDeadlineRepository;
use App\Services\AbstractService;

class ComplaintDeadlineService extends AbstractService
{
    public function __construct(ComplaintDeadlineRepository $repository)
    {
        parent::__construct($repository);
    }

    

    public function percentageServicedWithinDeadline()
    {
        return $this->repository->percentageServicedWithinDeadline();
    }
}