<?php

use App\Http\Controllers\AdvertiseController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommercialDashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TypeController;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\GoogleSignInHeadersMiddleware;
use App\Http\Controllers\Api\GoogleLoginController;

Route::get('/admin', function () {

    if (!Session::has('logged_in.user_login')) {

        return view('login');
    }
    // return redirect('/dashboard');
    //   Route::redirect('/admin', '/admin/dashboard');
});

// EVENT ROUTE
Route::match(['get', 'post'], '/event', [EventController::class, 'index'])->name('event_index');
Route::match(['get', 'post'], '/event/add', [EventController::class, 'add_edit'])->name('add_event');
Route::match(['get', 'post'], 'event/edit/{id}', [EventController::class, 'add_edit'])->name('edit_event');
Route::match(['get', 'post'], 'event/change_status', [EventController::class, 'change_active_status'])->name('change_status_event');

Route::get('/event/remove_event/{id}', [EventController::class, 'remove_event'])->name('remove_event');
Route::get('/event/clear_search', [EventController::class, 'clear_search'])->name('clear_search_event');

//TESTIMONIAL ROUTE
Route::match(['get', 'post'], '/testimonial', [TestimonialController::class, 'index'])->name('testimonial_index');
Route::get('/testimonial/clear_search', [TestimonialController::class, 'clear_search'])->name('clear_search_testimonial');
Route::get('/testimonial/remove_testimonial/{id}', [TestimonialController::class, 'remove_testimonial'])->name('remove_testimonial');
Route::match(['get', 'post'], '/testimonial/add', [TestimonialController::class, 'add_edit'])->name('add_testimonial');
Route::match(['get', 'post'], 'testimonial/edit/{id}', [TestimonialController::class, 'add_edit'])->name('edit_testimonial');
Route::match(['get', 'post'], 'testimonial/change_status', [TestimonialController::class, 'change_active_status'])->name('change_status_testimonial');

//TYPE ROUTE
Route::match(['get', 'post'], '/type', [TypeController::class, 'index'])->name('type_index');
Route::match(['get', 'post'], '/type/add', [TypeController::class, 'add_edit'])->name('add_type');
Route::match(['get', 'post'], 'type/edit/{id}', [TypeController::class, 'add_edit'])->name('edit_type');
Route::match(['get', 'post'], 'type/change_status', [TypeController::class, 'change_active_status'])->name('change_status_type');

Route::get('/type/remove_type/{id}', [TypeController::class, 'remove_type'])->name('remove_type');
Route::get('/type/clear_search', [TypeController::class, 'clear_search'])->name('clear_search_type');
Route::match(['get', 'post'], 'type/update/{id}', [TypeController::class, 'update_type'])->name('update_type');





Route::get('get-states', [EventController::class, 'getStates']);
Route::get('get-cities', [EventController::class, 'getCities']);
Route::post('/login', [LoginController::class, 'index']);
// Route::post('/athlete_login', [LoginController::class, 'athlete_login']);

