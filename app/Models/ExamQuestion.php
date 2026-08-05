<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{

    protected $fillable = [
        'exam_id',
        'question_text',
        'points',
        'attachment_path',
        'attachment_type',
        'order',
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
    ];

    protected $appends = ['attachment_url'];

    /**
     * الامتحان اللي السؤال ده تابع له
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * اختيارات السؤال
     */
    public function choices()
    {
        return $this->hasMany(ExamChoice::class, 'question_id');
    }

    /**
     * الاختيار الصحيح للسؤال
     */
    public function correctChoice()
    {
        return $this->hasOne(ExamChoice::class, 'question_id')->where('is_correct', true);
    }

    /**
     * رابط المرفق الكامل (لو موجود)
     */
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_path ? asset('storage/' . $this->attachment_path) : null;
    }
}
