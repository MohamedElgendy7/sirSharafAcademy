<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentEvaluationsTable extends Migration
{
    public function up()
    {
        Schema::create('student_evaluations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            // المستوى اللي التقييم ده بتاعه (وقت نهاية المستوى)
            $table->string('level')->nullable();

            // اسم المهارة (زي Speaking, Listening, Grammar...)
            $table->string('skill');

            // تعليق المدرب على المهارة دي
            $table->text('comment')->nullable();

            // تقييم رقمي اختياري (مثلاً من 5 أو من 10) - هنحدد النطاق بدقة وقت بناء فورم الإدخال بعدين
            $table->unsignedTinyInteger('rating')->nullable();

            $table->unsignedBigInteger('evaluated_by');
            $table->foreign('evaluated_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_evaluations');
    }
}
