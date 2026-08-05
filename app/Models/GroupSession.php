<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSession extends Model
{
    protected $fillable = [
        'group_id',
        'taken_at',
        'created_by',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPresentCountAttribute(): int
    {
        return $this->attendances->where('status', 'present')->count();
    }

    public function getAbsentCountAttribute(): int
    {
        return $this->attendances->where('status', 'absent')->count();
    }

    public function getLateCountAttribute(): int
    {
        return $this->attendances->where('status', 'late')->count();
    }

    /**
     * أسماء الأيام بالعربي، تستخدم لعرض تاريخ الحصة بالشكل: 22/7/2026 الأربعاء 03:00 صباحاً
     */
    public static function arabicDays(): array
    {
        return [
            'Saturday'  => 'السبت',
            'Sunday'    => 'الأحد',
            'Monday'    => 'الاثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
        ];
    }

    public function getArabicTakenAtAttribute(): string
    {
        $date = $this->taken_at;
        $dayName = self::arabicDays()[$date->format('l')];
        $dateStr = $date->format('j/n/Y');
        $time = $date->format('h:i');
        $period = $date->format('A') === 'AM' ? 'صباحاً' : 'مساءً';

        return "{$dateStr} {$dayName}    {$time} {$period}";
    }
}
