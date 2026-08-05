<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'session_id',
        'student_id',
        'status',
        'recorded_by',
    ];

    /**
     * أسماء حالات الحضور بالعربي، تستخدم في العرض بدل القيمة الخام المخزنة في الداتابيز.
     */
    public static function statuses(): array
    {
        return [
            'present' => 'حاضر',
            'absent'  => 'غايب',
            'late'    => 'متأخر',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function session()
    {
        return $this->belongsTo(GroupSession::class, 'session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
