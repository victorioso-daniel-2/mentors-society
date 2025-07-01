<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $table = 'permission';
    protected $primaryKey = 'permission_id';
    public $timestamps = false;

    protected $fillable = [
        'permission_name',
        'description'
    ];

    /**
     * Get the roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'ROLE_PERMISSION', 'permission_id', 'role_id');
    }

    /**
     * Get the user role permissions for this permission
     */
    public function userRolePermissions(): BelongsToMany
    {
        return $this->belongsToMany(UserRole::class, 'USER_ROLE_PERMISSION', 'permission_id', 'user_role_id')
                    ->withPivot('is_granted', 'date');
    }

    /**
     * Check if this permission is granted to a specific user role
     */
    public function isGrantedToUserRole(UserRole $userRole): bool
    {
        // Check if there's a custom override
        $customPermission = $this->userRolePermissions()
                               ->where('user_role_id', $userRole->user_role_id)
                               ->first();

        if ($customPermission) {
            return $customPermission->pivot->is_granted;
        }

        // Check if the role has this permission by default
        return $userRole->role->hasPermission($this->permission_name);
    }
} 