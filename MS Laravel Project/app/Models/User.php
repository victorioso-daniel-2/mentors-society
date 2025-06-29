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

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'USER';
    protected $primaryKey = 'user_id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_initial',
        'email',
        'password'
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
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    /**
     * Get the user roles for this user
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    /**
     * Get the roles for this user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'USER_ROLE', 'user_id', 'role_id')
                    ->withPivot('academic_year_id', 'start_date', 'end_date');
    }

    /**
     * Get the events created by this user
     */
    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    /**
     * Get the transactions recorded by this user
     */
    public function recordedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'recorded_by');
    }

    /**
     * Get the transactions verified by this user
     */
    public function verifiedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'verified_by');
    }

    /**
     * Get the item conditions recorded by this user
     */
    public function recordedItemConditions(): HasMany
    {
        return $this->hasMany(ItemCondition::class, 'recorded_by');
    }

    /**
     * Get the transaction logs for this user
     */
    public function transactionLogs(): HasMany
    {
        return $this->hasMany(TransactionLog::class, 'user_id');
    }

    /**
     * Get the tasks assigned to this user
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name . ' ' . $this->last_name;
        if ($this->middle_initial) {
            $name = $this->first_name . ' ' . $this->middle_initial . '. ' . $this->last_name;
        }
        return $name;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('role_name', $roleName)->exists();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->userRoles()
                   ->whereHas('role.permissions', function ($query) use ($permissionName) {
                       $query->where('permission_name', $permissionName);
                   })
                   ->exists();
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
