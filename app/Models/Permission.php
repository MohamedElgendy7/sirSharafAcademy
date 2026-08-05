<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'is_super_admin',
        'can_view_students_current',
        'can_view_enrollment_requests',
        'can_view_waiting_list',
        'can_view_student_files',
        'can_view_courses',
        'can_view_schedule',
        'can_view_attendance',
        'can_view_teachers',
        'can_view_exams',
        'can_view_certificates',
        'can_view_payments',
        'can_view_installments',
        'can_view_financial_reports',
        'can_view_messages',
        'can_view_campaigns',
        'can_view_reviews',
        'can_view_academy_profile',
        'can_view_general_reports',
        'can_view_settings',
        'can_view_support',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_super_admin'                => 'boolean',
        'can_view_students_current'     => 'boolean',
        'can_view_enrollment_requests'  => 'boolean',
        'can_view_waiting_list'         => 'boolean',
        'can_view_student_files'        => 'boolean',
        'can_view_courses'              => 'boolean',
        'can_view_schedule'             => 'boolean',
        'can_view_attendance'           => 'boolean',
        'can_view_teachers'             => 'boolean',
        'can_view_exams'                => 'boolean',
        'can_view_certificates'         => 'boolean',
        'can_view_payments'             => 'boolean',
        'can_view_installments'         => 'boolean',
        'can_view_financial_reports'    => 'boolean',
        'can_view_messages'             => 'boolean',
        'can_view_campaigns'            => 'boolean',
        'can_view_reviews'              => 'boolean',
        'can_view_academy_profile'      => 'boolean',
        'can_view_general_reports'      => 'boolean',
        'can_view_settings'             => 'boolean',
        'can_view_support'              => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
