<?php

namespace App\Models\Description;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Description extends Model
{
    use HasFactory;
    protected $table = 'description';
    protected $primaryKey = 'id';
    protected $fillable = ['type','body','fk_complaint','description'];
}