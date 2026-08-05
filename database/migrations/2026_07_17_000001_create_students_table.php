<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // البيانات الشخصية
            $table->string('name');
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->enum('gender', ['male', 'female']);

            // بيانات ولي الأمر
            $table->string('guardian_phone')->nullable();

            // البيانات الأكاديمية
            $table->enum('branch', ['cairo', 'tanta', 'kafr_elsheikh', 'online'])->nullable();
            $table->unsignedTinyInteger('level')->nullable();      // المستوى: رقم من 1 إلى 10
            $table->string('course')->nullable();      // الكورس المطلوب

            // حالة الطلب: طلب جديد لسه مش متراجع، أو تم قبوله كطالب فعلي
            $table->enum('status', ['pending', 'active', 'waiting', 'rejected'])
                  ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
