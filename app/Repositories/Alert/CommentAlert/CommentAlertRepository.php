<?php
namespace App\Repositories\Alert\CommentAlert;

use App\Models\Alert\CommentAlert\CommentAlert;
use App\Repositories\AbstractRepository;

class CommentAlertRepository extends AbstractRepository
{
    public function __construct(CommentAlert $model)
    {
        parent::__construct($model);
    }

    public function store(array $data)
    {
        $data['user_id'] = auth()->id();  
        return $this->model->create($data);
    }
    
    
}