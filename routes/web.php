<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\StudentAuthController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamVersionGroupController;
use App\Http\Controllers\Admin\GroupSessionController;
use App\Http\Controllers\Admin\MaterialController;
/*
|--------------------------------------------------------------------------
| 1) صفحات الزوار (Guest) — تسجيل دخول وتسجيل حساب جديد
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showUserLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'loginUser'])->name('login.attempt');

    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'loginAdmin'])->name('admin.login.attempt');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 2) صفحات الطالب (role = user)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // امتحانات الطالب (دخول كود + حل + تسليم)
    Route::get('my-exams', [ExamController::class, 'studentIndex'])->name('student.exam-sessions.index');
    Route::get('exam-sessions/{examSession}/enter-code', [ExamController::class, 'codeEntry'])->name('student.exam-sessions.code-entry');
    Route::post('exam-sessions/{examSession}/verify-code', [ExamController::class, 'verifyCode'])->name('exam-sessions.verify-code');
    Route::get('exam-sessions/{examSession}/take', [ExamController::class, 'take'])->name('student.exam-sessions.take');
    Route::get('exam-sessions/{examSession}/status', [ExamController::class, 'sessionStatus'])->name('exam-sessions.status');
    Route::post('exam-sessions/{examSession}/submit', [ExamController::class, 'submitExam'])->name('exam-sessions.submit');


    Route::get('materials', [MaterialController::class, 'studentIndex'])->name('materials.index');


    });

/*
|--------------------------------------------------------------------------
| 3) صفحات الأدمن والسوبر أدمن (role = admin أو super-admin)
|--------------------------------------------------------------------------
| كل حاجة إدارية (طلاب، جروبات، حضور، امتحانات، صلاحيات) لازم تكون هنا
| جوّه الميدل وير ده، عشان محدش يوصلها من غير تسجيل دخول بصلاحية أدمن.
*/
Route::middleware(['auth', 'role:admin,super-admin'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // ---- الصلاحيات ----
    Route::get('/admin/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
    Route::put('/admin/permissions/{user}', [PermissionController::class, 'update'])->name('admin.permissions.update');

    // ---- الطلاب ----
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/pending', [StudentController::class, 'pending'])->name('students.pending');
    Route::get('/students/index', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/profile', [StudentController::class, 'profile'])->name('students.profile');
    Route::post('/students/{student}/approve', [StudentController::class, 'approve'])->name('students.approve');
    Route::post('/students/{student}/reject', [StudentController::class, 'reject'])->name('students.reject');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');

    // ---- الجروبات ----
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::post('/groups/{group}/students', [GroupController::class, 'addStudent'])->name('groups.addStudent');
    Route::delete('/groups/{group}/students/{student}', [GroupController::class, 'removeStudent'])->name('groups.removeStudent');

    // ---- الحضور والغياب ----
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/groups/{group}/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/groups/{group}/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/groups/{group}/attendance/sessions', [AttendanceController::class, 'sessions'])->name('attendance.sessions');
    Route::get('/attendance/sessions/{session}', [AttendanceController::class, 'show'])->name('attendance.sessions.show');
    Route::put('/attendance/sessions/{session}', [AttendanceController::class, 'update'])->name('attendance.sessions.update');

    // ---- بنك الأسئلة (إدارة الامتحانات) ----
    Route::get('exams', [ExamController::class, 'index'])->name('exams.index');
    Route::get('exams/create', [ExamController::class, 'create'])->name('exams.create');
    Route::post('exams', [ExamController::class, 'store'])->name('exams.store');
    Route::get('exams/{exam}/edit', [ExamController::class, 'edit'])->name('exams.edit');
    Route::put('exams/{exam}', [ExamController::class, 'update'])->name('exams.update');
    Route::delete('exams/{exam}', [ExamController::class, 'destroy'])->name('exams.destroy');
    Route::post('exams/{exam}/duplicate', [ExamController::class, 'duplicate'])->name('exams.duplicate');

    Route::post('exams/{exam}/questions', [ExamController::class, 'storeQuestion'])->name('exams.questions.store');
    Route::delete('exam-questions/{question}', [ExamController::class, 'destroyQuestion'])->name('exam-questions.destroy');
    Route::post('exam-questions/{question}/choices', [ExamController::class, 'storeChoice'])->name('exam-questions.choices.store');
    Route::delete('exam-choices/{choice}', [ExamController::class, 'destroyChoice'])->name('exam-choices.destroy');

    Route::get('/courses/{course}/levels', [ExamController::class, 'levelsByCourse'])->name('courses.levels');

    Route::resource('exam-version-groups', ExamVersionGroupController::class);

    // ---- تفعيل الامتحان لطلاب جروب معين (لوحة الأدمن) ----
    Route::get('exams/{exam}/groups/{group}/sessions', [ExamController::class, 'manage'])->name('exam-sessions.manage');
    Route::post('exams/{exam}/groups/{group}/students/{student}/activate', [ExamController::class, 'activateForStudent'])->name('exam-sessions.activate-student');
    Route::post('exams/{exam}/groups/{group}/activate-all', [ExamController::class, 'activateForGroup'])->name('exam-sessions.activate-group');
    Route::post('exams/{exam}/students/{student}/activate-placement', [ExamController::class, 'activatePlacementForStudent'])->name('exam-sessions.activate-placement');
    Route::get('exam-sessions/{examSession}/code', [ExamController::class, 'getCode'])->name('exam-sessions.code');
    Route::post('exam-sessions/{examSession}/end', [ExamController::class, 'endSession'])->name('exam-sessions.end');

    Route::get('admin/materials', [MaterialController::class, 'index'])->name('admin.materials.index');
    Route::get('admin/materials/create', [MaterialController::class, 'create'])->name('admin.materials.create');
    Route::post('admin/materials', [MaterialController::class, 'store'])->name('admin.materials.store');
    Route::delete('admin/materials/{material}', [MaterialController::class, 'destroy'])->name('admin.materials.destroy');
 
// ============ ضيف السطور دي جوّه مجموعة middleware الطالب (auth + role:user) ============



});

Route::middleware(['auth'])->group(function () {
   Route::middleware('auth')->group(function () {
    Route::get('materials/{material}/stream', [MaterialController::class, 'stream'])->name('materials.stream');
    });
});