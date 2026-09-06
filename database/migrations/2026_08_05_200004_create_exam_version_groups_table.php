<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamVersionGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('exam_version_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // مثلاً: "امتحان تحديد مستوى American Accent"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_version_groups');
    }
}
