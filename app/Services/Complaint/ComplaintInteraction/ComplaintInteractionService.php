<?php
namespace App\Services\Complaint\ComplaintInteraction;

use App\Repositories\Complaint\ComplaintInteraction\ComplaintInteractionRepository;
use App\Services\AbstractService;

class ComplaintInteractionService extends AbstractService
{
    public function __construct(ComplaintInteractionRepository $repository)
    {
        parent::__construct($repository);
    }
}