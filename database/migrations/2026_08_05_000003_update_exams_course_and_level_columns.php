<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateExamsCourseAndLevelColumns extends Migration
{
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->after('title');
            $table->bigInteger('level_id')->nullable()->after('course_id'); // signed bigint زي levels.id بالظبط
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['course', 'level']);
        });
    }

    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['level_id']);
            $table->dropColumn(['course_id', 'level_id']);
            $table->string('course')->nullable();
            $table->integer('level')->nullable();
        });
    }
}
