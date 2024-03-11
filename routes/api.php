<?php

use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\EventUserFollowController;
use App\Http\Controllers\Api\OrganizerController;
use App\Http\Controllers\Api\TestimonialController;
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
    Route::get('types', 'getTypes');
    Route::post('timezone', 'getTimezone');
    Route::get('get_distance', 'getDistanceOfEvents');

});
//

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
    Route::post('create_event', 'createEventBasicInfo');
    Route::post('event_details', 'getEventDetails');
    Route::post('event_duration', 'addEventDuration');
    Route::post('event_description', 'addEventDescription');
    Route::post('userfollowevent', 'UserFollowEvent');
    Route::get('popular_cities', 'PopularCity');
});

Route::controller(UserController::class)->group(function () {
    Route::get('get_profile', 'getProfile');
    Route::post('edit_profile', 'editProfile');
    Route::post('add_new_user', 'addnewuser');
    Route::post('delete_profile', 'delete_profile');
    Route::post('update_profile_pic', 'update_profile_pic');
    Route::post('edit_user_medical', 'EditUserMedical');
    #New Routes
    Route::post('personal_details', 'PersonalDetails');
    Route::post('general_details', 'GeneralDetails');
    Route::post('address_details', 'AddressDetails');
    Route::post('social_media', 'SocialMedia');
    Route::post('communication_settings', 'CommunicationSettings');
});

Route::controller(EventUserFollowController::class)->group(function () {
    Route::post('/follow', 'Eventuserfollow');
});

Route::controller(AdvertisementController::class)->group(function () {
    Route::Get('/get_advertisement', 'GetAdvertisement');
});

Route::controller(TestimonialController::class)->group(function () {
    Route::Get('/get_testimonial', 'GetTestimonial');
});

Route::controller(OrganizerController::class)->group(function () {
    Route::Post('/get_organizer', 'GetOrganizer');
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



