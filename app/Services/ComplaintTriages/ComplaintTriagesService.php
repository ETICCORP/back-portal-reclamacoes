<?php
namespace App\Services\ComplaintTriages;

use App\Repositories\ComplaintTriages\ComplaintTriagesRepository;
use App\Services\AbstractService;

class ComplaintTriagesService extends AbstractService
{
    public function __construct(ComplaintTriagesRepository $repository)
    {
        parent::__construct($repository);
    }
}