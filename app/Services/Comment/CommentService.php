<?php
namespace App\Services\Comment;

use App\Repositories\Comment\CommentRepository;
use App\Services\AbstractService;

class CommentService extends AbstractService
{
    public function __construct(CommentRepository $repository)
    {
        parent::__construct($repository);
    }
}