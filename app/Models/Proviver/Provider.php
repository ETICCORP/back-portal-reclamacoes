<?php

namespace App\Models\Proviver;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends Model
{
    use HasFactory;
    protected $table = 'provider';
    protected $primaryKey = 'id';
    protected $fillable = ['nif', 'name', 'email', 'phone'];
}