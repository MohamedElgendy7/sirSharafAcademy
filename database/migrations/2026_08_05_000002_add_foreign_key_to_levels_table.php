<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddForeignKeyToLevelsTable extends Migration
{
    public function up()
    {
        // لازم نغيّر نوع course_id من int(11) إلى unsigned bigint
        // عشان يطابق نوع courses.id، وإلا الـ foreign key هيفشل بـ error 150
        DB::statement('ALTER TABLE levels MODIFY course_id BIGINT UNSIGNED NOT NULL');

        Schema::table('levels', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('levels', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
        });

        DB::statement('ALTER TABLE levels MODIFY course_id INT(11) NOT NULL');
    }
}
