<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * الصلاحيات الأساسية بتاعة موديول الطلاب.
     * لما تعمل موديول جديد (فواتير، معلمين، إلخ)، ضيف صلاحياته هنا بنفس النمط.
     */
    protected $permissions = [
        'students.view',
        'students.create',
        'students.edit',
        'students.delete',
    ];

    public function run()
    {
        // امسح الكاش بتاع الصلاحيات القديمة (مهم يتعمل قبل أي إنشاء)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // مدير المركز: عنده كل الصلاحيات تلقائي عن طريق Gate::before
        // (شوف ملف auth-service-provider-snippet.txt) — مش محتاج نربط له صلاحيات هنا
        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        // موظف استقبال: يقدر يشوف ويضيف طلاب بس، من غير تعديل أو حذف
        $receptionist = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $receptionist->syncPermissions(['students.view', 'students.create']);
    }
}
