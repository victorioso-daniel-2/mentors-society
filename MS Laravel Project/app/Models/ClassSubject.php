<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSubject extends Model
{
    protected $table = 'class_subject';
    protected $primaryKey = 'class_subject_id';
    public $timestamps = false;

    protected $fillable = [
        'class_id',
        'academic_year_id',
        'subject_name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'class_id' => 'integer',
        'academic_year_id' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the class for this subject
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    /**
     * Get the academic year for this subject
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }

    /**
     * Get the schedules for this subject
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'subject_id', 'class_subject_id');
    }

    /**
     * Get the professors for this subject
     */
    public function professors(): HasMany
    {
        return $this->hasMany(ClassProfessor::class, 'subject_id', 'class_subject_id');
    }
} 