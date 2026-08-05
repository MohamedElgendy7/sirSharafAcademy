<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * جدول صلاحيات منفصل تمامًا عن users، بعلاقة hasOne/belongsTo.
     * is_super_admin: لو 1، صاحبه ياخد كل الصلاحيات تلقائي من غير
     * ما نحتاج نلمس بقية الأعمدة - مفيد جدًا لو هتعمل اليوزر
     * بإنسرت يدوي في الداتا بيز مباشرة.
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_super_admin')->default(false);

            // إدارة الطلاب
            $table->boolean('can_view_students_current')->default(false);
            $table->boolean('can_view_enrollment_requests')->default(false);
            $table->boolean('can_view_waiting_list')->default(false);
            $table->boolean('can_view_student_files')->default(false);

            // الإدارة الأكاديمية
            $table->boolean('can_view_courses')->default(false);
            $table->boolean('can_view_schedule')->default(false);
            $table->boolean('can_view_attendance')->default(false);
            $table->boolean('can_view_teachers')->default(false);
            $table->boolean('can_view_exams')->default(false);
            $table->boolean('can_view_certificates')->default(false);

            // المالية
            $table->boolean('can_view_payments')->default(false);
            $table->boolean('can_view_installments')->default(false);
            $table->boolean('can_view_financial_reports')->default(false);

            // التسويق والتواصل
            $table->boolean('can_view_messages')->default(false);
            $table->boolean('can_view_campaigns')->default(false);
            $table->boolean('can_view_reviews')->default(false);

            // النظام
            $table->boolean('can_view_academy_profile')->default(false);
            $table->boolean('can_view_general_reports')->default(false);
            $table->boolean('can_view_settings')->default(false);
            $table->boolean('can_view_support')->default(false);

            $table->timestamps();

            $table->unique('user_id'); // كل يوزر له صف واحد بس
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
