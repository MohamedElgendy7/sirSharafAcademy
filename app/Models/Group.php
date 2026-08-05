<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
        'course',
        'level',
        'capacity',
        'status',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'group_student')
                     ->withPivot(['joined_at', 'status'])
                     ->withTimestamps();
    }

    public function sessions()
    {
        return $this->hasMany(GroupSession::class);
    }
}
