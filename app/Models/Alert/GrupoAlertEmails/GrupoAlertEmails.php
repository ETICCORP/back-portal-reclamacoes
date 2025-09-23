<?php

namespace App\Models\Alert\GrupoAlertEmails;

use App\Models\Alert\GrupoType\GrupoType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User\User; 
class GrupoAlertEmails extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'grupo_alert_emails';
    protected $primaryKey = 'id';
    protected $fillable = ['name','description'];
    protected $hidden = ['pivot'];
    public function grupoTypes()
    {
        return $this->hasMany(GrupoType::class, 'grup_alert_id');
    }
    public function users()
    {
        return $this->belongsToMany(
            User::class,              // modelo relacionado
            'user_grupo_alert',       // tabela pivot
            'grup_alert_id',          // chave estrangeira para grupo_alert_emails
            'user_id'                 // chave estrangeira para users
        )->withTimestamps();
    }
    
    
}

