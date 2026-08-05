<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
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
            'phone'    => 'required|string|max:20',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'     => 'من فضلك ادخل الاسم.',
            'phone.required'    => 'من فضلك ادخل رقم التليفون.',
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

        // البحث عن سجل الطالب المطابق لرقم التليفون، بشرط إن تسجيله معتمد (active)
        // ومش مربوط بحساب دخول تاني قبل كده
        $matches = Student::where('phone', $request->phone)
            ->where('status', 'active')
            ->whereNull('user_id')
            ->get();

        if ($matches->isEmpty()) {
            return back()
                ->withErrors(['phone' => 'رقم التليفون ده مش مسجل عندنا كطالب معتمد، أو الحساب اتربط قبل كده. تواصل مع الأدمن.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        if ($matches->count() > 1) {
            return back()
                ->withErrors(['phone' => 'في أكتر من طالب مسجل بنفس رقم التليفون، محتاج الأدمن يربط حسابك يدويًا.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $student = $matches->first();

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user', // الصفحة دي لليوزر العادي (الطالب) فقط
        ]);

        $student->update(['user_id' => $user->id]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
