<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateExamSessionsForTimerAndBugfix extends Migration
{
    public function up()
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            // خاصية الوقت المحدد (اختيارية) — بتتطبق على كل أنواع الامتحانات
            $table->unsignedInteger('time_limit_minutes')->nullable()->after('status');
            $table->timestamp('expires_at')->nullable()->after('time_limit_minutes');

            // تسجيل هل النسخة اتحددت يدوي من الأدمن ولا عشوائي (لتقرير امتحان تحديد المستوى)
            $table->boolean('is_random_version')->default(false)->after('exam_id');
        });

        // حذف الـ unique constraint القديمة اللي كانت بتمنع حتى تكرار جلسات "ended"
        // (ده هو سبب باج "عنده جلسة بالفعل" بعد إنهاء الجلسة يدويًا وإعادة التفعيل)
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropUnique('unique_active_session');
        });
    }

    public function down()
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropColumn(['time_limit_minutes', 'expires_at', 'is_random_version']);
            $table->unique(['exam_id', 'student_id', 'status'], 'unique_active_session');
        });
    }
}
