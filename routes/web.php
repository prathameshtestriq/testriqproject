<?php

use App\Http\Controllers\AdvertiseController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailSendingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventParticipantsController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MasterRoleController;
use App\Http\Controllers\PaymentLogController;
use App\Http\Controllers\RegistrationSuccessfulController;
use App\Http\Controllers\RemittanceManagementController;
use App\Http\Controllers\RgistrationSuccessfulController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\RacesCategoryController;
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
use App\Http\Controllers\RoleController;
use App\Http\Controllers\OrganiserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\MasterCMSController;
use App\Http\Controllers\ParticipantBulkController;
use App\Http\Controllers\MaintenanceModeController;

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
     Route::get('/db_backup', [DashboardController::class, 'db_backup']);
    
     /* USER ROUTE */
     Route::match (['get', 'post'], '/users', [UserController::class, 'index'])->name('user_index')->name('users.index');
     Route::match (['get', 'post'], '/user/add_edit/{id?}', [UserController::class, 'add_edit'])->name('users.add_user');
     Route::match (['get', 'post'], 'get_country_info', [UserController::class, 'get_country_info'])->name('get_country_info');
     Route::match (['get', 'post'], 'user/change_status', [UserController::class, 'change_active_status'])->name('change_status_user');
     Route::match (['get'], 'user/delete/{id}', [UserController::class, 'delete_user'])->name('delete_user');
     Route::get('user/clear_search', [UserController::class, 'clear_search'])->name('clear_search_user');
     Route::match(['get','post'],'user/export_download',[UserController::class,'export_excel'])->name('export_user');
     Route::match (['get', 'post'], '/get_states', [UserController::class, 'get_states'])->name('get_states_user');
     Route::match (['get', 'post'], '/get_cities', [UserController::class, 'get_cities'])->name('get_cities_user');

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
    Route::match (['get'], ' /event/remove_event_image/{event_id}/{id}', [EventController::class, 'delete_event_image'])->name('delete_event_image');
    Route::match(['get', 'post'],'/ckeditor_event_description/upload', [EventController::class, 'upload'])->name('ckeditor_event_description.upload');
  
    // EVENT Participants 
    Route::match(['get', 'post'],'/participants_event/{event_id?}', [EventParticipantsController::class, 'index'])->name('participants_event_index');
    Route::match (['get'],'/participants_event/{event_id}/delete/{id}', [EventParticipantsController::class, 'delete_participants_event'])->name('delete_participants_event');
    Route::get('/participants_event/{event_id}/clear_search', [EventParticipantsController::class, 'clear_search'])->name('clear_search_participants_event');
    Route::match(['get','post'],'participants_event/{event_id}/export_download',[EventParticipantsController::class,'export_event_participants'])->name('export_event_participants');
    Route::match(['get','post'],'participants_event/{event_id}/export_revenue',[EventParticipantsController::class,'export_participants_revenue'])->name('export_participants_revenue');
    Route::match(['get','post'],'participants_event/{event_id}/view/{id}',[EventParticipantsController::class,'view'])->name('question_participants_event');
    Route::match(['get','post'],'participants_event/{event_id}/edit/{id}',[EventParticipantsController::class,'Edit_question'])->name('participants_question_edit');
    Route::match (['get', 'post'], '/get_states/{country_id}', [EventParticipantsController::class, 'get_states'])->name('get_states')->name('get_states');
    Route::match (['get', 'post'], '/get_cities/{state_id}', [EventParticipantsController::class, 'get_cities'])->name('get_cities')->name('get_cities');

    // EVENT Registration
    Route::match(['get', 'post'], '/registration_successful/{event_id?}', [RegistrationSuccessfulController::class, 'index'])->name('registration_successful_index');
    Route::get('/registration_successful/{event_id}/clear_search', [RegistrationSuccessfulController::class, 'clear_search'])->name('clear_search_registration_successful');
    Route::match(['get','post'],'/registration_successful/{event_id}/export_registration',[RegistrationSuccessfulController::class,'export_registration_successful'])->name('export_registration_successful');
    

    //TESTIMONIAL ROUTE
    Route::match(['get', 'post'], '/testimonial', [TestimonialController::class, 'index'])->name('testimonial_index');
    Route::get('/testimonial/clear_search', [TestimonialController::class, 'clear_search'])->name('clear_search_testimonial');
    Route::get('/testimonial/remove_testimonial/{id}', [TestimonialController::class, 'remove_testimonial'])->name('remove_testimonial');
    Route::match(['get', 'post'], '/testimonial/add', [TestimonialController::class, 'add_edit'])->name('add_testimonial');
    Route::match(['get', 'post'], 'testimonial/edit/{id}', [TestimonialController::class, 'add_edit'])->name('edit_testimonial');
    Route::match(['get', 'post'], 'testimonial/change_status', [TestimonialController::class, 'change_active_status'])->name('change_status_testimonial');

    //TYPE ROUTE
    Route::match(['get', 'post'], '/type', [RacesCategoryController::class, 'index'])->name('type_index');
    Route::match(['get', 'post'], '/type/add', [RacesCategoryController::class, 'add_edit'])->name('add_type');
    Route::match(['get', 'post'], 'type/edit/{id}', [RacesCategoryController::class, 'add_edit'])->name('edit_type');
    Route::match(['get', 'post'], 'type/change_status', [RacesCategoryController::class, 'change_active_status'])->name('change_status_type');

    Route::get('/type/remove_type/{id}', [RacesCategoryController::class, 'remove_type'])->name('remove_type');
    Route::get('/type/clear_search', [RacesCategoryController::class, 'clear_search'])->name('clear_search_type');
    Route::match(['get', 'post'], 'type/update/{id}', [RacesCategoryController::class, 'update_type'])->name('update_type');

    #Remittance management 
    Route::match(['get', 'post'], '/remittance_management', [RemittanceManagementController::class, 'index'])->name('remittance_management_index');
    Route::match(['get', 'post'],'remittance_management/add', [RemittanceManagementController::class,'add_edit'])->name('add_remittance_management');
    Route::match(['get', 'post'],'remittance_management/edit/{Id}', [RemittanceManagementController::class,'add_edit'])->name('edit_remittance_management');
    Route::match (['get'], 'remittance_management/delete/{id}', [RemittanceManagementController::class, 'delete_remittance_management'])->name('delete_remittance_management');
    Route::get('/remittance_management/clear_search', [RemittanceManagementController::class, 'clear_search'])->name('clear_search_remittance_management');
    Route::match(['get', 'post'], 'remittance_management/change_status', [RemittanceManagementController::class, 'change_active_status'])->name('change_status_remittance_management');
     /* Remittance Export */
    Route::match(['get','post'],' remittance_management/export_remittance_management',[RemittanceManagementController::class,'export_remittance_management'])->name('export_remittance_management');
   /* Remittance Import */
   Route::post('/remittance_management/import_employee', [RemittanceManagementController::class, 'import_remittance_management'])->name('remittance_management.import_remittance_management');

    
    // paymentlog
    Route::match(['get', 'post'], '/payment_log', [PaymentLogController::class, 'index'])->name('Payment_log_index');
    Route::get('/payment_log/clear_search', [PaymentLogController::class, 'clear_search'])->name('clear_search_payment_log');
    Route::match(['get','post'],' payment_log/export_payment_log',[PaymentLogController::class,'export_payment_log'])->name('export_payment_log');

    // Sending Email
    Route::get('/email_sending/clear_search', [EmailSendingController::class, 'clear_search'])->name('clear_search_email');
    Route::match(['get', 'post'], '/email_sending', [EmailSendingController::class, 'index'])->name('email_sending_index');
    Route::match(['get', 'post'],'email_sending/add', [EmailSendingController::class,'add_edit'])->name('add_email_sending');
    Route::match(['get', 'post'], 'email_sending/change_status', [EmailSendingController::class, 'change_active_status'])->name('change_status_email_sending');
    Route::match(['get', 'post'],'/ckeditor/upload', [EmailSendingController::class, 'upload'])->name('ckeditor.upload');
    
    // Marketing
    Route::match(['get', 'post'], '/marketing', [MarketingController::class, 'index'])->name('marketing_index');
    Route::match(['get', 'post'],'marketing/add', [MarketingController::class,'add_edit'])->name('add_marketing');
    Route::match(['get', 'post'],'marketing/edit/{Id}', [MarketingController::class,'add_edit'])->name('edit_marketing');
    Route::match (['get'], 'marketing/delete/{id}', [MarketingController::class, 'delete_marketing'])->name('delete_marketing');
    Route::get('/marketing/clear_search', [MarketingController::class, 'clear_search'])->name('clear_search_marketing');
    Route::match(['get', 'post'], 'marketing/change_status', [MarketingController::class, 'change_active_status'])->name('change_status_marketing');
 

    // master_role
    Route::match(['get', 'post'], '/role_master', [MasterRoleController::class, 'index'])->name('role_master_index');
    Route::match(['get', 'post'],'role_master/add', [MasterRoleController::class,'add_edit'])->name('add_role_master');
    Route::match(['get', 'post'],'role_master/edit/{Id}', [MasterRoleController::class,'add_edit'])->name('edit_role_master');
    Route::match (['get'], 'role_master/delete/{id}', [MasterRoleController::class, 'delete_role_master'])->name('delete_role_master');
    Route::get('/role_master/clear_search', [MasterRoleController::class, 'clear_search'])->name('clear_search_role_master');
    Route::match(['get', 'post'], 'role_master/change_status', [MasterRoleController::class, 'change_active_status'])->name('change_status_role_master');
 
    #LOGOUT
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Role Controller
    Route::get('/role_access/{id}', [RoleController::class, 'index'])->name('role_master.index');
    Route::post('/role_access/{id}', [RoleController::class, 'update'])->name('role_master.update');

    // Organiser
    Route::match(['get', 'post'], '/organiser_master', [OrganiserController::class, 'index'])->name('organiser_index');
    Route::match(['get', 'post'],'organiser_master/add', [OrganiserController::class,'add_edit'])->name('add_organiser');
    Route::match(['get', 'post'],'organiser_master/edit/{Id}', [OrganiserController::class,'add_edit'])->name('edit_organiser');
    Route::get('/organiser_master/clear_search', [OrganiserController::class, 'clear_search'])->name('clear_search_organiser_master');
    Route::match(['get'], 'organiser_master/delete/{id}', [OrganiserController::class, 'delete_organiser'])->name('delete_organiser');
    Route::match(['get', 'post'],'/ckeditor_organiser/upload', [OrganiserController::class, 'upload'])->name('ckeditor_organiser.upload');
    Route::match(['get', 'post'],'/ckeditor_testimonial_description/upload', [TestimonialController::class, 'upload'])->name('ckeditor_testimonial_description.upload');
    
    // Audit Log
    Route::match(['get', 'post'], '/audit_log', [AuditLogController::class, 'index'])->name('audit_log_index');
    Route::get('/audit_log/clear_search', [AuditLogController::class, 'clear_search'])->name('clear_search_audit_log');
    
    // CMS Master
    Route::match(['get', 'post'], '/master_cms', [MasterCMSController::class, 'index'])->name('master_cms_index');
    Route::match(['get', 'post'],'master_cms/add', [MasterCMSController::class,'add_edit'])->name('add_master_cms');
    Route::match(['get', 'post'],'master_cms/edit/{Id}', [MasterCMSController::class,'add_edit'])->name('edit_master_cms');
    Route::get('/master_cms/clear_search', [MasterCMSController::class, 'clear_search'])->name('clear_search_master_cms');
    Route::match(['get'], 'master_cms/delete/{id}', [MasterCMSController::class, 'delete_master_cms'])->name('delete_master_cms');
    Route::match(['get', 'post'],'/ckeditor_master_cms/upload', [MasterCMSController::class, 'upload'])->name('ckeditor_master_cms.upload');
    Route::match(['get', 'post'], 'master_cms/change_status', [MasterCMSController::class, 'change_active_status'])->name('change_status_master_cms');

    // Participant Work Upload
    Route::match(['get', 'post'], '/participan_work_upload', [ParticipantBulkController::class, 'index'])->name('participan_work_upload_index');
    Route::get('/participan_work_upload/clear_search', [ParticipantBulkController::class, 'clear_search'])->name('clear_search_participan_work_upload');
    Route::match(['get','post'],'participan_work_upload/export_download',[ParticipantBulkController::class,'export_event_participants_work'])->name('export_event_participants_work');
    Route::match(['get', 'post'], '/participan_bulk_upload/import_participant', [ParticipantBulkController::class, 'event_participan_bulk_upload'])->name('event_participan_bulk_upload');
    Route::match (['get'], 'participan_bulk_upload/delete/{id}', [ParticipantBulkController::class, 'delete_participant'])->name('delete_participant');

    // Races Mode
    Route::match (['get', 'post'], 'index_mode', [MaintenanceModeController::class, 'index_mode'])->name('index_mode');

    Route::match (['get', 'post'], 'user/change_status_mode', [MaintenanceModeController::class, 'change_active_status_mode'])->name('change_status_mode_user');

});
