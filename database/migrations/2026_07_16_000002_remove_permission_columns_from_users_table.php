<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePermissionColumnsFromUsersTable extends Migration
{
    /**
     * الأعمدة دي كانت في users قبل ما نقلناها لجدول permissions
     * المنفصل. بنمسحها من هنا عشان مايبقاش فيه ازدواجية.
     */
    public function up()
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

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_view_students_current')->default(false);
            $table->boolean('can_view_enrollment_requests')->default(false);
            $table->boolean('can_view_waiting_list')->default(false);
            $table->boolean('can_view_student_files')->default(false);
            $table->boolean('can_view_courses')->default(false);
            $table->boolean('can_view_schedule')->default(false);
            $table->boolean('can_view_attendance')->default(false);
            $table->boolean('can_view_teachers')->default(false);
            $table->boolean('can_view_exams')->default(false);
            $table->boolean('can_view_certificates')->default(false);
            $table->boolean('can_view_payments')->default(false);
            $table->boolean('can_view_installments')->default(false);
            $table->boolean('can_view_financial_reports')->default(false);
            $table->boolean('can_view_messages')->default(false);
            $table->boolean('can_view_campaigns')->default(false);
            $table->boolean('can_view_reviews')->default(false);
            $table->boolean('can_view_academy_profile')->default(false);
            $table->boolean('can_view_general_reports')->default(false);
            $table->boolean('can_view_settings')->default(false);
            $table->boolean('can_view_support')->default(false);
        });
    }
}
