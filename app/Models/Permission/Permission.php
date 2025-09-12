<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permission';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description', 'is_active'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }
    public function permissionRoles(): HasMany
    {
        return $this->hasMany(PermissionRole::class, 'permission_id', 'id');
    }
}
