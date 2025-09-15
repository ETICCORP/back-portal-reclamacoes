<?php

namespace App\Models\Comment;

use App\Models\Complaint\Complaint;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;
    protected $table = 'comment';
    protected $primaryKey = 'id';
    protected $fillable = [
        'comment',
        'report_id',
        'fk_user',
    ];
    public function users(){
        return $this->belongsTo(User::class, 'fk_user', 'id');
    }
    public function report(){
        return $this->belongsTo(Complaint::class, 'report_id', 'id');
    }
}    