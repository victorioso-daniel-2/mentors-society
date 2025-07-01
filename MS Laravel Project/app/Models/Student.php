<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\EventRegistration;
use App\Models\EventParticipation;
use App\Models\ItemBorrowing;
use App\Models\StudentClass;

class Student extends Model
{
    protected $table = 'student';
    protected $primaryKey = 'student_number';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'middle_initial',
        'course',
        'year_level',
        'section',
        'email',
        'academic_status'
    ];

    /**
     * Get the user for this student
     */
    public function user()
    {
        return $this->hasOne(User::class, 'student_number', 'student_number');
    }

    /**
     * Get the classes for this student
     */
    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class, 'student_number', 'student_number');
    }

    /**
     * Get the event registrations for this student
     */
    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class, 'student_number', 'student_number');
    }

    /**
     * Get the event participations for this student
     */
    public function eventParticipations()
    {
        return $this->hasMany(EventParticipation::class, 'student_number', 'student_number');
    }

    /**
     * Get the item borrowings for this student
     */
    public function itemBorrowings()
    {
        return $this->hasMany(ItemBorrowing::class, 'student_number', 'student_number');
    }

    /**
     * Get the current classes for this student
     */
    public function currentClasses()
    {
        return $this->studentClasses()
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
        return $this->studentClasses()->where('class_id', $classId)->exists();
    }
} 