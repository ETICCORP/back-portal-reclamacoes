<?php
namespace App\Services\Description;

use App\Repositories\Description\DescriptionRepository;
use App\Services\AbstractService;

class DescriptionService extends AbstractService
{
    public function __construct(DescriptionRepository $repository)
    {
        parent::__construct($repository);
    }
}