<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamVersionGroup extends Model
{


    protected $fillable = ['name'];

    /**
     * كل نسخ الامتحان اللي تابعة للمجموعة دي
     */
    public function exams()
    {
        return $this->hasMany(Exam::class, 'version_group_id');
    }
}
