<?php
namespace App\Repositories\Complaint\ComplaintInteraction;

use App\Models\Complaint\ComplaintInteraction\ComplaintInteraction;
use App\Repositories\AbstractRepository;

class ComplaintInteractionRepository extends AbstractRepository
{
    public function __construct(ComplaintInteraction $model)
    {
        parent::__construct($model);
    }
}