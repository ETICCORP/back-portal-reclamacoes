<?php

namespace App\Models\Complaint;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeComplaints extends Model
{
    use HasFactory;
    protected $table = 'type_complaints';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description', 'level'];
}