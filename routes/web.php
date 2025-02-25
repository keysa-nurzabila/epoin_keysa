<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PelanggaranController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
   Route::get('/register', [LoginRegisterController::class, 'register'])->name('register'); 
    Route::post('/store', [LoginRegisterController::class, 'store'])->name('store');
    Route::get('/login', [LoginRegisterController::class, 'login'])->name('login');
    Route::post('/authenticate', [LoginRegisterController::class, 'authenticate'])->name('authenticate');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', [AdminController::class, 'Index'])->name('admin.dashboard');
    Route::resource('/admin/siswa',LoginRegisterController::class);
    Route::resource('/admin/akun',LoginRegisterController::class);
    Route::put('/UpdateEmail{Akun}', [LoginRegisterController::class, 'updateEmail'])->name('updateEmail');
    Route::put('/UpdatePassword{Akun}', [LoginRegisterController::class, 'updatePassword'])->name('updatePassword');
    Route::resource('admin/pelanggaran', PelanggaranController::class);
    Route::post('/logout', [LoginRegisterController::class, 'logout'])->name('logout');
});