/***************ADMIN ROUTES ***********************/
Route::group(['middleware' => ['checkLogin']], function () {
    // DASHBOARD
    Route::match (['get', 'post'], '/dashboard', [DashboardController::class, 'dashboard_details'])->name('dashboard_details');
    Route::match (['get', 'post'], '/dashboard/overview', [DashboardController::class, 'dashboard_overview_details'])->name('dashboard_overview_details');
    Route::match (['get', 'post'], '/dashboard/health_and_safety', [DashboardController::class, 'dashboard_health_safty_details'])->name('dashboard_health_safty_details');
    Route::match (['get', 'post'], '/dashboard/sustainable_practices', [DashboardController::class, 'dashboard_sustainable_practices_details'])->name('dashboard_sustainable_practices_details');
    Route::match (['get', 'post'], '/dashboard/women_empowerment', [DashboardController::class, 'dashboard_women_empowerment_details'])->name('dashboard_women_empowerment_details');
    Route::match (['get', 'post'], '/dashboard/decision_making', [DashboardController::class, 'dashboard_decision_making_details'])->name('dashboard_decision_making_details');
    Route::match (['get', 'post'], '/dashboard/social_fairness', [DashboardController::class, 'dashboard_social_fairness_details'])->name('dashboard_social_fairness_details');
    Route::match (['get', 'post'], '/dashboard/trust_level', [DashboardController::class, 'dashboard_trust_level_details'])->name('dashboard_trust_level_details');

    // Route::get('/commercial_dashboard', [CommercialDashboardController::class, 'commercial_dashboard'])->name('commercial_dashboard');
    // Route::match(['get', 'post'], '/download_pdf', [CommercialDashboardController::class, 'download_pdf']);
    Route::get('/logout', [LoginController::class, 'logout']);

    /* USER ROUTE */
    Route::match (['get', 'post'], '/users', [UserController::class, 'index'])->name('user_index');
    Route::match (['get', 'post'], '/user/add_edit/{id?}', [UserController::class, 'add_edit'])->name('add_user');
    Route::match (['get', 'post'], 'get_country_info', [UserController::class, 'get_country_info'])->name('get_country_info');
    Route::match (['get', 'post'], 'user/change_status', [UserController::class, 'change_active_status'])->name('change_status_user');
    Route::match (['get'], 'user/delete/{id}', [UserController::class, 'delete_user'])->name('delete_user');
    Route::get('user/clear_search', [UserController::class, 'clear_search'])->name('clear_search_user');

    // CategoryController
    Route::match (['get', 'post'], '/category', [CategoryController::class, 'index_category'])->name('index_category');
    Route::match (['get', 'post'], '/category/add_edit/{id?}', [CategoryController::class, 'add_edit_category'])->name('add_edit_category');
    //Route::match(['get', 'post'],'get_country_info', [CategoryController::class,'get_country_info'])->name('get_country_info');
    Route::match (['get', 'post'], 'category/change_status', [CategoryController::class, 'change_active_status_category'])->name('change_status_category');
    Route::match (['get'], 'category/delete/{id}', [CategoryController::class, 'delete_category'])->name('delete_category');
    Route::get('category/clear_search', [CategoryController::class, 'clear_search'])->name('clear_search_category');

    // BannerController
    Route::match (['get', 'post'], '/banner', [BannerController::class, 'index_banner'])->name('index_banner');
    Route::match (['get', 'post'], '/banner/add_edit/{id?}', [BannerController::class, 'add_edit_banner'])->name('add_edit_banner');
    //Route::match(['get', 'post'],'get_country_info', [CategoryController::class,'get_country_info'])->name('get_country_info');
    Route::match (['get', 'post'], 'banner/change_status', [BannerController::class, 'change_status']);
    Route::match (['get'], 'banner/delete/{id}', [BannerController::class, 'delete_banner'])->name('delete_banner');
    Route::get('banner/clear_search', [BannerController::class, 'clear_search'])->name('clear_search_banner');
    Route::get('get-states', [BannerController::class, 'getStates']);
    Route::get('get-cities', [BannerController::class, 'getCities']);

    // Advertisement
    Route::match (['get', 'post'], '/advertisement', [AdvertiseController::class, 'index_advertisement']);
    Route::match (['get', 'post'], '/advertisement/add_edit/{id?}', [AdvertiseController::class, 'add_edit_advertisement']);
    //Route::match(['get', 'post'],'get_country_info', [CategoryController::class,'get_country_info'])->name('get_country_info');
    Route::match (['get', 'post'], 'advertisement/change_status', [AdvertiseController::class, 'change_status']);
    Route::match (['get'], 'advertisement/delete/{id}', [AdvertiseController::class, 'delete_advertisement'])->name('delete_Advertisement');
    Route::get('advertisement/clear_search', [AdvertiseController::class, 'clear_search']);

});

// Route::middleware([GoogleSignInHeadersMiddleware::class])->group(function () {
    Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle']);
    Route::get('google_success', [GoogleLoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');
// });

