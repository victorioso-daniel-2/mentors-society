<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    protected $table = 'class';
    protected $primaryKey = 'class_id';
    public $timestamps = false;

    protected $fillable = [
        'class_name',
        'academic_year_id',
        'class_president_id',
        'status',
        'remarks'
    ];

    protected $casts = [
        'academic_year_id' => 'integer',
        'class_president_id' => 'integer',
    ];

    /**
     * Get the academic year for this class
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }

    /**
     * Get the class president (user)
     */
    public function classPresident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'class_president_id', 'user_id');
    }

    /**
     * Get the students in this class
     */
    public function students(): HasMany
    {
        return $this->hasMany(StudentClass::class, 'class_id', 'class_id');
    }

    /**
     * Get the subjects for this class
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(ClassSubject::class, 'class_id', 'class_id');
    }

    /**
     * Get the schedules for this class
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'class_id', 'class_id');
    }

    /**
     * Get the professors for this class
     */
    public function professors(): HasMany
    {
        return $this->hasMany(ClassProfessor::class, 'class_id', 'class_id');
    }
} 