<?php
namespace App\Repositories\Comment;

use App\Models\Comment\Comment;
use App\Repositories\AbstractRepository;

class CommentRepository extends AbstractRepository
{
    public function __construct(Comment $model)
    {
        parent::__construct($model);
    }
    
}