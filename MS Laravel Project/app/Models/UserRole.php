<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserRole extends Model
{
    protected $table = 'user_role';
    protected $primaryKey = 'user_role_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id',
        'academic_year_id',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the user for this user role
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the role for this user role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the academic year for this user role
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * Get the custom permissions for this user role
     */
    public function customPermissions(): HasMany
    {
        return $this->hasMany(UserRolePermission::class, 'user_role_id');
    }

    /**
     * Check if this user role is currently active
     */
    public function isActive(): bool
    {
        $now = now();
        return $now->gte($this->start_date) && 
               ($this->end_date === null || $now->lte($this->end_date));
    }

    /**
     * Check if this user role has a specific permission
     */
    public function hasPermission(string $permissionName): bool
    {
        // Check role permissions
        if ($this->role->hasPermission($permissionName)) {
            // Check if there's a custom override
            $customPermission = $this->customPermissions()
                                   ->whereHas('permission', function ($query) use ($permissionName) {
                                       $query->where('permission_name', $permissionName);
                                   })
                                   ->first();

            if ($customPermission) {
                return $customPermission->is_granted;
            }

            return true;
        }

        return false;
    }
} 