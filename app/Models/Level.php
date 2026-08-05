<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = [
        'name',
        'course_id',
    ];

    /**
     * الكورس اللي المستوى ده تابع له
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * الامتحانات المرتبطة بالمستوى ده
     */
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
