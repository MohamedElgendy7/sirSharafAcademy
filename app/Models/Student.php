<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'whatsapp',
        'email',
        'age',
        'gender',
        'guardian_phone',
        'branch',
        'level',
        'course',
        'status',
        'notes',
    ];

    /**
     * أسماء الفروع بالعربي، تستخدم في العرض بدل القيمة الخام المخزنة في الداتابيز.
     */
    public static function branches(): array
    {
        return [
            'cairo'          => 'فرع القاهرة',
            'tanta'          => 'فرع طنطا',
            'kafr_elsheikh'  => 'فرع كفر الشيخ',
            'online'         => 'Online',
        ];
    }

    public function getBranchLabelAttribute(): string
    {
        return self::branches()[$this->branch] ?? $this->branch;
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_student')
                     ->withPivot(['joined_at', 'status'])
                     ->withTimestamps();
    }

    /**
     * حساب الدخول (User) المربوط بهذا الطالب، لو عمل تسجيل حساب بالفعل.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * كل جلسات الامتحانات الخاصة بالطالب ده
     */
    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    /**
     * كل تسليمات الامتحانات (النتائج الفعلية بالدرجات) الخاصة بالطالب ده
     */
    public function examSubmissions()
    {
        return $this->hasMany(ExamSubmission::class);
    }

    /**
     * كل سجلات الحضور/الغياب الخاصة بالطالب ده
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * كل تقييمات المهارات الخاصة بالطالب ده
     */
    public function evaluations()
    {
        return $this->hasMany(StudentEvaluation::class);
    }
}