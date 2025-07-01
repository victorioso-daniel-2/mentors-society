<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\ItemCondition;
use App\Models\TransactionLog;
use App\Models\Task;
use App\Models\Role;
use App\Models\UserRole;
use App\Models\Student;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'student_number';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_number',
        'password',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the student record for this user
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'student_number', 'student_number');
    }

    /**
     * Get the user roles for this user
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'student_number', 'student_number');
    }

    /**
     * Get the roles for this user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'USER_ROLE', 'student_number', 'role_id')
                    ->withPivot('academic_year_id', 'start_date', 'end_date');
    }

    /**
     * Get the events created by this user
     */
    public function createdEvents()
    {
        return $this->hasMany(Event::class, 'created_by', 'student_number');
    }

    /**
     * Get the transactions recorded by this user
     */
    public function recordedTransactions()
    {
        return $this->hasMany(Transaction::class, 'recorded_by', 'student_number');
    }

    /**
     * Get the transactions verified by this user
     */
    public function verifiedTransactions()
    {
        return $this->hasMany(Transaction::class, 'verified_by', 'student_number');
    }

    /**
     * Get the item conditions recorded by this user
     */
    public function recordedItemConditions()
    {
        return $this->hasMany(ItemCondition::class, 'recorded_by', 'student_number');
    }

    /**
     * Get the transaction logs for this user
     */
    public function transactionLogs()
    {
        return $this->hasMany(TransactionLog::class, 'student_number', 'student_number');
    }

    /**
     * Get the tasks assigned to this user
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to', 'student_number');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('role.role_name', $roleName)->exists();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionName): bool
    {
        // Get all active user roles ordered by role priority (highest priority first)
        $activeUserRoles = $this->userRoles()
            ->join('role', 'user_role.role_id', '=', 'role.role_id')
            ->where('user_role.start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('user_role.end_date')
                      ->orWhere('user_role.end_date', '>=', now());
            })
            ->with(['role', 'customPermissions.permission'])
            ->orderBy('role.role_priority', 'asc')
            ->get();

        foreach ($activeUserRoles as $userRole) {
            // Check if this role has the permission
            if ($userRole->role->hasPermission($permissionName)) {
                // Check for custom override
                $customPermission = $userRole->customPermissions
                    ->where('permission.permission_name', $permissionName)
                    ->first();

                if ($customPermission) {
                    return $customPermission->is_granted;
                }

                return true;
            }

            // Check if there's a custom permission override even if role doesn't have it
            $customPermission = $userRole->customPermissions
                ->where('permission.permission_name', $permissionName)
                ->first();

            if ($customPermission) {
                return $customPermission->is_granted;
            }
        }

        return false;
    }

    /**
     * Get current active roles for this user
     */
    public function getCurrentRoles()
    {
        return $this->userRoles()
                   ->where('start_date', '<=', now())
                   ->where(function ($query) {
                       $query->whereNull('end_date')
                             ->orWhere('end_date', '>=', now());
                   })
                   ->with('role');
    }
}
