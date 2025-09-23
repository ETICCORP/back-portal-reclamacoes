<?php

namespace App\Models\Alert\GrupoType;

use App\Models\Alert\GrupoAlertEmails\GrupoAlertEmails;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoType extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'grupo_type';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description', 'grup_alert_id'];
    public function grupoAlert()
    {
        return $this->belongsTo(GrupoAlertEmails::class, 'grup_alert_id');
    }
   
}
