<?php

use App\Http\Controllers\CommercialDashboardController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldExecutiveController;
use App\Http\Controllers\MasterBrandController;
use App\Http\Controllers\MasterFarmerController;
use App\Http\Controllers\MasterKpiController;
use App\Http\Controllers\MasterProgramController;
use App\Http\Controllers\MasterQuestionController;
use App\Http\Controllers\ProgramQuestionController;
use App\Http\Controllers\ProgramformController;
use App\Http\Controllers\ProgramTabController;
use App\Http\Controllers\UploadFarmerController;
use App\Http\Controllers\UploadFarmerSeasonController;
use Illuminate\Support\Facades\Route;

Route::get('/admin', function () {

    if (!Session::has('logged_in.user_login')) {

        return view('login');
    }
    return redirect('/dashboard');
 //   Route::redirect('/admin', '/admin/dashboard');
});

// EVENT ROUTE
Route::match(['get', 'post'],'/event', [EventController::class, 'index'])->name('event_index');
Route::match(['get', 'post'],'/event/add', [EventController::class,'add_edit'])->name('add_event');
Route::match(['get', 'post'],'event/edit/{id}', [EventController::class,'add_edit'])->name('edit_event');
Route::post('/login', [LoginController::class, 'index']);
// Route::post('/athlete_login', [LoginController::class, 'athlete_login']);

