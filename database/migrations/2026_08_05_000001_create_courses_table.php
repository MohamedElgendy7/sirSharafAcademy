<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // إدخال الكورسات اللي كانت ثابتة في الفيو
        foreach ([
            'American Accent',
            'Business English',
            'General English',
            'Conversation',
            'IELTS preps',
            'TOEFL preps',
        ] as $course) {
            DB::table('courses')->insert([
                'name' => $course,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
