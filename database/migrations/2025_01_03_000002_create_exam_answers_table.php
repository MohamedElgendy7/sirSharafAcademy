<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamAnswersTable extends Migration
{
    public function up()
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('exam_submission_id');
            $table->foreign('exam_submission_id')->references('id')->on('exam_submissions')->onDelete('cascade');

            $table->unsignedBigInteger('question_id');
            $table->foreign('question_id')->references('id')->on('exam_questions')->onDelete('cascade');

            // ممكن تبقى null لو الطالب سايب السؤال من غير إجابة
            $table->unsignedBigInteger('selected_choice_id')->nullable();
            $table->foreign('selected_choice_id')->references('id')->on('exam_choices')->onDelete('set null');

            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('points_earned')->default(0);
            $table->timestamps();

            // إجابة واحدة بس لكل سؤال في كل submission
            $table->unique(['exam_submission_id', 'question_id'], 'unique_answer_per_question');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_answers');
    }
}
