<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ExamSession extends Model
{

    protected $fillable = [
        'exam_id',
        'group_id',
        'student_id',
        'is_random_version',
        'activated_by',
        'activated_at',
        'current_code',
        'code_generated_at',
        'started_at',
        'status',
        'time_limit_minutes',
        'expires_at',
        'session_data',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'code_generated_at' => 'datetime',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_random_version' => 'boolean',
        'session_data' => 'array',
    ];

    /**
     * الامتحان الأصلي
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * الجروب اللي الطالب فيه وقت التفعيل
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * الطالب صاحب الـ session
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * الأدمن اللي فعّل الامتحان
     */
    public function activator()
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    /**
     * تسليم الطالب لهذه الجلسة (لو خلص وسلّم)
     */
    public function submission()
    {
        return $this->hasOne(ExamSubmission::class, 'exam_session_id');
    }

    /**
     * ترتيب الأسئلة العشوائي الخاص بالطالب ده
     */
    public function getQuestionOrderAttribute()
    {
        return $this->session_data['question_order'] ?? [];
    }

    /**
     * ترتيب الاختيارات العشوائي لكل سؤال، خاص بالطالب ده
     */
    public function getChoicesOrderAttribute()
    {
        return $this->session_data['choices_order'] ?? [];
    }

    /**
     * هل الكود الحالي لسه صالح (لم تعدِ الدقيقة)؟
     */
    public function isCodeValid()
    {
        if (! $this->current_code || ! $this->code_generated_at) {
            return false;
        }

        return $this->code_generated_at->diffInSeconds(Carbon::now()) < 60;
    }

    /**
     * توليد/تجديد الكود لو عدت الدقيقة، وإرجاع الكود الحالي دايمًا
     */
    public function getOrRefreshCode()
    {
        if (! $this->isCodeValid()) {
            $this->current_code = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
            $this->code_generated_at = Carbon::now();
            $this->save();
        }

        return $this->current_code;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    /**
     * هل الجلسة ليها حد وقت أصلًا؟
     */
    public function hasTimeLimit(): bool
    {
        return ! is_null($this->time_limit_minutes);
    }

    /**
     * هل انتهى وقت الامتحان (لو كان محدد بوقت)؟
     */
    public function isTimeExpired(): bool
    {
        if (! $this->hasTimeLimit() || ! $this->expires_at) {
            return false;
        }

        return Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * الوقت المتبقي بالثواني (null لو مفيش حد وقت)
     */
    public function getRemainingSecondsAttribute()
    {
        if (! $this->hasTimeLimit() || ! $this->expires_at) {
            return null;
        }

        $remaining = Carbon::now()->diffInSeconds($this->expires_at, false);

        return max(0, $remaining);
    }
}