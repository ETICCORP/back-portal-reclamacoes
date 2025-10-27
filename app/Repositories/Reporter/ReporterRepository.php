<?php

namespace App\Repositories\Reporter;

use App\Models\Reporter\Reporter;
use App\Repositories\AbstractRepository;

class ReporterRepository extends AbstractRepository
{
    public function __construct(Reporter $model)
    {
        parent::__construct($model);
    }

    public function handleReporter(array $data, int $complaintId)
{
    $this->model->create([
        'fullName'     => $data['fullName'] ?? null,
        'email'        => $data['email'] ?? null,
        'departament'   => $data['department'] ?? null,
        'phone'        => $data['phone'] ?? null,
        'quality'=> $data['quality'],
        'fk_complaint' => $complaintId,
    ]);
}

    
}
