<?php
namespace App\Services\Complaint;

use App\Repositories\Complaint\TypeComplaintsRepository;
use App\Services\AbstractService;

class TypeComplaintsService extends AbstractService
{
    public function __construct(TypeComplaintsRepository $repository)
    {
        parent::__construct($repository);
    }
}