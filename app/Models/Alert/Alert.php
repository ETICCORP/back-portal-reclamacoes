<?php

namespace App\Models\Alert;

use App\Models\Complaint\Complaint;
use App\Models\Entities\Entities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alert extends Model
{
    use HasFactory;
    protected $table = 'alert';
    protected $primaryKey = 'id';
    protected $fillable = ['complit_id', 'is_active'];


    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complit_id');
    }
}
