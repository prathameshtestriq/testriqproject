@extends('layout.index')
@section('title', 'Admin Dashboard')
@section('content')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    {{-- <script src="https://code.highcharts.com/highcharts.js"></script>
   <script src="https://code.highcharts.com/modules/exporting.js"></script>
   <script src="https://code.highcharts.com/modules/accessibility.js"></script>  --}}

   <script src={{ asset('app-assets/js/scripts/highcharts/highcharts.js') }}></script>
   <script src={{asset('app-assets/js/scripts/highcharts/exporting.js') }}></script>
   <script src={{asset('app-assets/js/scripts/highcharts/accessibility.js') }}></script> 


     <style>
         .highcharts-figure,
         .highcharts-data-table table {
            min-width: 320px;
            max-width: 660px;
            margin: 1em auto;
         }

         .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
         }

         .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
         }

         .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
         }

         .highcharts-data-table td,
         .highcharts-data-table th,
         .highcharts-data-table caption {
            padding: 0.5em;
         }

         .highcharts-data-table thead tr,
         .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
         }

         .highcharts-data-table tr:hover {
            background: #f1f7ff;
         }
      </style>

   
    <!-- header dashboard -->

    <!-- end -->

    <!-- Dashboard Ecommerce ends -->

   <section>

        <div class="row match-height pt-2">
            <div class="col-xl-12 col-md-12 col-12">
               <div class="card card-statistics">
                    <div class="card-header">
                        <h4 class="card-title"><b><i class="fa fa-search"></i> Search Filter</b></h4>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-sm-12">
                           <form class="dt_adv_search" action="{{ url('dashboard') }}" method="POST">
                              @csrf
                              <input type="hidden" name="form_type" value="search_dashboard">
                              <div class="card mb-1" style="border-radius:15px;">
                                 <div class="card-body" style="padding: 0.5rem 0.5rem">
                                       <div class="row justify-content-between mx-1">
                                          <div class="" style="min-width: 210px;">
                                             <div class="form-group mb-0">
                                                <?php
                                                $Filters = array('today', 'week', 'month');
                                                ?>
                                                <lable class="text-dark"> Filter</label>
                                                <select id="search_filter" name="search_filter"
                                                   class="form-control select2 form-control">
                                                   <option value="">Select Status</option>
                                                   <?php 
                                                      foreach ($Filters as $key => $value)
                                                      {
                                                         $selected = '';
                                                         if(old('search_filter',$search_filter) == $value){
                                                            $selected = 'selected';
                                                         }
                                                         ?>
                                                   <option value="<?php echo $value; ?>" <?php echo $selected; ?>>
                                                         <?php echo $value; ?></option>
                                                   <?php 
                                                      }
                                                   ?>
                                                </select>
                                             </div>
                                          </div>
                                          <div class="" style="min-width: 210px;">
                                             <div class="form-group mb-0">
                                                   <lable class="text-dark"> Category</label>

                                                      <select id="category" name="category"
                                                         class="form-control select2 form-control">
                                                         <option value="">Select Category</option>
                                                         <?php 
                                                            foreach ($TicketsData as $value)
                                                            {
                                                               $selected = '';
                                                               if(old('category',$search_category) == $value->id){
                                                                  $selected = 'selected';
                                                               }
                                                               ?>
                                                               <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->ticket_name; ?></option>
                                                               <?php 
                                                            }
                                                         ?>
                                                      </select>
                                                      <small class="text-danger" id="chart_country_err"></small>
                                             </div>
                                          </div>
                                          <div class="" style="min-width: 210px;">
                                             <div class="form-group mb-0">
                                                   <lable class="text-dark"> Event Name</label>
                                                      <select id="event_name" name="event_name"
                                                         class="form-control select2 form-control">
                                                         <option value="">Select Event Name</option>
                                                         <?php 
                                                            foreach ($EventsData as $value)
                                                            {
                                                               $selected = '';
                                                               if(old('event_name',$search_event_name) == $value->id){
                                                                  $selected = 'selected';
                                                               }
                                                               ?>
                                                               <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->name; ?></option>
                                                               <?php 
                                                            }
                                                         ?>
                                                      </select>
                                             </div>
                                          </div>

                                          <div class="" style="min-width: 140px;">
                                             <div class="form-group mb-0">
                                                   <lable class="text-dark">Date From</label>
                                                      <input type="datetime-local" id="from_date" class="form-control"
                                                         placeholder="From Date" name="from_date" value="{{ old('start_booking_date', $search_from_date ? \Carbon\Carbon::parse($search_from_date)->format('Y-m-d\TH:i') : '') }}"
                                                         autocomplete="off" />
                                             </div>
                                          </div>

                                          <div class="" style="min-width: 140px;">
                                             <div class="form-group mb-0">
                                                   <lable class="text-dark">To From</label>
                                                      <input type="datetime-local" id="to_date" class="form-control"
                                                         placeholder="To Date" name="to_date" value="{{ old('end_booking_date', $search_to_date ? \Carbon\Carbon::parse($search_to_date)->format('Y-m-d\TH:i') : '') }}"
                                                         autocomplete="off" />
                                             </div>
                                          </div>

                                          <div class="" style="min-width: 190px;">
                                             <div class="form-group mb-0">
                                                   <lable><br></label>
                                                      <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">Search</button>
                                                {{-- @if (!empty($search_banner) || !empty($search_start_booking_date) || !empty($search_end_booking_date) || ($search_banner_status != '')) --}}
                                                    <a title="Clear" href="{{url('dashboard/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                {{-- @endif --}}
                                             </div>
                                          </div>
                                       </div>
                                 </div>
                              </div>
                           </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
         <div class="row pr-2">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
               <div class="card-body" style="padding: 0.6rem 0.6rem">
                  <div class="row justify-content-between mx-1">
                     <!-- Registrations Successful  -->
                     <div class="text-primary mb-0" style="min-width: 390px;">  
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Registrations Successful</h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark">{{ $TotalRegistrationUsersWithSuccess }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/registration_Successful.jpeg') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                     <!-- Participants  -->
                     <div class="text-primary mb-0" style="min-width: 390px;">     
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Participants </h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark">{{ $NetSales }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/participant.jpeg') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div>     
                     <!-- Net Sales -->
                     <div class="text-primary mb-0" style="min-width:390px;">  
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Net Sales </h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark">{{ $NetSales }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/net_sales.png') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                     <!-- Net Earnings  -->
                     <div class="text-primary mb-0" style="min-width:390px;">     
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Net Earnings</h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark"> <i class="fa fa-inr" aria-hidden="true"></i>{{ $TotalAmount }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/net_earning.png') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                     <!-- Coversion Rate -->  
                     <div class="text-primary mb-0 mt-2" style="min-width:390px;">  
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Coversion Rate </h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark">{{ $SuccessPercentage }} %</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/conversion_rate.png') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                     <!-- Page Views  -->
                     <div class="text-primary mb-0 mt-2" style="min-width:390px;">     
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Page Views </h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark">{{ $TotalPageViews }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/page_view.png') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                     <!-- Total Registrations -->
                     <div class="text-primary mb-0 mt-2" style="min-width:390px;">  
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Total Registrations </h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark">{{ $TotalRegistrationCount }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/total_registration.png') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                     <!-- Total Events  -->
                     <div class="text-primary mb-0 mt-2" style="min-width:390px;">     
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Total Events</h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark">{{ $TotalNumberEvents }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/event.jpg') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                     <!--Live Events -->
                     <div class="text-primary mb-0 mt-2" style="min-width:390px;">  
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Live Events</h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark">{{ $TotalNumberLiveEvents }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/liveevent.png') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                     <!-- Draft Events  -->
                     <div class="text-primary mb-0 mt-2" style="min-width:390px;">     
                        <!-- Draft Events -->
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Draft Events</h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark">{{ $TotalNumberDraftEvents }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/draft_event.png') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                     <!-- Private Events  -->
                     <div class="text-primary mb-0 mt-2" style="min-width:390px;">  
                        <!-- Private Events -->
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Private Events </h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark">{{ $TotalNumberPrivateEvents }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/private_event.jpeg') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                     <!-- Users  -->
                     <div class="text-primary mb-0 mt-2" style="min-width:390px;">     
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Users</h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark"> {{ $TotalNumberUsers }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/user.png') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                     <!-- Remitted Amount --> 
                     <div class="text-primary mb-0 mt-2" style="min-width:390px;">  
                        <!-- Remitted Amount -->
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Remitted Amount </h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark"><i class="fa fa-inr" aria-hidden="true"></i>{{ $TotalRemittedAmount }}</h1>
                                             {{-- <p class="m-1 text-center" style="font-weight:500">New farmers registered in </p> --}}
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/total_registration.png') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 {{-- <div class="dashboard-card-min canvas1 mt-1" id=""></div> --}}
                              </div>
                           </div>
                        </div>
                     </div> 
                      <!-- Payment  -->
                     <div class="text-primary mb-0 mt-2" style="min-width:390px;">     
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                           <div class="card card-equal-height m-0 custom-highlight-bg">
                              <div class="card-body ">
                                 <h5 class="text-primary mb-0 text-dark">Payment</h5>
                                 <div class="row mb-0">
                                       <div class="col-sm-6">
                                          <div>
                                             <h1 style="font-size: 25px; font-weight:bold" class="mt-1 d-flex align-items-center justify-content-center text-dark">{{ $TotalNumberDraftEvents }}</h1>
                                             <p class="text-primary canvas1 mt-2" style="font-weight:500"><a href="{{url('/payment_log')}}">View Details</a></p>
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="d-flex align-items-center justify-content-center">
                                             <img src="{{ asset('uploads/dashboard/draft_event.png') }}" alt="avatar" height="100" width="100">
                                          </div>
                                       </div>
                                 </div>
                                 <div class="dashboard-card-min canvas1 mt-1" id=""></div>
                              </div>
                           </div>
                        </div>
                     </div> 
                  

                     
                  </div>
               </div>     
            </div>  
         </div>


         <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
            <div class="row pt-2">
                  <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                    <div class="card">
                        <div class="card-body">
                          <div class="row">
                             <div class="col-md-12">
                                 <div class="row">
                                    <div class="col-md-8">
                                       <h5 class="text-primary  pull-left ">Category Wise</h5>
                                        <br/><br/><br/>
                                    </div>
                                 </div> 
                             </div>
                          </div>
                         <?php if(!empty($search_event_name) && !empty($BookingData)){ ?>
                           <div id="container"></div>
                           <p class="highcharts-description">
                              <h5 class="text-primary  pull-left "></h5> 
                           </p>
                        <?php }else{ ?>
                           <center>
                           <img src="{{ asset('uploads/event_images/no-events.png') }}" width="130px" height="130px" alt="">
                        </center>
                        <?php } ?>   
                        </div>
                    </div>
                  </div>

                  <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                     <div class="card">
                         <div class="card-body">
                           <div class="row">
                              <div class="col-md-12">
                                  <div class="row">
                                     <div class="col-md-8">
                                        <h5 class="text-primary  pull-left ">Category Booking Data</h5>
                                         <br/><br/><br/>
                                     </div>
                                  </div> 
                              </div>
                           </div>
                           <figure class="highcharts-figure">
                              <div class="highcharts-data-table"><br/>
                                  <table>
                                    
                                      <thead>
                                          <tr>
                                             <th> Id</th>
                                              <th>Category</th>
                                              <th>Total Collection</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                       <?php 
                                       if (!empty($BookingData)){
                                           $i = 0;?>
                                       <?php foreach ($BookingData as $val){
                                                   $i++;?>
                                          <tr>
                                              <td>{{$i }}</td>
                                              <td>{{$val->TicketName}}</td>
                                              <td>{{ $val->TotalTicketPrice }}</td>
                                          </tr>
                                          <?php }
                                 }else{?>
                                 <tr>
                                     <td colspan="8" style="text-align:center; color:red;">No Record Found</td>
                                 </tr>
                                 <?php }?>
                                      </tbody>
                                  </table>
                              </div>
                          </figure>                  
                         </div>
                     </div>
                  </div>
 

            </div>
         </div>




       
       
   </section>
   
   <script>
       document.addEventListener('DOMContentLoaded', function () {
         const bookingData = <?php echo json_encode($BookingData); ?>;
         const chartData = bookingData.map(item => ({
            name: item.TicketName,
            y: parseFloat(item.TotalTicketPrice)
         }));

            Highcharts.chart('container', {
               chart: {
                  type: 'pie',
                  custom: {},   
               },
               
               title: {
                  text: 'category'
               },
               
               legend: {
                  enabled: true
               },
               plotOptions: {
                  series: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        borderRadius: 8,
                        dataLabels: [{
                           enabled: true,
                           distance: 20,
                           format: '<b>{point.name}</b>: {point.y:.1f} %'
                        }],
                        showInLegend: true
                  }
               },
               
               series: [{
                     name: 'Registrations',
                     colorByPoint: true,
                     innerSize: '75%',
                     name: 'Total Collection',
                     data: chartData
                  }]
            });

      });   

   </script>

  
@endsection
