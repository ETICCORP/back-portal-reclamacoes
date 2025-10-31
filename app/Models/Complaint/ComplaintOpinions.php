<?php

namespace App\Models\Complaint;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComplaintOpinions extends Model
{
    use HasFactory;
    protected $table = 'complaint_opinions';
    protected $primaryKey = 'id';
    protected $fillable = ['complaint_id', 'department_id', 'user_id', 'opinion', 'submitted_at'];
   public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

      public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}