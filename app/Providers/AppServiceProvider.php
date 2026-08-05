<?php

namespace App\Providers;


use App\Models\Student;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.partials.sidebar', function ($view) {
            $view->with([
                // الطلاب الحاليين = اللي اتقبلوا فعليًا كطلاب (status = active)
                'currentStudentsCount' => Student::where('status', 'active')->count(),
 
                // طلبات تسجيل جديدة لسه محتاجة مراجعة (status = pending)
                'pendingStudentsCount' => Student::where('status', 'pending')->count(),
 
                // قائمة الانتظار (status = waiting)
                'waitingStudentsCount' => Student::where('status', 'waiting')->count(),
            ]);
        });

        View::composer('admin.dashboard', function ($view) {
            $view->with([
                // الطلاب الحاليين = اللي اتقبلوا فعليًا كطلاب (status = active)
                'currentStudentsCount' => Student::where('status', 'active')->count(),
 
                // طلبات تسجيل جديدة لسه محتاجة مراجعة (status = pending)
                'pendingStudentsCount' => Student::where('status', 'pending')->count(),
 
                // قائمة الانتظار (status = waiting)
                'waitingStudentsCount' => Student::where('status', 'waiting')->count(),
            ]);
        });
        
    }
}
