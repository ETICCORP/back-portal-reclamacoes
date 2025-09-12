<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionRole extends Model
{
    use HasFactory;
    protected $table = 'permission_role';
    protected $primaryKey = 'id';
    protected $fillable = ['permission_id', 'role_id'];
}