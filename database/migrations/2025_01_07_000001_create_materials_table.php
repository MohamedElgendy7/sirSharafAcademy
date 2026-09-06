<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialsTable extends Migration
{
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['video', 'book']);

            // كورس ومستوى (نفس نمط الامتحانات) — nullable بس لو المادة "عامة"
            $table->unsignedBigInteger('course_id')->nullable();
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');

            $table->unsignedBigInteger('level_id')->nullable();
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');

            // مادة عامة = تظهر لكل الطلاب بغض النظر عن كورسهم/مستواهم
            $table->boolean('is_general')->default(false);

            $table->text('description')->nullable();

            // المسار داخل الـ disk المخصص (materials) — مش داخل public خالص
            $table->string('disk_path');
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('materials');
    }
}
