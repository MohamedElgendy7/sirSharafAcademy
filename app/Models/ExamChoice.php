<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamChoice extends Model
{

    protected $fillable = [
        'question_id',
        'choice_text',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * السؤال اللي الاختيار ده تابع له
     */
    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }
}
