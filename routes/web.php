<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ضيف الكود ده جوه ملف routes/web.php بتاعك (فوق أي routes تانية بتستخدم auth)
|--------------------------------------------------------------------------
*/

// ==== صفحات الزوار (Guest) ====
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showUserLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'loginUser']);

    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'loginAdmin']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ==== صفحات اليوزر العادي (لازم يكون role = user) ====
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // اعمل الـ view دي بعدين
    })->name('dashboard');
});

// ==== صفحات الأدمن (لازم يكون role = admin) ====
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); // اعمل الـ view دي بعدين
    })->name('admin.dashboard');
});
