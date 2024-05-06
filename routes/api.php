<?php

use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\EventDetailsController;
use App\Http\Controllers\Api\EventUserFollowController;
use App\Http\Controllers\Api\OrganizerController;
use App\Http\Controllers\Api\TestimonialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\EventTicketController;
use App\Http\Controllers\UserEventDetailsController;
use App\Http\Controllers\Api\GoogleLoginController;
//----------- added by prathmesh
use App\Http\Controllers\Api\FormQuestionsController;

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


Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle']);
Route::get('google_success', [GoogleLoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');


Route::controller(MasterController::class)->group(function () {
    Route::post('country', 'getCountry');
    Route::post('state', 'getState');
    Route::post('city', 'getCity');
    Route::get('category', 'getCategory');
    Route::get('types', 'getTypes');
    Route::post('timezone', 'getTimezone');
    Route::get('get_distance', 'getDistanceOfEvents');
    Route::post('get_location', 'getLocationData');
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
    Route::post('event_details_page', 'EventDetailsPage');
    Route::post('get_banner_events', 'get_banner_events');
    Route::post('create_event', 'createEventBasicInfo');
    Route::post('event_details', 'getEventDetails');
    Route::post('event_duration', 'addEventDuration');
    Route::post('event_description', 'addEventDescription');
    Route::post('userfollowevent', 'UserFollowEvent');
    Route::get('popular_cities', 'PopularCity');
    Route::post('event_setting', 'addEventSetting');
    Route::post('event_communication', 'addCommunication');
    Route::post('delete_event_comm_faq', 'deleteEventCommFqa');
    Route::post('edit_event_comm_faq', 'editEventCommFqa');
    Route::post('event_faq', 'addFAQ');
    Route::post('duplicate_events', 'DuplicateEvents');
    Route::post('delete_event/{id}', 'EventDelete');
    Route::post('event_status', 'EventStatus');
    Route::post('add_edit_coupon', 'addEditCoupon');
    Route::post('add_edit_age_criteria', 'addEditAgeCriteria');
    Route::post('add_edit_terms_conditions', 'addEditTermsConditions');
    Route::post('status_coupon', 'StatusCoupon');
});

Route::controller(UserController::class)->group(function () {
    Route::get('get_profile', 'getProfile');
    Route::post('edit_profile', 'editProfile');
    Route::post('add_new_user', 'addnewuser');
    Route::post('delete_profile', 'delete_profile');
    Route::post('update_profile_pic', 'update_profile_pic');
    Route::post('edit_user_medical', 'EditUserMedical');
    Route::post('personal_details', 'PersonalDetails');
    Route::post('general_details', 'GeneralDetails');
    Route::post('address_details', 'AddressDetails');
    Route::post('social_media', 'SocialMedia');
    Route::post('communication_settings', 'CommunicationSettings');
});

Route::controller(EventUserFollowController::class)->group(function () {
    Route::post('/follow', 'Eventuserfollow');
    Route::post('/organizer_follow', 'OrgEventuserfollow');
});

Route::controller(EventUserFollowController::class)->group(function () {
    Route::post('event_user_follow', 'Eventuserfollow');
    Route::post('event_user_unfollow', 'Eventuserunfollow');
});

Route::controller(EventTicketController::class)->group(function () {
    Route::post('get_event_ticket', 'geteventticket');
    Route::post('get_ticket_detail', 'getTicketDetail');
    Route::post('add_edit_event_ticket', 'addediteventticket');
    Route::post('delete_event_ticket', 'EventTicketDelete');
    Route::post('get_form_questions', 'getFormQuestions');
    Route::post('book_tickets', 'BookTickets');
    Route::post('get_bookings', 'GetBookings');
    Route::post('get_event_booking_tickets', 'GetEventBookingTickets');
    Route::post('ticket_pdf', 'generatePDF');
    Route::post('get_coupons', 'getCoupons');
});

Route::controller(UserEventDetailsController::class)->group(function () {
    Route::get('get_all_users', 'getallUsers');
    Route::get('get_all_events', 'getallEvents');
});

Route::controller(AdvertisementController::class)->group(function () {
    Route::get('/get_advertisement', 'GetAdvertisement');
});

Route::controller(TestimonialController::class)->group(function () {
    Route::get('/get_testimonial', 'GetTestimonial');
    Route::post('/add_subscriber','AddSubscriber');
});

Route::controller(OrganizerController::class)->group(function () {
    Route::post('/get_organizer', 'getOrganizerDetails');
    Route::get('/get_roles', 'getRoles');
    Route::get('/get_organizing_team', 'getOrganizingTeam');
    Route::post('/add_edit_organizer', 'addEditOrganizer');
    Route::post('/organizer_details', 'allOrganizerData');
    Route::post('/send_notification_org', 'sendOrgMail');

});

Route::controller(EventDetailsController::class)->group(function () {
    Route::post('/get_event_faq', 'getEventFaq');
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


//--------------- addded by prathmesh on 19-03-24

Route::controller(FormQuestionsController::class)->group(function () {
    Route::post('eventFormQuestions', 'event_form_questions');
    Route::post('GeneralFormQuestions', 'general_form_questions');
    Route::post('AddGeneralFormQuestions', 'add_general_form_questions');
    Route::post('deleteEventFormQuestions', 'delete_event_form_questions');
    Route::post('AddMaualeventFormQuestions', 'add_manual_event_form_questions');
    Route::post('AddCustomFormQuestions', 'add_custom_form_questions');
    Route::post('AddEventSetting', 'add_event_setting');
    Route::post('AllEventDetails', 'all_event_details');
    Route::post('EventDeleteChangeStatus', 'event_delete_change_status');
    Route::post('ViewSubquestionsTree', 'view_sub_question_tree');
    Route::post('EventFormQuestionsSorting', 'event_form_question_sorting');
});
