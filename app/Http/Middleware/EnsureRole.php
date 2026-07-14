<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    /**
     * الاستخدام في routes/web.php:
     *   Route::middleware(['auth', 'role:admin'])->group(...)
     *   Route::middleware(['auth', 'role:user'])->group(...)
     *
     * لو اليوزر مسجل دخول لكن بدور مختلف عن الصفحة اللي طالبها،
     * بيتوجه تلقائي للوحة تحكم بتاعته (مش error 403 جاف).
     */
    public function handle($request, Closure $next, $role)
    {
        if (! Auth::check()) {
            return redirect()->route($role === 'admin' ? 'admin.login' : 'login');
        }

        if (Auth::user()->role !== $role) {
            $ownRoute = Auth::user()->role === 'admin' ? 'admin.dashboard' : 'dashboard';

            return redirect()->route($ownRoute)
                ->with('warning', 'ليس لديك صلاحية الوصول لهذه الصفحة.');
        }

        return $next($request);
    }
}
