<?php
namespace App\Repositories\Alert\GrupoType;

use App\Models\Alert\GrupoType\GrupoType;
use App\Repositories\AbstractRepository;

class GrupoTypeRepository extends AbstractRepository
{
    public function __construct(GrupoType $model)
    {
        parent::__construct($model);
    }
}