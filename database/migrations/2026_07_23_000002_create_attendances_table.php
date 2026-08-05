<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('group_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'late'])->default('present');
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();

            $table->unique(['session_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
