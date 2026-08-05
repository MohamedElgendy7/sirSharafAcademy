<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDurationToExamSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::table('exam_submissions', function (Blueprint $table) {
            $table->unsignedInteger('duration_seconds')->nullable()->after('submitted_at');
        });
    }

    public function down()
    {
        Schema::table('exam_submissions', function (Blueprint $table) {
            $table->dropColumn('duration_seconds');
        });
    }
}
