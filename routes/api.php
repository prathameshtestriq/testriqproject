<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(LoginController::class)->group(function () {
    Route::post('signup', 'signup');
    Route::post('login', 'login');
    Route::post('google_signup', 'GoogleSignUp');
    Route::post('logout', 'logout');
    Route::post('forgot_password', 'forgot_password');
    Route::get('db_backup', 'db_backup');
    Route::post('send_reset_password_link', 'send_reset_password_link');
    Route::post('reset_password/{token}', 'reset_password');

});

Route::controller(EventController::class)->group(function () {
    Route::post('events', 'getEvents');
});

Route::controller(UserController::class)->group(function () {
    Route::get('get_profile', 'getProfile');
    Route::post('edit_profile', 'editProfile');
});























#Download Database and Project
// Route::get('/database_backup', function () {
//     Artisan::call('backup:run');
//     $path = storage_path('app/Laravel/*');
//     $latest_ctime = 0;
//     $latest_filename = '';
//     $files = glob($path);
//     // dd($files);
//     foreach ($files as $file) {
//         if (is_file($file) && filectime($file) > $latest_ctime) {
//             $latest_ctime = filectime($file);
//             $latest_filename = $file;
//         }
//     }
//     return response()->download($latest_filename);
// });


