<?php
namespace App\Services\Complaintattachment;

use App\Repositories\Complaintattachment\ComplaintattachmentRepository;
use App\Services\AbstractService;

class ComplaintattachmentService extends AbstractService
{
    public function __construct(ComplaintattachmentRepository $repository,)
    {
        parent::__construct($repository);
    }

  
}