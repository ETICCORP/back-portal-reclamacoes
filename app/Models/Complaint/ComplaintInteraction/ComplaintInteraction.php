<?php

namespace App\Models\Complaint\ComplaintInteraction;

use App\Models\Complaint\Complaint;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComplaintInteraction extends Model
{
    use HasFactory;
    protected $table = 'complaint_interactions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'complaint_id',
        'user_id',
        'type_contact',
        'contact',
        'notes',
    ];

      public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

      public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
