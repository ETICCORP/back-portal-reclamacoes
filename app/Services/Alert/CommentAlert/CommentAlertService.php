<?php
namespace App\Services\Alert\CommentAlert;

use App\Repositories\Alert\CommentAlert\CommentAlertRepository;
use App\Services\AbstractService;

class CommentAlertService extends AbstractService
{
    public function __construct(CommentAlertRepository $repository)
    {
        parent::__construct($repository);
    }
}