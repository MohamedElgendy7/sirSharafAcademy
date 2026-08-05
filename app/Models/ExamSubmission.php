<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubmission extends Model
{

    protected $fillable = [
        'exam_session_id',
        'student_id',
        'exam_id',
        'score',
        'total_points',
        'submitted_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'total_points' => 'integer',
        'submitted_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    /**
     * النسبة المئوية للدرجة (محسوبة، مش مخزنة)
     */
    public function getPercentageAttribute()
    {
        if (! $this->total_points) {
            return 0;
        }

        return round(($this->score / $this->total_points) * 100, 1);
    }
}
