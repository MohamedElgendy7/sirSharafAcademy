<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleToUsersTable extends Migration
{
    /**
     * قيم العمود المتوقعة: 'user' أو 'admin'
     * اليوزر العادي بياخد 'user' تلقائي عند التسجيل من صفحة /register
     * الأدمن بيتعمل يدوي (Tinker / Seeder) أو من لوحة تحكم أعلى لاحقًا
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('user')->after('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
