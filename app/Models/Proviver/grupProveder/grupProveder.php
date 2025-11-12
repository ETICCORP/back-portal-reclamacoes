<?php

namespace App\Models\Proviver\grupProveder;

use App\Models\Proviver\Provider;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class grupProveder extends Model
{
    use HasFactory;
    protected $table = 'grup_proveder';
    protected $primaryKey = 'id';
    protected $fillable = ['proveder_id', 'user_id'];

     public function provedor()
    {
        return $this->belongsTo(Provider::class, 'proveder_id');
    }
      public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}