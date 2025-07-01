<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRolePermission extends Model
{
    protected $table = 'user_role_permission';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'user_role_id',
        'permission_id',
        'is_granted',
        'date'
    ];

    protected $casts = [
        'is_granted' => 'boolean'
    ];

    /**
     * Get the user role for this user role permission
     */
    public function userRole(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }

    /**
     * Get the permission for this user role permission
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
} 