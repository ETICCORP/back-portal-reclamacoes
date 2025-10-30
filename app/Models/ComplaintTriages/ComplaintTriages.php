<?php

namespace App\Models\ComplaintTriages;

use App\Models\Complaint\Complaint;
use App\Models\Reporter\Reporter;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComplaintTriages extends Model
{
    use HasFactory;
    protected $table = 'complaint_triages';
    protected $primaryKey = 'id';
    protected $fillable = ['complaint_id', 'classification_type', 'severity', 'urgency', 'responsible_area', 'is_refused', 'refusal_reason', 'assigned_user_id'];


     public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

      public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

}