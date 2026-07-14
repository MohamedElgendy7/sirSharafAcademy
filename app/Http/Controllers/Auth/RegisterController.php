<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'     => 'من فضلك ادخل الاسم.',
            'email.required'    => 'من فضلك ادخل البريد الإلكتروني.',
            'email.email'       => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique'      => 'البريد الإلكتروني مستخدم بالفعل.',
            'password.required' => 'من فضلك ادخل كلمة المرور.',
            'password.min'      => 'كلمة المرور لازم تكون 8 أحرف على الأقل.',
            'password.confirmed'=> 'تأكيد كلمة المرور غير مطابق.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user', // الصفحة دي لليوزر العادي فقط
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
