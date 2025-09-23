<?php

namespace App\Models\Alert\CommentAlert;

use App\Models\Alert\Alert;
use App\Models\Complaint\Complaint;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentAlert extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'comment_alert';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id','alert_id','comment'];
    public function alert()
    {
        return $this->belongsTo(Complaint::class, 'alert_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
