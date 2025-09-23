<?php

namespace App\Repositories\Complaint;

use App\Models\Complaint\TypeComplaints;
use App\Repositories\AbstractRepository;

class TypeComplaintsRepository extends AbstractRepository
{
    public function __construct(TypeComplaints $model)
    {
        parent::__construct($model);
    }

    // ✅ Adicione este método
    public function getAll()
    {
        return $this->model->all();
    }
}
