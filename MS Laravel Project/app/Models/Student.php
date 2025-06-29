<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $table = 'student';
    protected $primaryKey = 'student_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'student_number'
    ];

    /**
     * Get the user for this student
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the classes for this student
     */
    public function classes(): HasMany
    {
        return $this->hasMany(StudentClass::class, 'student_id');
    }

    /**
     * Get the event registrations for this student
     */
    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'student_id');
    }

    /**
     * Get the event participations for this student
     */
    public function eventParticipations(): HasMany
    {
        return $this->hasMany(EventParticipation::class, 'student_id');
    }

    /**
     * Get the item borrowings for this student
     */
    public function itemBorrowings(): HasMany
    {
        return $this->hasMany(ItemBorrowing::class, 'student_id');
    }

    /**
     * Get the current classes for this student
     */
    public function currentClasses()
    {
        return $this->classes()
                   ->whereHas('academicYear', function ($query) {
                       $query->where('start_date', '<=', now())
                             ->where('end_date', '>=', now());
                   });
    }

    /**
     * Check if student is enrolled in a specific class
     */
    public function isEnrolledInClass(int $classId): bool
    {
        return $this->classes()->where('class_id', $classId)->exists();
    }

    /**
     * Get student's full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    /**
     * Get student's email
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email;
    }
} 