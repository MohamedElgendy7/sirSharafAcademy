<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeGroupIdNullableInExamSessionsTable extends Migration
{
    public function up()
    {
        // ندور على اسم الـ foreign key الحقيقي (ممكن يكون مختلف عن التسمية الافتراضية)
        $dbName = DB::getDatabaseName();

        $constraint = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'exam_sessions'
              AND COLUMN_NAME = 'group_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ", [$dbName]);

        if ($constraint) {
            DB::statement("ALTER TABLE exam_sessions DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
        }

        // نعدّل العمود يقبل NULL
        DB::statement('ALTER TABLE exam_sessions MODIFY group_id BIGINT UNSIGNED NULL');

        // نرجّع الـ foreign key بنفس الاسم اللي كان موجود، أو باسم جديد لو مكانش موجود أصلًا
        $newConstraintName = $constraint ? $constraint->CONSTRAINT_NAME : 'exam_sessions_group_id_foreign';

        DB::statement("
            ALTER TABLE exam_sessions
            ADD CONSTRAINT `{$newConstraintName}`
            FOREIGN KEY (group_id) REFERENCES groups(id)
            ON DELETE CASCADE
        ");
    }

    public function down()
    {
        $dbName = DB::getDatabaseName();

        $constraint = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'exam_sessions'
              AND COLUMN_NAME = 'group_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ", [$dbName]);

        if ($constraint) {
            DB::statement("ALTER TABLE exam_sessions DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
        }

        DB::statement('ALTER TABLE exam_sessions MODIFY group_id BIGINT UNSIGNED NOT NULL');

        $newConstraintName = $constraint ? $constraint->CONSTRAINT_NAME : 'exam_sessions_group_id_foreign';

        DB::statement("
            ALTER TABLE exam_sessions
            ADD CONSTRAINT `{$newConstraintName}`
            FOREIGN KEY (group_id) REFERENCES groups(id)
            ON DELETE CASCADE
        ");
    }
}