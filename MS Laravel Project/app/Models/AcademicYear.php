<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $table = 'academic_year';
    protected $primaryKey = 'academic_year_id';
    public $timestamps = false;

    protected $fillable = [
        'start_date',
        'end_date',
        'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the classes for this academic year
     */
    public function classes(): HasMany
    {
        return $this->hasMany(\App\Models\ClassModel::class, 'academic_year_id');
    }

    /**
     * Get the user roles for this academic year
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'academic_year_id');
    }

    /**
     * Get the student classes for this academic year
     */
    public function studentClasses(): HasMany
    {
        return $this->hasMany(StudentClass::class, 'academic_year_id');
    }

    /**
     * Check if this academic year is currently active
     */
    public function isActive(): bool
    {
        $now = now();
        return $now->between($this->start_date, $this->end_date);
    }

    /**
     * Get the current active academic year
     */
    public static function getCurrentActive()
    {
        return static::where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();
    }
} 