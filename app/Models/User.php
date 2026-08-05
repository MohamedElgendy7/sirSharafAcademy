<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * كل مفاتيح الصلاحيات المتاحة في السايد بار، مقسّمة حسب
     * المجموعات. دي مجرد قايمة أسماء/تسميات بنستخدمها في صفحة
     * إدارة الصلاحيات وفي الـ Gate، والبيانات الفعلية بقت جوه
     * جدول permissions المنفصل (شوف Permission model وعلاقة
     * permissions() تحت).
     *
     * @var array<string, array<string, string>>
     */
    public const PERMISSION_GROUPS = [
        'إدارة الطلاب' => [
            'can_view_students_current'    => 'الطلاب الحاليين',
            'can_view_enrollment_requests'  => 'طلبات تسجيل جديدة',
            'can_view_waiting_list'         => 'قائمة الانتظار',
            'can_view_student_files'        => 'ملفات وأولياء الأمور',
        ],
        'الإدارة الأكاديمية' => [
            'can_view_courses'      => 'الكورسات والمستويات',
            'can_view_schedule'     => 'الجدول والحصص',
            'can_view_attendance'   => 'الحضور والغياب',
            'can_view_teachers'     => 'المعلمين',
            'can_view_exams'        => 'الاختبارات والدرجات',
            'can_view_certificates' => 'الشهادات',
        ],
        'المالية' => [
            'can_view_payments'          => 'المدفوعات والفواتير',
            'can_view_installments'      => 'الأقساط والخصومات',
            'can_view_financial_reports' => 'التقارير المالية',
        ],
        'التسويق والتواصل' => [
            'can_view_messages'   => 'الرسائل والإشعارات',
            'can_view_campaigns'  => 'الحملات والعروض',
            'can_view_reviews'    => 'تقييمات ومراجعات الطلاب',
        ],
        'النظام' => [
            'can_view_academy_profile'  => 'ملف الأكاديمية',
            'can_view_general_reports'  => 'التقارير العامة',
            'can_view_settings'         => 'إعدادات المركز',
            'can_view_support'          => 'الدعم الفني',
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * علاقة اليوزر بصف الصلاحيات بتاعه (صف واحد بس لكل يوزر).
     */
    public function permissions()
    {
        return $this->hasOne(Permission::class);
    }

    /**
     * سجل بيانات الطالب (Student) المربوط بحساب الدخول ده، لو كان
     * دور اليوزر student وربط حسابه بنجاح وقت التسجيل.
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    /**
     * أدمن عادي (مش سوبر أدمن).
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * سوبر أدمن (صاحب المركز). بيتحدد من مصدرين، وأي واحد فيهم
     * كافي: عمود role = 'super-admin'، أو is_super_admin = 1
     * في جدول permissions. ده بيسهل عليك تعمل السوبر أدمن بإنسرت
     * يدوي في الداتا بيز من غير ما تحتاج تظبط الاتنين مع بعض.
     */
    public function isSuperAdmin(): bool
    {
        if ($this->role === 'super-admin') {
            return true;
        }

        return (bool) optional($this->permissions)->is_super_admin;
    }

    /**
     * السوبر أدمن بس هو اللي بياخد كل الصلاحيات تلقائي. أي حد
     * تاني (أدمن أو موظف) لازم يكون عنده صف في جدول permissions،
     * ولو مفيش صف خالص يبقى محجوب من كل حاجة (الافتراضي الآمن).
     */
    public function hasPermission(string $key): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (! $this->permissions) {
            return false;
        }

        return (bool) $this->permissions->{$key};
    }
}
