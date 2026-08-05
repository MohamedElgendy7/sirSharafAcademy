<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('exam_id');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');

            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');

            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->unsignedBigInteger('activated_by');
            $table->foreign('activated_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamp('activated_at')->nullable();
            $table->string('current_code', 8)->nullable();
            $table->timestamp('code_generated_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->enum('status', ['active', 'ended'])->default('active');
            $table->json('session_data')->nullable(); // { question_order: [...], choices_order: {...} }
            $table->timestamps();

            // منع تكرار session نشطة لنفس الطالب في نفس الامتحان
            $table->unique(['exam_id', 'student_id', 'status'], 'unique_active_session');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_sessions');
    }
}
