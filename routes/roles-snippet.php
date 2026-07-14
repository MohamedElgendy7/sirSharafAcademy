<?php

use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ضيف السطر ده جوه مجموعة الراوتس اللي فيها ['auth', 'role:admin'] في routes/web.php
|--------------------------------------------------------------------------
*/

Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
Route::put('/admin/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
