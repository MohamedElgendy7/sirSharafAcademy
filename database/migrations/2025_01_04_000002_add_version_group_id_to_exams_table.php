<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVersionGroupIdToExamsTable extends Migration
{
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->unsignedBigInteger('version_group_id')->nullable()->after('type');
            $table->foreign('version_group_id')->references('id')->on('exam_version_groups')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['version_group_id']);
            $table->dropColumn('version_group_id');
        });
    }
}
