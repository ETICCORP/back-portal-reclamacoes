<?php

namespace App\Models\Alert\UserGrupoAlert;

use App\Models\Alert\GrupoAlertEmails\GrupoAlertEmails;
use App\Models\User\User; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGrupoAlert extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'user_grupo_alert';
    protected $primaryKey = 'id';
    protected $fillable = ['grup_alert_id', 'user_id'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function grupo()
    {
        return $this->belongsTo(GrupoAlertEmails::class, 'grup_alert_id');
    }
}