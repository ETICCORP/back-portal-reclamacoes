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
        'fullName'     => $data['reporter']['fullName'] ?? null,
        'email'        => $data['reporter']['email'] ?? null,
        'departament'   => $data['reporter']['department'] ?? null,
        'phone'        => $data['reporter']['phone'] ?? null,
        'quality'=> $data['relationship'],
        'fk_complaint' => $complaintId,
    ]);
}

    
}
