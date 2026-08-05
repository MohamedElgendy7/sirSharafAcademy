<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\admin\GroupController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ExamSessionController;
use App\Http\Controllers\Admin\StudentAuthController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\GroupSessionController;


/*
|--------------------------------------------------------------------------
| ضيف الكود ده جوه ملف routes/web.php بتاعك (فوق أي routes تانية بتستخدم auth)
|--------------------------------------------------------------------------
*/

// ==== صفحات الزوار (Guest) ====
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showUserLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'loginUser'])->name('login.attempt');

    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'loginAdmin'])->name('admin.login.attempt');

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

// ==== صفحات الأدمن والسوبر أدمن (role = admin أو super-admin) ====
Route::middleware(['auth', 'role:admin,super-admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); // اعمل الـ view دي بعدين
    })->name('admin.dashboard');

    // إدارة صلاحيات الموظفين (بديل RoleController القديم اللي كان بيستخدم Spatie)
    Route::get('/admin/permissions', [PermissionController::class, 'index'])
        ->name('admin.permissions.index');
    Route::put('/admin/permissions/{user}', [PermissionController::class, 'update'])
        ->name('admin.permissions.update');


});
// ============ الطلاب ============
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');

Route::get('/students/pending', [StudentController::class, 'pending'])->name('students.pending');
Route::get('/students/index', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
Route::post('/students/{student}/approve', [StudentController::class, 'approve'])->name('students.approve');
Route::post('/students/{student}/reject', [StudentController::class, 'reject'])->name('students.reject');
Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
// ============ الجروبات ============
Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
Route::post('/groups/{group}/students', [GroupController::class, 'addStudent'])->name('groups.addStudent');
Route::delete('/groups/{group}/students/{student}', [GroupController::class, 'removeStudent'])->name('groups.removeStudent');

// ============ الحضور والغياب ============
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/groups/{group}/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
Route::post('/groups/{group}/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/groups/{group}/attendance/sessions', [AttendanceController::class, 'sessions'])->name('attendance.sessions');
Route::get('/attendance/sessions/{session}', [AttendanceController::class, 'show'])->name('attendance.sessions.show');
Route::put('/attendance/sessions/{session}', [AttendanceController::class, 'update'])->name('attendance.sessions.update');
// =========== الامتحان ============
Route::get('exams/{exam}/groups/{group}/sessions', [ExamSessionController::class, 'manage'])->name('exam-sessions.manage');
Route::post('exams/{exam}/groups/{group}/students/{student}/activate', [ExamSessionController::class, 'activateForStudent']);
Route::post('exams/{exam}/groups/{group}/activate-all', [ExamSessionController::class, 'activateForGroup']);
Route::get('exam-sessions/{examSession}/code', [ExamSessionController::class, 'getCode']);
Route::post('exam-sessions/{examSession}/verify-code', [ExamSessionController::class, 'verifyCode']);
Route::post('exam-sessions/{examSession}/submit', [ExamSessionController::class, 'submitExam']);
Route::post('exam-sessions/{examSession}/end', [ExamSessionController::class, 'endSession']);
// =========== الامتحان — لوحة الأدمن ============
Route::get('exams/{exam}/groups/{group}/sessions', [ExamSessionController::class, 'manage'])->name('exam-sessions.manage');
Route::post('exams/{exam}/groups/{group}/students/{student}/activate', [ExamSessionController::class, 'activateForStudent']);
Route::post('exams/{exam}/groups/{group}/activate-all', [ExamSessionController::class, 'activateForGroup']);
Route::get('exam-sessions/{examSession}/code', [ExamSessionController::class, 'getCode']);
Route::post('exam-sessions/{examSession}/end', [ExamSessionController::class, 'endSession']);

// =========== الامتحان — صفحات الطالب ============
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('my-exams', [ExamSessionController::class, 'studentIndex'])->name('student.exam-sessions.index');
    Route::get('exam-sessions/{examSession}/enter-code', [ExamSessionController::class, 'codeEntry'])->name('student.exam-sessions.code-entry');
    Route::post('exam-sessions/{examSession}/verify-code', [ExamSessionController::class, 'verifyCode']);
    Route::get('exam-sessions/{examSession}/take', [ExamSessionController::class, 'take'])->name('student.exam-sessions.take');
    Route::post('exam-sessions/{examSession}/submit', [ExamSessionController::class, 'submitExam']);
});
    // ============ بنك الأسئلة (إدارة الامتحانات) ============
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
Route::get('exams/{exam}/groups/{group}/sessions', [ExamSessionController::class, 'manage'])->name('exam-sessions.manage');
Route::get('/courses/{course}/levels', [ExamController::class, 'levelsByCourse'])->name('courses.levels');