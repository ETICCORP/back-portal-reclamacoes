<?php
namespace App\Services\Proviver;

use App\Repositories\Proviver\ProviderRepository;
use App\Services\AbstractService;

class ProviderService extends AbstractService
{
    public function __construct(ProviderRepository $repository)
    {
        parent::__construct($repository);
    }
}