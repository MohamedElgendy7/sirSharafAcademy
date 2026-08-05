<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * صفحة إدارة الصلاحيات. متاحة للسوبر أدمن بس (شوف
     * routes/web.php: role:super-admin). بتعرض الأدمنز العاديين
     * والموظفين مع بعض، والسوبر أدمن نفسه مش بيظهر هنا لأن
     * صلاحياته ثابتة دايمًا.
     */
    public function index()
    {
        $admins = User::with('permissions')->where('role', 'admin')->orderBy('name')->get();
        $employees = User::with('permissions')->where('role', 'user')->orderBy('name')->get();
        $permissionGroups = User::PERMISSION_GROUPS;

        return view('admin.permissions.index', compact('admins', 'employees', 'permissionGroups'));
    }

    /**
     * حفظ صلاحيات يوزر واحد (أدمن أو موظف) في جدول permissions
     * المنفصل. لو مفيش صف ليه أصلاً بننشئه، ولو موجود بنحدثه.
     */
    public function update(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'صلاحيات السوبر أدمن ثابتة ومش قابلة للتعديل.');
        }

        $allKeys = [];
        foreach (User::PERMISSION_GROUPS as $group) {
            $allKeys = array_merge($allKeys, array_keys($group));
        }

        $data = [];
        foreach ($allKeys as $key) {
            $data[$key] = $request->boolean($key);
        }

        $user->permissions()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return back()->with('success', "اتحدثت صلاحيات \"{$user->name}\" بنجاح.");
    }
}
