<?php

use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\EventUserFollowController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MasterController;

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


Route::controller(MasterController::class)->group(function () {
    Route::post('country', 'getCountry');
    Route::post('state', 'getState');
    Route::post('city', 'getCity');
    Route::get('category', 'getCategory');
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
    Route::post('update_password', 'update_password');
});

Route::controller(EventController::class)->group(function () {
    Route::post('get_data_location_wise', 'get_data_location_wise');
    Route::post('events', 'getEvents');
    Route::post('get_banner_events', 'get_banner_events');
    Route::post('create_event', 'createEvent');
   // Route::post('update_event', 'updateEventDescription');
   Route::post('update_event/{id}', [EventController::class, 'updateEventDescription']);
});

Route::controller(UserController::class)->group(function () {
    Route::get('get_profile', 'getProfile');
    Route::post('edit_profile', 'editProfile');
    Route::post('add_new_user', 'addnewuser');
    Route::post('delete_profile', 'delete_profile');
    Route::post('update_profile_pic', 'update_profile_pic');
   Route::post('edit_user_medical/{id?}', 'edit_user_medical');
   Route::get('remove/{id}', 'remove');
  //  Route::post('edit_user_medical/{id}', 'UserController@edit_user_medical');


});

Route::controller(EventUserFollowController::class)->group(function () {
    Route::post('/follow', 'Eventuserfollow');
});

Route::controller(AdvertisementController::class)->group(function () {
    Route::Get('/get_advertisement', 'GetAdvertisement');
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


// 1) Banner images not showing on location detect
// 2) Banner images not showing with proper size & background
// 3) Banner -> Arrows missing & click curser not used
//// 4) Popular Events -> Arrows hide/show as per available events
//// 5) Events -> Prices will show range if multiple tickets available
//// 6) Advertisement -> click curser not used, check size for different image (it should look same as design)
// 7) What Our Top Clints Say -> section looks scattered, should show as design
//// 8) Forgot Password -> should have click cursor
//// 9) Forgot Password -> not having cancel/back button
//// 10) After login -> user details should be displayed in menu (FirstName, LastName & email) -
//// 11) Logo should redirect to home page of website
// 12) Event search not working at all
// 13) Loader is missing on all actions throughout the system
// 14) Top bar not fixed to page scroll
// 15) Responsiveness issues are there on landing page
//// 16) Logo & page scroll top option is missing
