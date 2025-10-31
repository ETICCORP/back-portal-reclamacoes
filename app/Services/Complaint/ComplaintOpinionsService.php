<?php
namespace App\Services\Complaint;

use App\Repositories\Complaint\ComplaintOpinionsRepository;
use App\Services\AbstractService;

class ComplaintOpinionsService extends AbstractService
{
    public function __construct(ComplaintOpinionsRepository $repository)
    {
        parent::__construct($repository);
    }
}