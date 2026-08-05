<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    /**
     * الاستخدام في routes/web.php:
     *   Route::middleware(['auth', 'role:admin,super-admin'])->group(...)
     *   Route::middleware(['auth', 'role:user'])->group(...)
     *
     * لو اليوزر مسجل دخول لكن بدور مختلف عن الصفحة اللي طالبها،
     * بيتوجه تلقائي للوحة تحكم بتاعته (مش error 403 جاف).
     */

    /** الأدوار اللي بتتعامل مع لوحة الأدمن */
    protected array $adminRoles = ['admin', 'super-admin'];

    public function handle($request, Closure $next, ...$roles)
    {
        if (! Auth::check()) {
            $isAdminArea = !empty(array_intersect($roles, $this->adminRoles));

            return redirect()->route($isAdminArea ? 'admin.login' : 'login');
        }

        if (! in_array(Auth::user()->role, $roles, true)) {
            $ownRoute = in_array(Auth::user()->role, $this->adminRoles, true) ? 'admin.dashboard' : 'dashboard';

            return redirect()->route($ownRoute)
                ->with('warning', 'ليس لديك صلاحية الوصول لهذه الصفحة.');
        }

        return $next($request);
    }
}
