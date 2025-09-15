<?php

namespace App\Models\InvolveColleagues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvolveColleagues extends Model
{
    use HasFactory;
    protected $table = 'involve_colleagues';
    protected $primaryKey = 'id';
    protected $fillable = ['fk_complaint','name','role'];
}   