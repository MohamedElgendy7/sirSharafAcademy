<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeSuperAdmin extends Command
{
    /**
     * الاستخدام:
     *   php artisan make:super-admin "اسم صاحب المركز" owner@example.com "كلمة-سر-قوية"
     *
     * @var string
     */
    protected $signature = 'make:super-admin {name} {email} {role} {password}';

    /**
     * @var string
     */
    protected $description = 'إنشاء حساب سوبر أدمن (صاحب المركز) بصلاحيات كاملة تلقائيًا';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $role = $this->argument('role');

        $validator = Validator::make(
            compact('name', 'email', 'role', 'password'),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|in:super-admin,admin,user',
                'password' => 'required|string|min:6',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => Hash::make($password),
        ]);

        // بنعمل صف صلاحيات كمان بـ is_super_admin = 1، عشان يبقى
        // نفس الشكل اللي هتعمله لو حبيت تنسخ نفس الفكرة بإنسرت يدوي.
        if($user->role === 'super-admin') {

        
        $user->permissions()->create([
            'is_super_admin' => true,
        ]);
        
        $this->info("account created successfully as [$user->role] with email [$user->email] under name [$user->name].");
        }
        $this->info("account created successfully as [$user->role] with email [$user->email] under name [$user->name].");
    }
}
