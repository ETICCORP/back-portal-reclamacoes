<?php
namespace App\Services\InvolveColleagues;

use App\Repositories\InvolveColleagues\InvolveColleaguesRepository;
use App\Services\AbstractService;

class InvolveColleaguesService extends AbstractService
{
    public function __construct(InvolveColleaguesRepository $repository)
    {
        parent::__construct($repository);
    }
}