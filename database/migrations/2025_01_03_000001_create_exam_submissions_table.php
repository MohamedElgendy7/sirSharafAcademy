<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('exam_submissions', function (Blueprint $table) {
            $table->id();

            // جلسة الامتحان الخاصة بالطالب (كل session ليها submission واحدة بس)
            $table->unsignedBigInteger('exam_session_id');
            $table->foreign('exam_session_id')->references('id')->on('exam_sessions')->onDelete('cascade');
            $table->unique('exam_session_id');

            // بيانات مكررة (denormalized) عشان سهولة الاستعلام والتقارير من غير join دايمًا
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->unsignedBigInteger('exam_id');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');

            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('total_points')->default(0); // نسخة من مجموع درجات الامتحان وقت التسليم
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_submissions');
    }
}
