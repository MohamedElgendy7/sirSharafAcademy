<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /** الأدوار المسموح لها تدخل من صفحة الأدمن */
    protected array $adminRoles = ['admin', 'super-admin'];

    public function showUserLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToOwnDashboard();
        }

        return view('auth.login');
    }

    public function showAdminLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToOwnDashboard();
        }

        return view('auth.admin-login');
    }

    public function loginUser(Request $request)
    {
        return $this->attemptLogin($request, ['user']);
    }

    public function loginAdmin(Request $request)
    {
        return $this->attemptLogin($request, $this->adminRoles);
    }

    /**
     * منطق الدخول المشترك.
     * $expectedRoles: الأدوار المسموح بيها من الصفحة اللي الفورم اتبعت منها.
     */
    protected function attemptLogin(Request $request, array $expectedRoles)
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

        // الإيميل موجود لكن بدور مش متوقع في الصفحة دي -> نوجهه تلقائي للصفحة الصح
        if ($user && !in_array($user->role, $expectedRoles, true)) {
            $correctRoute = in_array($user->role, $this->adminRoles, true) ? 'admin.login' : 'login';

            return redirect()->route($correctRoute)
                ->with('info', 'تم توجيهك تلقائيًا لصفحة الدخول الخاصة بحسابك.')
                ->withInput($request->only('email'));
        }

        // لاحظ: مبنبعتش 'role' هنا لأن ممكن يكون فيه أكتر من دور مسموح (admin / super-admin)
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // تأكيد إضافي إن الدور فعلاً من ضمن الأدوار المسموحة (حماية إضافية)
            if (!in_array(Auth::user()->role, $expectedRoles, true)) {
                Auth::logout();

                return back()
                    ->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])
                    ->withInput($request->only('email'));
            }

            $request->session()->regenerate();

            return redirect()->intended(route($this->dashboardRouteFor(Auth::user()->role)));
        }

        return back()
            ->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])
            ->withInput($request->only('email'));
    }

    protected function redirectToOwnDashboard()
    {
        return redirect()->route($this->dashboardRouteFor(Auth::user()->role));
    }

    protected function dashboardRouteFor(string $role): string
    {
        return in_array($role, $this->adminRoles, true) ? 'admin.dashboard' : 'dashboard';
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}