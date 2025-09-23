<?php

namespace App\Models\Alert\GrupoType;

use App\Models\Alert\GrupoAlertEmails\GrupoAlertEmails;
use App\Models\Complaint\Complaint;
use App\Models\Complaint\TypeComplaints;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoType extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'grupo_type';
    protected $primaryKey = 'id';
    protected $fillable = ['complaint_id', 'grup_alert_id'];
    
    public function grupoAlert()
    {
        return $this->belongsTo(GrupoAlertEmails::class, 'grup_alert_id');
    }
    public function complaint()
    {
        return $this->belongsTo(TypeComplaints::class, 'complaint_id');
    }
    
    
}
