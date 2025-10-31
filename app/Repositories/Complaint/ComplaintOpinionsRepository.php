<?php
namespace App\Repositories\Complaint;

use App\Models\Complaint\ComplaintOpinions;
use App\Repositories\AbstractRepository;

class ComplaintOpinionsRepository extends AbstractRepository
{
    public function __construct(ComplaintOpinions $model)
    {
        parent::__construct($model);
    }
}