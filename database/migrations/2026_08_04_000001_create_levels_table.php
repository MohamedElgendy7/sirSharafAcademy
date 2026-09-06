<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('levels', function (Blueprint $table) {
            // ملحوظة: id هنا signed bigint (مش unsignedBigInteger القياسي)
            // عشان يطابق exams.level_id اللي معمول بنفس النوع صراحةً
            $table->bigInteger('id')->autoIncrement();
            $table->string('name');
            // نوعه int(11) عادي هنا، وهيتحول لـ unsigned bigint
            // في migration الـ 2026_08_05_000002_add_foreign_key_to_levels_table
            $table->integer('course_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('levels');
    }
}