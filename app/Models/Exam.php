<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{

    protected $fillable = [
        'title',
        'course_id',
        'level_id',
        'type',
        'points_mode',
        'uniform_points',
        'created_by',
    ];

    protected $casts = [
        'uniform_points' => 'integer',
    ];

    /**
     * الأدمن اللي أنشأ الامتحان
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الكورس اللي الامتحان تابع له
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * المستوى اللي الامتحان تابع له
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * أسئلة الامتحان (مرتبة حسب order)
     */
    public function questions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('order');
    }

    /**
     * كل الـ sessions (التفعيلات) الخاصة بالامتحان ده
     */
    public function sessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    /**
     * كل تسليمات الامتحان ده من الطلاب
     */
    public function submissions()
    {
        return $this->hasMany(ExamSubmission::class);
    }

    /**
     * مجموع درجات الامتحان الكلية (بتتحسب من مجموع points الأسئلة)
     */
    public function getTotalPointsAttribute()
    {
        return $this->questions()->sum('points');
    }

    public function scopeRegular($query)
    {
        return $query->where('type', 'regular');
    }

    public function scopePlacement($query)
    {
        return $query->where('type', 'placement');
    }
}
