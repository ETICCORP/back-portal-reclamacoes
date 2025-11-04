<?php
namespace App\Services\Complaint\Proviver;

use App\Repositories\Complaint\Proviver\ComplaintProviderResponseRepository;
use App\Services\AbstractService;

class ComplaintProviderResponseService extends AbstractService
{
    public function __construct(ComplaintProviderResponseRepository $repository)
    {
        parent::__construct($repository);
    }
     public function storeData(array $data)
    {
        return $this->repository->storeData($data);
    }
}