<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamVersionGroup extends Model
{
    protected $fillable = ['name'];

    /**
     * كل النسخ (الامتحانات) التابعة للمجموعة دي
     */
    public function exams()
    {
        return $this->hasMany(Exam::class, 'version_group_id');
    }

    /**
     * اختيار نسخة عشوائية من المجموعة (لخاصية "السيستم يختار")
     */
    public function randomExam()
    {
        return $this->exams()->inRandomOrder()->first();
    }
}
