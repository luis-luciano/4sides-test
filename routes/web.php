<?php

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyCodeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function () {
        return redirect()->action([App\Http\Controllers\UserController::class, 'index']);
    });
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('users', UserController::class)->except(['show', 'create', 'store', 'destroy']);
    Route::put('/users/profiles/{user}', [UserController::class, 'profilesUpdate'])->name('users.profiles.update');
});


Route::get('/password/reset/verify-code', [ResetPasswordController::class, 'showResetForm'])->name('password.reset.verify-code');
Route::get('/password/verify-code', [VerifyCodeController::class, 'showVerifyCodeForm'])->name('password.verify-code');
Route::post('/password/verify-code', [VerifyCodeController::class, 'sendVerifyCode'])->name('password.verify-code.send');
Auth::routes();


// /**SEGURIDAD */
// Route::prefix('seguridad')->group(function () {
//     Route::prefix('auth')->group(function () {
//         Route::get('/login', [LoginController::class, 'login'])->name('login');

//         Route::post('/acceso', [
//             LoginController::class,
//             'acceso'
//         ])->name('login.acceso');
//     });

//     /**USUARIO */
//     Route::prefix('usuario')->group(function () {
//         Route::get('/catalogo', [
//             UsuarioController::class,
//             'catalogo'
//         ])->name('usuarios.catalogo');
//     });
// });
