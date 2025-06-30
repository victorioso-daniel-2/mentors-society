<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSchedule extends Model
{
    protected $table = 'class_schedule';
    protected $primaryKey = 'class_schedule_id';
    public $timestamps = false;

    protected $fillable = [
        'class_id',
        'subject_id',
        'academic_year_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room'
    ];

    protected $casts = [
        'class_id' => 'integer',
        'subject_id' => 'integer',
        'academic_year_id' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the class for this schedule
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    /**
     * Get the subject for this schedule
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class, 'subject_id', 'class_subject_id');
    }

    /**
     * Get the academic year for this schedule
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }
} 