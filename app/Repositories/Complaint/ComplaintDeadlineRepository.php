<?php
namespace App\Repositories\Complaint;

use App\Models\Complaint\ComplaintDeadline;
use App\Repositories\AbstractRepository;

class ComplaintDeadlineRepository extends AbstractRepository
{
    public function __construct(ComplaintDeadline $model)
    {
        parent::__construct($model);
    }
}