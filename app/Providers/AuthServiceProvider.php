<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /*
         * السوبر أدمن بيعدي أي Gate في المشروع تلقائي (مش بس
         * صلاحيات السايد بار)، عشان نغطي أي @can أو authorize()
         * تعمله في المستقبل من غير ما نحتاج نكرر الشرط في كل Gate.
         */
        Gate::before(function (User $user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        /*
         * بنسجل Gate حقيقي لكل مفتاح صلاحية موجود في User::PERMISSION_GROUPS.
         * ده بيدينا تحكم فعلي في الوصول (مش بس إخفاء شكلي في السايد بار):
         *
         *  - في الـ routes:  Route::middleware(['auth','can:can_view_students_current'])
         *  - في الـ Blade:   @can('can_view_students_current') ... @endcan
         *  - في الـ Controller: $this->authorize('can_view_students_current');
         *
         * القيمة بترجع من نفس المكان اللي بيحدد ظهور السايد بار
         * (User::hasPermission)، فمفيش أي تعارض بين الاتنين.
         */
        foreach (User::PERMISSION_GROUPS as $group) {
            foreach ($group as $key => $label) {
                Gate::define($key, function (User $user) use ($key) {
                    return $user->hasPermission($key);
                });
            }
        }
    }
}