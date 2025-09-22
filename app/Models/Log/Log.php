<?php

namespace App\Models\Log;

use App\Models\Complaint\Complaint;
use App\Models\Entities\Entities;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;
    protected $table = 'log';
    protected $primaryKey = 'id';
    protected $fillable = [
        'level',
        'remote_addr',
        'path_info',
        'user_name',
        'type',
        'user_id',
        'http_user_agent',
        'message',
        'complaint_id'
    ];

   
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }
}
