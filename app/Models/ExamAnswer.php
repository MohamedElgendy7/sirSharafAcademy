<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{

    protected $fillable = [
        'exam_submission_id',
        'question_id',
        'selected_choice_id',
        'is_correct',
        'points_earned',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'integer',
    ];

    public function submission()
    {
        return $this->belongsTo(ExamSubmission::class, 'exam_submission_id');
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }

    public function selectedChoice()
    {
        return $this->belongsTo(ExamChoice::class, 'selected_choice_id');
    }
}
