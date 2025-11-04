<?php

namespace App\Models\Complaint\Proviver;

use App\Models\Complaint\Complaint;
use App\Models\Proviver\Provider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComplaintProvider extends Model
{
    use HasFactory;
    protected $table = 'complaint_provider';
    protected $primaryKey = 'id';
    protected $fillable = ['complaint_id', 'provider_id', 'summary', 'notes', 'sent_at', 'deadline', 'status'];
protected $casts = [
    'deadline' => 'datetime',
];

      public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }
       public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }
}