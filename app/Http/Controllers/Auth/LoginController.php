<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User; // Laravel 7: الموديل في App\User (مش App\Models\User)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /** صفحة تسجيل الدخول لليوزر العادي */
    public function showUserLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToOwnDashboard();
        }

        return view('auth.login');
    }

    /** صفحة تسجيل دخول الأدمن */
    public function showAdminLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToOwnDashboard();
        }

        return view('auth.admin-login');
    }

    public function loginUser(Request $request)
    {
        return $this->attemptLogin($request, 'user');
    }

    public function loginAdmin(Request $request)
    {
        return $this->attemptLogin($request, 'admin');
    }

    /**
     * منطق الدخول المشترك.
     * $expectedRole هو الدور المفروض إن اليوزر بيه بناءً على الصفحة اللي بعت منها الفورم.
     */
    protected function attemptLogin(Request $request, string $expectedRole)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'من فضلك ادخل البريد الإلكتروني.',
            'email.email'       => 'صيغة البريد الإلكتروني غير صحيحة.',
            'password.required' => 'من فضلك ادخل كلمة المرور.',
        ]);

        $user = User::where('email', $request->email)->first();

        // الإيميل موجود لكن بدور مختلف عن الصفحة الحالية -> نوجهه تلقائي للصفحة الصح
        if ($user && $user->role !== $expectedRole) {
            $correctRoute = $user->role === 'admin' ? 'admin.login' : 'login';

            return redirect()->route($correctRoute)
                ->with('info', 'تم توجيهك تلقائيًا لصفحة الدخول الخاصة بحسابك.')
                ->withInput($request->only('email'));
        }

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => $expectedRole,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(
                $expectedRole === 'admin' ? route('admin.dashboard') : route('dashboard')
            );
        }

        return back()
            ->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])
            ->withInput($request->only('email'));
    }

    protected function redirectToOwnDashboard()
    {
        return redirect()->route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
