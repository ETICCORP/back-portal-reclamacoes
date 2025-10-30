<?php
namespace App\Repositories\ComplaintTriages;

use App\Models\ComplaintTriages\ComplaintTriages;
use App\Repositories\AbstractRepository;

class ComplaintTriagesRepository extends AbstractRepository
{
    public function __construct(ComplaintTriages $model)
    {
        parent::__construct($model);
    }
}