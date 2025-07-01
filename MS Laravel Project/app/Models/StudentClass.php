<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentClass extends Model
{
    protected $table = 'student_class';
    protected $primaryKey = ['student_number', 'class_id', 'academic_year_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'student_number',
        'class_id',
        'academic_year_id',
        'year_level'
    ];

    protected $casts = [
        'class_id' => 'integer',
        'academic_year_id' => 'integer',
    ];

    /**
     * Get the student for this class enrollment
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_number', 'student_number');
    }

    /**
     * Get the class for this enrollment
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    /**
     * Get the academic year for this enrollment
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }
} 