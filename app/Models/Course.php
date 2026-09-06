<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['name'];

    /**
     * المستويات التابعة للكورس ده
     */
    public function levels()
    {
        return $this->hasMany(Level::class);
    }

    /**
     * الامتحانات التابعة للكورس ده
     */
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
