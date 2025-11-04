<?php

namespace App\Models\Complaint\ModelEmail;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModelEmail extends Model
{
    use HasFactory;
    protected $table = 'model_email';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'subject', 'body', 'signature_path'];

       public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}