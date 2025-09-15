<?php

namespace App\Models\Reporter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reporter extends Model
{
    use HasFactory;
    protected $table = 'reporter';
    protected $primaryKey = 'id';
    protected $fillable = ['fk_complaint','fullName','email','departament','phone'];
}  