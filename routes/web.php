<?php

use App\Http\Controllers\AdvertiseController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventParticipantsController;
use App\Http\Controllers\PaymentLogController;
use App\Http\Controllers\RemittanceManagementController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\RestCategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\LeaveController;

//--------------------------------------------

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Route::get('/', function () {
//     if(!Session::has('logged_in.admin_loggedin')) {
//         return view('login');
//     }
//     return redirect('/home');
// });

Route::get('/admin', function () {
    if(!Session::has('logged_in.admin_loggedin')) {
        return view('login');
    }
    return redirect('/home');
});
Route::post('/login', [LoginController::class,'index']);

Route::group(['middleware' => ['checkLogin']], function () {
    Route::match (['get', 'post'],'/dashboard', [DashboardController::class,'dashboard_details'])->name('dashboard');
    Route::get('dashboard/clear_search', [DashboardController::class, 'clear_search'])->name('clear_search_dashboard');
    
     /* USER ROUTE */
     Route::match (['get', 'post'], '/users', [UserController::class, 'index'])->name('user_index')->name('users.index');
     Route::match (['get', 'post'], '/user/add_edit/{id?}', [UserController::class, 'add_edit'])->name('users.add_user');
     Route::match (['get', 'post'], 'get_country_info', [UserController::class, 'get_country_info'])->name('get_country_info');
     Route::match (['get', 'post'], 'user/change_status', [UserController::class, 'change_active_status'])->name('change_status_user');
     Route::match (['get'], 'user/delete/{id}', [UserController::class, 'delete_user'])->name('delete_user');
     Route::get('user/clear_search', [UserController::class, 'clear_search'])->name('clear_search_user');
 

     // CategoryController
     Route::match (['get', 'post'], '/category', [CategoryTypeController::class, 'index_category'])->name('index_category');
     Route::match (['get', 'post'], '/category/add_edit/{id?}', [CategoryTypeController::class, 'add_edit_category'])->name('add_edit_category');
     //Route::match(['get', 'post'],'get_country_info', [CategoryTypeController::class,'get_country_info'])->name('get_country_info');
     Route::match (['get', 'post'], 'category/change_status', [CategoryTypeController::class, 'change_active_status_category'])->name('change_status_category');
     Route::match (['get'], 'category/delete/{id}', [CategoryTypeController::class, 'delete_category'])->name('delete_category');
     Route::get('category/clear_search', [CategoryTypeController::class, 'clear_search'])->name('clear_search_category');

     
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
 
    
    // EVENT ROUTE
    Route::match(['get', 'post'], '/event', [EventController::class, 'index'])->name('event_index');
    Route::match(['get', 'post'], '/event/add', [EventController::class, 'add_edit'])->name('add_event');
    Route::match(['get', 'post'], 'event/edit/{id}', [EventController::class, 'add_edit'])->name('edit_event');
    Route::match(['get', 'post'], 'event/change_status', [EventController::class, 'change_active_status'])->name('change_status_event');

    Route::get('/event/remove_event/{id}', [EventController::class, 'remove_event'])->name('remove_event');
    Route::get('/event/clear_search', [EventController::class, 'clear_search'])->name('clear_search_event');
    Route::match(['get', 'post'],'/participants_event/{event_id}', [EventParticipantsController::class, 'index'])->name('participants_event_index');
    Route::match (['get'],'/participants_event/{event_id}/delete/{id}', [EventParticipantsController::class, 'delete_participants_event'])->name('delete_participants_event');
    Route::get('/participants_event/{event_id}/clear_search', [EventParticipantsController::class, 'clear_search'])->name('clear_search_participants_event');
    // Route::match(['get', 'post'], 'participants_event/download/event_participants_sample',[EventParticipantsController::class,'download_sample']);
    Route::match(['get','post'],'participants_event/{event_id}/export_event_participants',[EventParticipantsController::class,'export_event_participants'])->name('export_event_participants');


    //TESTIMONIAL ROUTE
    Route::match(['get', 'post'], '/testimonial', [TestimonialController::class, 'index'])->name('testimonial_index');
    Route::get('/testimonial/clear_search', [TestimonialController::class, 'clear_search'])->name('clear_search_testimonial');
    Route::get('/testimonial/remove_testimonial/{id}', [TestimonialController::class, 'remove_testimonial'])->name('remove_testimonial');
    Route::match(['get', 'post'], '/testimonial/add', [TestimonialController::class, 'add_edit'])->name('add_testimonial');
    Route::match(['get', 'post'], 'testimonial/edit/{id}', [TestimonialController::class, 'add_edit'])->name('edit_testimonial');
    Route::match(['get', 'post'], 'testimonial/change_status', [TestimonialController::class, 'change_active_status'])->name('change_status_testimonial');

    //TYPE ROUTE
    Route::match(['get', 'post'], '/type', [RestCategoryController::class, 'index'])->name('type_index');
    Route::match(['get', 'post'], '/type/add', [RestCategoryController::class, 'add_edit'])->name('add_type');
    Route::match(['get', 'post'], 'type/edit/{id}', [RestCategoryController::class, 'add_edit'])->name('edit_type');
    Route::match(['get', 'post'], 'type/change_status', [RestCategoryController::class, 'change_active_status'])->name('change_status_type');

    Route::get('/type/remove_type/{id}', [RestCategoryController::class, 'remove_type'])->name('remove_type');
    Route::get('/type/clear_search', [RestCategoryController::class, 'clear_search'])->name('clear_search_type');
    Route::match(['get', 'post'], 'type/update/{id}', [RestCategoryController::class, 'update_type'])->name('update_type');

    #Remittance management 
    Route::match(['get', 'post'], '/remittance_management', [RemittanceManagementController::class, 'index'])->name('remittance_management_index');
    Route::match(['get', 'post'],'remittance_management/add', [RemittanceManagementController::class,'add_edit'])->name('add_remittance_management');
    Route::match(['get', 'post'],'remittance_management/edit/{Id}', [RemittanceManagementController::class,'add_edit'])->name('edit_remittance_management');
    Route::match (['get'], 'remittance_management/delete/{id}', [RemittanceManagementController::class, 'delete_remittance_management'])->name('delete_remittance_management');
    Route::get('/remittance_management/clear_search', [RemittanceManagementController::class, 'clear_search'])->name('clear_search_remittance_management');
    Route::match(['get', 'post'], 'remittance_management/change_status', [RemittanceManagementController::class, 'change_active_status'])->name('change_status_remittance_management');
    
    // paymentlog
    Route::match(['get', 'post'], '/payment_log', [PaymentLogController::class, 'index'])->name('Payment_log_index');
    Route::get('/payment_log/clear_search', [PaymentLogController::class, 'clear_search'])->name('clear_search_payment_log');
    

});
