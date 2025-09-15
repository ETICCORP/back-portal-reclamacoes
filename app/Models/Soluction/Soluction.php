<?php

namespace App\Models\Soluction;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Soluction extends Model
{
    use HasFactory;
    protected $table = 'soluction';
    protected $primaryKey = 'id';
    protected $fillable = ['fk_complaint','body','file'];
}    