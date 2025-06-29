<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassProfessor extends Model
{
    protected $table = 'class_professor';
    protected $primaryKey = 'class_professor_id';
    public $timestamps = false;

    protected $fillable = [
        'class_id',
        'subject_id',
        'academic_year_id',
        'professor_name',
        'email',
        'phone'
    ];

    protected $casts = [
        'class_id' => 'integer',
        'subject_id' => 'integer',
        'academic_year_id' => 'integer',
    ];

    /**
     * Get the class for this professor
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    /**
     * Get the subject for this professor
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class, 'subject_id', 'class_subject_id');
    }

    /**
     * Get the academic year for this professor
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }
} 