<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'role';
    protected $primaryKey = 'role_id';
    public $timestamps = false;

    protected $fillable = [
        'role_name',
        'description',
        'role_priority'
    ];

    /**
     * Get the user roles for this role
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'role_id');
    }

    /**
     * Get the permissions for this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'ROLE_PERMISSION', 'role_id', 'permission_id');
    }

    /**
     * Get the users that have this role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'USER_ROLE', 'role_id', 'user_id')
                    ->withPivot('academic_year_id', 'start_date', 'end_date');
    }

    /**
     * Check if this role has a specific permission
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->where('permission_name', $permissionName)->exists();
    }

    /**
     * Assign a permission to this role
     */
    public function assignPermission(Permission $permission): void
    {
        $this->permissions()->attach($permission->permission_id);
    }

    /**
     * Remove a permission from this role
     */
    public function removePermission(Permission $permission): void
    {
        $this->permissions()->detach($permission->permission_id);
    }
} 