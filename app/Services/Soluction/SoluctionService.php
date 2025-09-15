<?php
namespace App\Services\Soluction;

use App\Repositories\Soluction\SoluctionRepository;
use App\Services\AbstractService;

class SoluctionService extends AbstractService
{
    public function __construct(SoluctionRepository $repository)
    {
        parent::__construct($repository);
    }
}