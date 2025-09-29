<?php

namespace App\Repositories\InvolveColleagues;

use App\Models\InvolveColleagues\InvolveColleagues;
use App\Repositories\AbstractRepository;

class InvolveColleaguesRepository extends AbstractRepository
{
    public function __construct(InvolveColleagues $model)
    {
        parent::__construct($model);
    }
    public function handleInvolvedColleagues(int $complaintId, $data)
{
    // Garante que sempre será array
    $involvedList = is_array($data) && isset($data[0]) ? $data : [$data];

    foreach ($involvedList as $involved) {
        if (!empty($involved['name'])) {
            $this->model->create([
                'name'         => $involved['name'],
                'role'         => $involved['role'] ?? null,
                'fk_complaint' => $complaintId,
            ]);
        }
    }
}

}
