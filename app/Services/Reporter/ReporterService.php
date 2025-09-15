<?php
namespace App\Services\Reporter;

use App\Repositories\Reporter\ReporterRepository;
use App\Services\AbstractService;

class ReporterService extends AbstractService
{
    public function __construct(ReporterRepository $repository)
    {
        parent::__construct($repository);
    }
}