<?php

namespace App\Repositories\Alert\GrupoAlertEmails;

use App\Models\Alert\GrupoAlertEmails\GrupoAlertEmails;
use App\Models\Alert\GrupoType\GrupoType;
use App\Repositories\AbstractRepository;

class GrupoAlertEmailsRepository extends AbstractRepository
{
    public function __construct(GrupoAlertEmails $model)
    {
        parent::__construct($model);
    }
    public function store(array $data)
    {
        $grup = $this->model->create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        if (!empty($data['grupo_Type']) && is_array($data['grupo_Type'])) {
            $grup->grupoTypes()->createMany(
                collect($data['grupo_Type'])->map(fn($type) => [
                    'type_complaints_id' => $type['type_complaints_id'],
                    'grup_alert_id' => $grup->id,
                ])->toArray()
            );
        }

        return $grup->load('grupoTypes');
    }
}
