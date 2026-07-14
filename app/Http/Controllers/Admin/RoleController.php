<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('name', '!=', 'super-admin')->get();
        $permissions = Permission::all();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'super-admin') {
            return back()->with('error', 'صلاحيات مدير المركز ثابتة ومش قابلة للتعديل.');
        }

        $selected = $request->input('permissions', []);
        $role->syncPermissions($selected);

        return back()->with('success', "اتحدثت صلاحيات دور \"{$role->name}\" بنجاح.");
    }
}
