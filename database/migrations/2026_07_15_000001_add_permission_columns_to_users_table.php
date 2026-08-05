<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionColumnsToUsersTable extends Migration
{
    /**
     * أعمدة الصلاحيات: كل عمود يمثل قسم في السايد بار.
     * الافتراضي false لأي موظف جديد، والأدمن بياخد كل الصلاحيات
     * تلقائي من غير ما نفعّل الأعمدة دي (شوف User::hasPermission()).
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // إدارة الطلاب
            $table->boolean('can_view_students_current')->default(false)->after('role');
            $table->boolean('can_view_enrollment_requests')->default(false)->after('can_view_students_current');
            $table->boolean('can_view_waiting_list')->default(false)->after('can_view_enrollment_requests');
            $table->boolean('can_view_student_files')->default(false)->after('can_view_waiting_list');

            // الإدارة الأكاديمية
            $table->boolean('can_view_courses')->default(false)->after('can_view_student_files');
            $table->boolean('can_view_schedule')->default(false)->after('can_view_courses');
            $table->boolean('can_view_attendance')->default(false)->after('can_view_schedule');
            $table->boolean('can_view_teachers')->default(false)->after('can_view_attendance');
            $table->boolean('can_view_exams')->default(false)->after('can_view_teachers');
            $table->boolean('can_view_certificates')->default(false)->after('can_view_exams');

            // المالية
            $table->boolean('can_view_payments')->default(false)->after('can_view_certificates');
            $table->boolean('can_view_installments')->default(false)->after('can_view_payments');
            $table->boolean('can_view_financial_reports')->default(false)->after('can_view_installments');

            // التسويق والتواصل
            $table->boolean('can_view_messages')->default(false)->after('can_view_financial_reports');
            $table->boolean('can_view_campaigns')->default(false)->after('can_view_messages');
            $table->boolean('can_view_reviews')->default(false)->after('can_view_campaigns');

            // النظام
            $table->boolean('can_view_academy_profile')->default(false)->after('can_view_reviews');
            $table->boolean('can_view_general_reports')->default(false)->after('can_view_academy_profile');
            $table->boolean('can_view_settings')->default(false)->after('can_view_general_reports');
            $table->boolean('can_view_support')->default(false)->after('can_view_settings');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
}