/***************ADMIN ROUTES ***********************/
Route::group(['middleware' => ['checkLogin']], function () {
    // dd('here');
    // DASHBOARD
    Route::match(['get','post'],'/dashboard', [DashboardController::class, 'dashboard_details'])->name('dashboard_details');
    Route::match(['get','post'],'/dashboard/overview', [DashboardController::class, 'dashboard_overview_details'])->name('dashboard_overview_details');
    Route::match(['get','post'],'/dashboard/health_and_safety', [DashboardController::class, 'dashboard_health_safty_details'])->name('dashboard_health_safty_details');
    Route::match(['get','post'],'/dashboard/sustainable_practices', [DashboardController::class, 'dashboard_sustainable_practices_details'])->name('dashboard_sustainable_practices_details');
    Route::match(['get','post'],'/dashboard/women_empowerment', [DashboardController::class, 'dashboard_women_empowerment_details'])->name('dashboard_women_empowerment_details');
    Route::match(['get','post'],'/dashboard/decision_making', [DashboardController::class, 'dashboard_decision_making_details'])->name('dashboard_decision_making_details');
    Route::match(['get','post'],'/dashboard/social_fairness', [DashboardController::class, 'dashboard_social_fairness_details'])->name('dashboard_social_fairness_details');
    Route::match(['get','post'],'/dashboard/trust_level', [DashboardController::class, 'dashboard_trust_level_details'])->name('dashboard_trust_level_details');

    Route::get('/commercial_dashboard', [CommercialDashboardController::class, 'commercial_dashboard'])->name('commercial_dashboard');
    Route::match(['get', 'post'],'/download_pdf',[CommercialDashboardController::class, 'download_pdf']);
    Route::get('/logout',[LoginController::class, 'logout']);

    /* USER ROUTE */
    Route::match(['get', 'post'],'/users', [UserController::class, 'index'])->name('user_index');
    Route::match(['get', 'post'],'/user/add_edit/{id?}', [UserController::class,'add_edit'])->name('add_user');
    Route::match(['get', 'post'],'get_country_info', [UserController::class,'get_country_info'])->name('get_country_info');
    Route::match(['get','post'],'user/change_status', [UserController::class,'change_active_status'])->name('change_status_user');
    Route::match(['get'],'user/delete/{id}', [UserController::class,'delete_user'])->name('delete_user');
    Route::get('user/clear_search', [UserController::class,'clear_search'])->name('clear_search_user');

      /* FIELD EXECUTIVE ROUTE */
    //   Route::match(['get', 'post'],'/field_executive', [FieldExecutiveController::class, 'index'])->name('user_index');
    //   Route::match(['get', 'post'],'/field_executive/add_edit/{id?}', [FieldExeCutiveController::class,'add_edit'])->name('add_user');
    //   Route::match(['get', 'post'],'get_country_info', [UserController::class,'get_country_info'])->name('get_country_info');
    //   Route::match(['get','post'],'user/change_status', [UserController::class,'change_active_status'])->name('change_status_user');
    //   Route::match(['get'],'user/delete/{id}', [UserController::class,'delete_user'])->name('delete_user');
    //   Route::get('user/clear_search', [UserController::class,'clear_search'])->name('clear_search_user');

    /* Master Question */
    Route::match(['get', 'post'],'/master_questions', [MasterQuestionController::class, 'index'])->name('master.form');
    Route::match(['get', 'post'],'master_questions/add_edit/{id?}', [MasterQuestionController::class, 'master_form'])->name('master.add_form');
    Route::match(['get', 'post'],'master_questions/view/{id?}', [MasterQuestionController::class, 'view_form'])->name('master.view_form');
    Route::match(['get', 'post'],'master_questions/add_language/{id}', [MasterQuestionController::class, 'add_language'])->name('master.form_save');
    Route::match(['get'],'master_questions/delete/{id}', [MasterQuestionController::class,'delete_master'])->name('delete_master');
    Route::get('master_questions/clear_search', [MasterQuestionController::class,'clear_search'])->name('clear_search_question');
    Route::match(['get', 'post'],'master_questions/link/{id}', [MasterQuestionController::class,'link_table'])->name('link_table');
    Route::match(['get', 'post'],' master_questions/delete_language/{id}', [MasterQuestionController::class,'delete_language'])->name('delete_language');
    Route::match(['get', 'post'],' option/status_change', [MasterQuestionController::class,'status_change'])->name('status_change');
    Route::match(['get', 'post'],' option/add_child_question', [MasterQuestionController::class,'add_child_question'])->name('add_child_question');


    // Master Brand
    Route::match(['get', 'post'],'/master_brands', [MasterBrandController::class, 'index'])->name('master_brand.list');
    Route::match(['get', 'post'],'/master_brands/add', [MasterBrandController::class,'add_edit'])->name('master_brand.add');
    Route::match(['get', 'post'],'/master_brands/edit/{id}', [MasterBrandController::class,'add_edit'])->name('master_brand.edit');
    Route::match(['get'],'/master_brands/remove/{id}', [MasterBrandController::class,'delete'])->name('delete.master_brand');
    Route::get('/master_brands/view/{id}',[MasterBrandController::class,'view'])->name('program_details');
    Route::match(['get'],'/master_brands/program/remove/{id}', [MasterBrandController::class,'brand_program_delete']);
    Route::match(['get', 'post'],'/master_brands/brand_program_ajx/{id}', [MasterBrandController::class,'brand_program_ajx_add']);
    Route::match(['get','post'],'master_brands/change_status', [MasterBrandController::class,'change_active_status'])->name('change_status_master_brand');

    // Master Program
    Route::match(['get', 'post'],'/master_programs', [MasterProgramController::class, 'index'])->name('master_programs.list');
    Route::match(['get', 'post'],'/master_programs/add', [MasterProgramController::class,'add_edit'])->name('master_programs.add');
    Route::match(['get', 'post'],'/master_programs/edit/{id}', [MasterProgramController::class,'add_edit'])->name('master_programs.edit');
    Route::match(['get'], '/master_programs/remove/{id}', [MasterProgramController::class,'delete'])->name('delete.master_programs');
    Route::match(['get','post'],'master_programs/change_status', [MasterProgramController::class,'change_active_status'])->name('change_status_master_program');


    /* Upload Farmers */
    Route::match(['get','post'], '/upload-farmers', [UploadFarmerController::class, 'upload_farmer']);
    Route::match(['get','post'], 'import_farmers', [UploadFarmerController::class, 'import_farmer']);

    // Program Tabs
    Route::match(['get', 'post'],'/program_tabs', [ProgramTabController::class, 'index'])->name('program_tabs.list');
    Route::match(['get', 'post'],'/program_tabs/add', [ProgramTabController::class,'add_edit'])->name('program_tabs.add');
    Route::match(['get', 'post'],'/program_tabs/edit/{id}', [ProgramTabController::class,'add_edit'])->name('program_tabs.edit');
    Route::match(['get'], '/program_tabs/remove/{id}', [ProgramTabController::class,'delete'])->name('delete.program_tabs');
    Route::get('program_tabs/clear_search', [ProgramTabController::class,'clear_search'])->name('clear_search_program_tabs');
    Route::match(['get', 'post'],'/program_tabs/program_form_ajx/{id}', [ProgramTabController::class,'program_form_add_view']);
    Route::match(['get'],'/program_tabs/program_form/remove/{id}', [ProgramTabController::class,'program_form_delete']);

    /* Upload/Download Data */
    Route::match(['get','post'], 'upload_data', [UploadFarmerController::class, 'upload_data']);
    Route::match(['get', 'post'], 'download/farmer_sample',[UploadFarmerController::class,'download_sample']);
    Route::match(['get','post'], 'fetch_program', [UploadFarmerController::class, 'fetch_program']);
    Route::match(['get','post'], 'fetch_tab', [UploadFarmerController::class, 'fetch_tab']);

    Route::match(['get','post'], 'fetch_forms', [UploadFarmerController::class, 'fetch_forms']);

    // master_farmers
    Route::match(['get', 'post'],'/master_farmers', [MasterFarmerController::class, 'index'])->name('master_farmers.list');
    Route::match(['get', 'post'],'/master_farmers/fetch_countries/{country_id}', [MasterFarmerController::class, 'master_index'])->name('master_index');
    Route::match(['get','post'], '/master_farmers/get_master_farmer/', [MasterFarmerController::class, 'get_master_farmer']);
    Route::match(['get','post'], '/master_farmers/edit_master_farmer/{country_id}/{farmer_id}/{program_id}', [MasterFarmerController::class, 'edit_master_farmer'])->name('edit_master_farmer');

    /*Program Form Question*/
    Route::match(['get','post'], 'question_program', [ProgramQuestionController::class, 'question_program']);

    Route::match(['get','post'], 'change_text', [ProgramQuestionController::class, 'change_text']);

    /*KPI*/
    Route::match(['get','post'], 'master_kpi', [MasterKpiController::class, 'list_kpi']);
    Route::match(['get','post'], 'add_question/{id}', [MasterKpiController::class, 'add_question']);
    Route::match(['get','post'], 'kpi_question/remove/{kpi_id}/{que_id}', [MasterKpiController::class, 'remove_question']);

    /*Upload season farmer */
    Route::match(['get','post'], '/upload_farmer_season', [UploadFarmerSeasonController::class, 'import_farmer_season']);
    Route::match(['get','post'], 'download/farmer_season_sample', [UploadFarmerSeasonController::class, 'download_sample']);


});
Route::match(['post'], 'get_question_program', [ProgramQuestionController::class, 'get_question_program']);
