<?php

namespace App\Models\Complaintattachment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Complaintattachment extends Model
{
    use HasFactory;
    protected $table = 'complaintattachment';
    protected $primaryKey = 'id';
    protected $fillable = ['fk_complaint','name','file'];
} 
