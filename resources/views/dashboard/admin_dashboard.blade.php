@extends('layout.index')
@section('title', 'Dashboard')
@section('content')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    {{-- <script src="https://code.highcharts.com/highcharts.js"></script>
   <script src="https://code.highcharts.com/modules/exporting.js"></script>
   <script src="https://code.highcharts.com/modules/accessibility.js"></script>  --}}

    <script src={{ asset('app-assets/js/scripts/highcharts/highcharts.js') }}></script>
    <script src={{ asset('app-assets/js/scripts/highcharts/exporting.js') }}></script>
    <script src={{ asset('app-assets/js/scripts/highcharts/accessibility.js') }}></script>


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

        /* hover */

         /* Default style for the <h1> */
        .hover-effect {
            font-size: 20px;
            font-weight: bold;
            color: #000; 
            transition: color 0.3s ease; 
        }

        /* Hover effect: Change color */
        .hover-effect:hover {
            color: #007bff; 
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

                                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-4 col-xxl-2">
                                                <div class="form-group mb-0">
                                                    <?php
                                                    $Filters = ['Today', 'Week', 'Month'];
                                                    ?>
                                                    <lable class="text-dark"> Filter</label>
                                                        <select id="search_filter" name="search_filter"
                                                            class="form-control select2 form-control">
                                                            <option value="">Select Filter</option>
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

                                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-4 col-xxl-2">
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
                                                            <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>>
                                                                <?php echo $value->ticket_name; ?></option>
                                                            <?php 
                                                            }
                                                         ?>
                                                        </select>
                                                        <small class="text-danger" id="chart_country_err"></small>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-4 col-xxl-2">
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
                                                            <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>>
                                                                <?php echo $value->name; ?></option>
                                                            <?php 
                                                            }
                                                         ?>
                                                        </select>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-4 col-xxl-2">
                                                <div class="form-group mb-0">
                                                    <lable class="text-dark">From Date</label>
                                                        <input type="date" id="from_date" class="form-control"
                                                            placeholder="From Date" name="from_date"
                                                            value="{{ old('start_booking_date', $search_from_date ? \Carbon\Carbon::parse($search_from_date)->format('Y-m-d') : '') }}"
                                                            autocomplete="off" />
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-4 col-xxl-2">
                                                <div class="form-group mb-0">
                                                    <lable class="text-dark">To Date</label>
                                                        <input type="date" id="to_date" class="form-control"
                                                            placeholder="To Date" name="to_date"
                                                            value="{{ old('end_booking_date', $search_to_date ? \Carbon\Carbon::parse($search_to_date)->format('Y-m-d') : '') }}"
                                                            autocomplete="off" />
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-4 col-xxl-2">
                                                <div class="form-group mb-0">
                                                    <lable><br></label>
                                                        <button type="submit"
                                                            class="btn btn-primary waves-effect waves-float waves-light">Search</button>
                                                        <?php if((!empty($search_filter) || !empty($search_category) || !empty($search_event_name) || !empty($search_from_date) || !empty($search_to_date))){ ?>
                                                            <a title="Clear" href="{{ url('dashboard/clear_search') }}"
                                                                type="button" class="btn btn-outline-primary">
                                                                <i data-feather="rotate-ccw" class="me-25"></i> Clear
                                                            </a>
                                                        <?php } ?>
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


        <div class="row">
             <!-- Total Events  -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Total Events</h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                        {{ $TotalNumberEvents }}</h1>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/8-total-events.png') }}" alt="avatar" height="80"
                                        width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Live Events -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Public Events</h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    {{--  <h1 style="font-size: 20px; font-weight:bold; color:rgb(103, 157, 238) !important" class="mt-1 text-dark"> --}}
                                    <?php if($search_event_name > 0){ ?>
                                    <a href="{{ url('redirect_to_dashboard/' . $search_event_name . '/1') }}" title="Click Here"><h1 class="hover-effect">
                                        {{  $TotalNumberLiveEvents }}</h1></a>
                                    <?php }else{ ?>
                                        <a href="{{ url('redirect_to_dashboard/0/1') }}" title="Click Here"><h1 class="hover-effect">
                                            {{  $TotalNumberLiveEvents }}</h1></a>
                                    <?php } ?>     
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/9-live-events.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Draft Events -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Draft Events</h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <?php if($search_event_name > 0){ ?>
                                    <a href="{{ url('redirect_to_dashboard/' . $search_event_name . '/3') }}" title="Click Here"><h1 class="hover-effect">
                                        {{  $TotalNumberDraftEvents }}</h1></a>
                                    <?php }else{ ?>
                                        <a href="{{ url('redirect_to_dashboard/0/3') }}" title="Click Here"><h1 class="hover-effect">
                                            {{  $TotalNumberDraftEvents }}</h1></a>
                                    <?php } ?>   
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/10-draft-events.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Private Events -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Private Events </h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <?php if($search_event_name > 0){ ?>
                                    <a href="{{ url('redirect_to_dashboard/' . $search_event_name . '/2') }}" title="Click Here"><h1 class="hover-effect">
                                        {{ $TotalNumberPrivateEvents }}</h1></a>
                                    <?php }else{ ?>
                                        <a href="{{ url('redirect_to_dashboard/0/2') }}" title="Click Here"><h1 class="hover-effect">
                                            {{ $TotalNumberPrivateEvents }}</h1></a>
                                    <?php } ?>    
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/11-private-events.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <!-- Total Registrations -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Total Registrations </h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                        {{ $TotalRegistrationCount }}</h1>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/7-total-registrations (2).png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Registrations Successful  -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Registrations Successful</h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">

                                <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                    {{ $TotalSuccessRegistration }}</h1>  
                                    <?php if($search_event_name > 0){ ?>
                                        <p class="text-primary canvas1 mt-2"
                                        style="font-weight:500; position: absolute; bottom: -45px;"><a
                                            href="{{ url('/registration_successful/'.$search_event_name.'/1') }}">View Details</a></p>
                                    <?php }else{ ?>
                                        <p class="text-primary canvas1 mt-2"
                                        style="font-weight:500; position: absolute; bottom: -45px;"><a
                                            href="{{ url('/registration_successful/'.$search_event_name) }}">View Details</a></p>
                                    <?php } ?>         
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/1-registration-successful.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participants  -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Total Participants </h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                        {{ $NetSales }}</h1>
                                        <?php if($search_event_name > 0){ ?>
                                        <p class="text-primary canvas1 mt-2"
                                        style="font-weight:500; position: absolute; bottom: -45px;"><a
                                            href="{{ url('/participants_event/'.$search_event_name.'/1') }}">View Details</a></p>
                                        <?php }else{ ?>
                                            <p class="text-primary canvas1 mt-2"
                                        style="font-weight:500; position: absolute; bottom: -45px;"><a
                                            href="{{ url('/participants_event/'.$search_event_name) }}">View Details</a></p>  
                                        <?php } ?>     

                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/2-participants.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             {{-- <!-- Page Views  -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Page Views </h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                        {{ $TotalPageViews }}</h1>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/6-pages-views.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

             <!-- Coversion Rate -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Conversion Rate </h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                        {{ $SuccessPercentage }} %</h1>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/5-converstion-rate.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Sales -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Net Sales </h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                        {{ $NetSales }}</h1>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/3-net-sales.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Earnings  -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Net Earnings</h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark"> <i class="fa fa-inr" aria-hidden="true"> </i>
                                         <?php echo !empty($NetEarningAmt) ? $NetEarningAmt : $TotalAmount; ?> 
                                    </h1>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/4-net-earnings.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment  -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark"> Receivable to Organiser</h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                        <i class="fa fa-inr" aria-hidden="true"></i>  {{ $OrganiserAmount }}</h1>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/17-receivabletoorg.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Remitted Amount -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Remitted Amount </h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark"><i
                                            class="fa fa-inr" aria-hidden="true"></i>  {{ $TotalRemittedAmount }}
                                    </h1>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/13-remit.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment  -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Total Payment Gateway Charges</h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                        <i class="fa fa-inr" aria-hidden="true"></i>  {{ $TotalPaymentGateway }}</h1>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/15-payment-gateway.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment  -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Total Convenience Fee</h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                        <i class="fa fa-inr" aria-hidden="true"></i>   {{ $TotalConvenience }}</h1>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/16-convenience-fee.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       
            <!-- Users  -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Total Users</h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                        {{ $TotalNumberUsers }}</h1>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/12-users.png') }}" alt="avatar" height="80"
                                        width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       
            <!-- Payment  -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 my-1">
                <div class="card m-0 custom-highlight-bg">
                    <div class="card-body ">
                        <h5 class="text-primary mb-0 text-dark">Payment History</h5>
                        <div class="row align-items-center mb-0">
                            <div class="col-sm-6">
                                <div>
                                    <h1 style="font-size: 20px; font-weight:bold" class="mt-1 text-dark">
                                        {{ $PaymentData }}</h1>
                                    <p class="text-primary canvas1 mt-2"
                                        style="font-weight:500; position: absolute; bottom: -45px;"><a
                                            href="{{ url('/payment_log') }}">View Details</a></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('uploads/dashboard/14-payment.png') }}" alt="avatar"
                                        height="80" width="80">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

       <?php if(!empty($search_event_name)){ ?>
       
        <div class="row">
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6 my-1">
               <div class="card">
                   <div class="card-body">
                       
                        <div class="row">
                           <div class="col-md-12">
                               <h4 class="text-dark m-0">Registration Per Day</h4>
                           </div>
                        </div>
                       
                        <?php if(!empty($search_event_name) && !empty($BookingData)){ ?>
                           <div class="card-min-height d-flex align-items-center justify-content-center py-2">
                                <div id="top_x_div" style="width: 700px; height: 500px;"></div>
                           </div>
                        <?php }else{ ?>
                           <div class="card-min-height d-flex align-items-center justify-content-center py-2">
                               <img src="{{ asset('uploads/event_images/no-events.png') }}" width="auto" height="130px"
                               alt="">
                           </div>
                        <?php } ?>
                   </div>
               </div>
            </div>


        <!-- ----------------------------------------------------- -->

         <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6 my-1">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12">
                            <h4 class="text-dark m-0">Coupons</h4>
                            </div>
                        </div>
                    
                        <div class="card-min-height d-flex justify-content-center py-2">
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">Code</th>
                                        <th style="text-align: center;">Total</th>
                                        <th style="text-align: center;">Used</th>
                                        <th style="text-align: center;">Available</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                <?php 
                                    if (!empty($CouponCodes)){
                                    $totalDiscountCodeSum = 0;
                                    $couponCountSum = 0;
                                    $availableSum = 0;
                                    $i = 0;
                                ?>
                                    <?php foreach ($CouponCodes as $val){
                                    $available = $val->TotalDiscountCode - $val->CouponCount;
                                    $totalDiscountCodeSum += $val->TotalDiscountCode;
                                    $couponCountSum += $val->CouponCount;
                                    $availableSum += $available;
                                    $i++;
                                ?>
                                <tr>
                                    <td style="text-align: left;">{{ $val->DiscountCode }}</td>
                                    <td style="text-align: center;">{{ $val->TotalDiscountCode }}</td>
                                    <td style="text-align: center;">{{ $val->CouponCount }}</td>
                                    <td style="text-align: center;">{{ $available }}</td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td style="text-align: left;"><strong>Total</strong></td>
                                    <td style="text-align: center;"><strong>{{ $totalDiscountCodeSum }}</strong></td>
                                    <td style="text-align: center;"><strong>{{ $couponCountSum }}</strong></td>
                                    <td style="text-align: center;"><strong>{{ $availableSum }}</strong></td>
                                </tr>
                                <?php }else{ ?>
                                <tr>
                                    <td colspan="8" style="text-align:center; color:red;">No Record Found
                                    </td>
                                </tr>
                                <?php }?>
                                </tbody>
                            </table>
                            
                        </div>
                        </div>
                    </div>
                </div>
            </div>
     
     
           
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6 my-1">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                           <div class="col-md-12">
                              <h4 class="text-dark m-0">Category Booking Data</h4>
                           </div>
                        </div>
                      
                        <div class="card-min-height d-flex justify-content-center py-2">
                           <div class="table-responsive">
                              <table class="table table-striped table-bordered">
                                  <thead>
                                    <tr>
                                       <th>Category</th>
                                       <th style="text-align:center;">Total</th>
                                       <th style="text-align:center;">Used</th>
                                       <th style="text-align:center;">Pending</th>
                                       <!-- <th>Price</th> -->
                                       <th style="text-align:right;">Total Collection</th>
                                   </tr>
                                 </thead>
                                 <tbody class="text-center">
                                    <?php 
                                       if (!empty($BookingData)){
                                          $total_quantitySum = 0;
                                          $TicketCountSum =0;
                                          $PendingCountSum =0;
                                          // $SingleTicketPriceSum =0;
                                          $TotalTicketPriceSum =0;
                                          $i = 0;
                                    ?>
                                    <?php foreach ($BookingData as $val){
                                       $total_quantitySum += $val->total_quantity;
                                       $TicketCountSum += $val->TicketCount;
                                       $PendingCountSum += $val->PendingCount;
                                       // $SingleTicketPriceSum += $val->SingleTicketPrice;
                                       $TotalTicketPriceSum += $val->TotalAmount;
                                             
                                    ?>
                                    <tr>
                                        <td align="left">{{ $val->TicketName }}</td>
                                        <td>{{ $val->total_quantity }}</td>
                                        <td>{{ $val->TicketCount }}</td>
                                        <td>{{ $val->PendingCount }}</td>
                                        <!-- <td> <i class="fa fa-inr" aria-hidden="true"></i>
                                            {{ $val->SingleTicketPrice }}</td> -->
                                        <td align="right">  <i class="fa fa-inr" aria-hidden="true"></i> 
                                           <?php echo !empty($val->TotalAmount) ? number_format($val->TotalAmount,2) : '0.00' ?></td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td align="left"><strong>Total</strong></td>
                                        <td><strong>{{ $total_quantitySum }}</strong></td>
                                        <td><strong>{{ $TicketCountSum }}</strong></td>
                                        <td><strong>{{ $PendingCountSum }}</strong></td>
                                        <!-- <td><strong> </strong></td> -->
                                        <td align="right"><strong><i class="fa fa-inr" aria-hidden="true"></i>
                                           <?php echo !empty($TotalTicketPriceSum) ? number_format($TotalTicketPriceSum,2) : '0.00' ?> </strong></td>
   
   
                                    </tr>
   
                                    <?php  }else{?>
                                    <tr>
                                        <td colspan="8" style="text-align:center; color:red;">No Record Found
                                        </td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                              </table>
                              {{-- <div class="card-body">
                                  <div class="d-flex justify-content-end">
                                      {{ $Paginator->links() }}
                                  </div>
                              </div> --}}
                          </div>
                        </div>
                    </div>
                </div>
            </div>

            <!------------------------------------ Category Pi chart -------------------------------------------------------->
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6 my-1">
               <div class="card">
                   <div class="card-body">
                       
                       <div class="row">
                           <div class="col-md-12">
                               <h4 class="text-dark m-0">Number of Categories Sold</h4>
                           </div>
                       </div>
                       
                       <?php if(!empty($search_event_name) && !empty($BookingData)){ ?>
                       <div class="card-min-height d-flex align-items-center justify-content-center py-2">
                           {{-- Enter your data --}}
                           <div id="container"></div>
                       </div>
                       <?php }else{ ?>
                       <div class="card-min-height d-flex align-items-center justify-content-center py-2">
                        {{-- Enter your data --}}
                           <img src="{{ asset('uploads/event_images/no-events.png') }}" width="auto" height="130px"
                           alt="">
                       </div>
                       <?php } ?>
                   </div>
               </div>
           </div>

       <?php } ?>

       <?php if(!empty($search_event_name)){ ?>
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6 my-1">
                <div class="card">
                    <div class="card-body">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="text-dark m-0">Gender Wise</h4>
                            </div>
                        </div>
                    
                        <?php if(!empty($maleCount)||!empty($maleCount)||!empty($maleCount)){ ?>
                        <div class="card-min-height d-flex justify-content-center py-2">
                            {{-- Enter your data --}}
                            <div id="container_male_female"></div>      
                        </div>
                        <?php }else{ ?>
                            <div class="card-min-height d-flex align-items-center justify-content-center py-2">
                            {{-- Enter your data --}}
                                <img src="{{ asset('uploads/event_images/no-events.png') }}" width="auto" height="130px"
                                alt="">
                            </div>
                            <?php } ?>
                    </div>
                </div>
            </div>

           

        <!------------------------------------ UTM Campaigns / Age Category -------------------------------------------------------->
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6 my-1">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12">
                            <h4 class="text-dark m-0">UTM Campaigns Data</h4>
                            </div>
                        </div>
                
                        <div class="card-min-height d-flex justify-content-center py-2">
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">UTM Code</th>
                                        <th style="text-align: center;">Total Count</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                <?php 
                                    if (!empty($utmCode)){
                                       foreach ($utmCode as $val){
                                ?>
                                    <tr>
                                        <td style="text-align: left;">{{ $val->utm_campaign }}</td>
                                        <td style="text-align: center;">{{ $val->total_quantity }}</td>
                                    </tr>
                                <?php 
                                       } 
                                    }else{ 
                                ?>
                                <tr>
                                    <td colspan="2" style="text-align:center; color:red;">No Record Found
                                    </td>
                                </tr>
                                <?php }?>
                                </tbody>
                            </table>
                            
                           </div>
                        </div>
                  </div>
                </div>
            </div>
            

            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6 my-1">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12">
                            <h4 class="text-dark m-0">Age Category</h4>
                            </div>
                        </div>
                
                        <div class="card-min-height d-flex justify-content-center py-2">
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">Age Category</th>
                                        <th style="text-align: center;">Total Count</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                <?php 
                                    if (!empty($ageCategory)){
                                       foreach ($ageCategory as $val){
                                ?>
                                    <tr>
                                        <td style="text-align: left;">{{ $val->age_category }}</td>
                                        <td style="text-align: center;">{{ $val->count }}</td>
                                    </tr>
                                <?php 
                                       } 
                                    }else{ 
                                ?>
                                    <tr>
                                        <td colspan="2" style="text-align:center; color:red;">No Record Found
                                        </td>
                                    </tr>
                                <?php }?>
                                </tbody>
                            </table>
                            
                           </div>
                        </div>
                  </div>
                </div>
            </div>

        
        <!------------------------------------ Dnyanamic Quetion wise table data -------------------------------------------------->
        
     

            <?php 
                if(!empty($CountArray)){
                   foreach($CountArray as $key=>$label){
            ?>
                <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6 my-1">
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-12">
                                <h4 class="text-dark m-0"><?php echo $label['question_label']; ?></h4>
                                </div>
                            </div>
                            
                            <div class="card-min-height d-flex justify-content-center py-2">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="text-align: letf;">Label</th>
                                                <th style="text-align: center;">Count</th>
                                                <th style="text-align: center;">Limit</th>
                                            </tr>
                                        </thead>

                                        <tbody class="text-center">
                                            <?php 
                                                if (!empty($CountArray[$key])){
                                                    $total_count = $total_limit = 0;
                                                    foreach ($CountArray[$key] as $key1=>$val){
                                                        if($key1 != 'question_label'){
                                                           $limit = !empty($val['limit']) && isset($val['limit']) ? $val['limit'] : 0; 
                                                           $total_count += $val['count'];
                                                           $total_limit += $limit;
                                            ?>
                                                <tr>
                                                    <td style="text-align: left;"><?php echo $val['label']; ?></td>
                                                    <td style="text-align: center;"><?php echo $val['count']; ?></td>
                                                    <td style="text-align: center;"><?php echo $limit; ?></td>
                                                </tr>
                                            <?php 
                                                      }
                                                    }
                                            ?> 
                                                <tr>
                                                    <td style="text-align: left;"><strong>Total</strong></td>
                                                    <td style="text-align: center;"><strong><?php echo $total_count; ?></strong></td>
                                                    <td style="text-align: center;"><strong><?php echo $total_limit; ?></strong></td>
                                                </tr>  

                                            <?php }else{ 
                                            ?>
                                                <tr>
                                                    <td colspan="3" style="text-align:center; color:red;">No Record Found
                                                    </td>
                                                </tr>
                                        <?php }?>
                                        </tbody>
                                       
                                    </table>
                               </div>
                            </div>
                           
                        </div>
                    </div>
                </div>

            <?php 
                   }
                } 
            ?>
        </div>



       <?php } ?>

    </section>
 

    <script>
          document.addEventListener('DOMContentLoaded', function () {
            Highcharts.chart('top_x_div', {
              chart: {
                type: 'column' // This creates a horizontal bar chart
              },
              title: {
                text: 'Daily Category Count'
              },
              xAxis: {
                title: {
                  text: 'Categories'
                },
                categories: [<?php echo $FinalBarChartDateData; ?>],
              },
              yAxis: {
                title: {
                  text: 'Category'
                }
              },
              series: [{
                name: 'Categories',
                data: [<?php echo $FinalBarChartCountData; ?>] // Data points for each category
              }]
            });
          });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookingData = <?php echo json_encode($BookingData); ?>;
            const chartData = bookingData.map(item => ({
                name: item.TicketName,
                y: parseFloat(item.TicketCount)
            }));

            Highcharts.chart('container', {
                chart: {
                    type: 'pie',
                    custom: {},
                },

                title: {
                    text: 'Category Booking Data'
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
                            format: '{point.y:1f} '
                            // format: '<b>{point.name}</b>: {point.y:.1f} '
                        }],
                        showInLegend: true
                    }
                },

                series: [{
                    name: 'Registrations',
                    colorByPoint: true,
                    innerSize: '75%',
                    name: 'Total',
                    data: chartData
                }]
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maleCount = <?php echo json_encode($maleCount); ?>;
            const femaleCount = <?php echo json_encode($femaleCount); ?>;
            const otherCount = <?php echo json_encode($otherCount); ?>;

            // Setting up Highcharts color options
            // Highcharts.setOptions({
            //    colors: Highcharts.map(Highcharts.getOptions().colors, function (color) {
            //          return {
            //             radialGradient: {
            //                cx: 0.5,
            //                cy: 0.3,
            //                r: 0.7
            //             },
            //             stops: [
            //                [0, color],
            //                // [1, Highcharts.color(color).brighten(-0.3).get('rgb')] // darken
            //             ]
            //          };
            //    })
            // });
            Highcharts.setOptions({
                colors: Highcharts.map(['#2caffe', '#544fc5', '#00FF00', '#0000FF', '#FFFF00', '#FF0000'],
                    function(color) {
                        return {
                            radialGradient: {
                                cx: 0.5,
                                cy: 0.3,
                                r: 0.7
                            },
                            stops: [
                                [0, color],
                                // [1, Highcharts.color(color).brighten(-0.3).get('rgb')] // darken
                            ]
                        };
                    })
            });

            // Build the chart
            Highcharts.chart('container_male_female', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Gender Distribution',
                    align: 'center'
                },
                tooltip: {
                    pointFormat: '<b>{series.name}: {point.y}</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                        },
                        showInLegend: true
                    }
                },
              
                series: [{
                    name: 'Count',
                    data: [{
                            name: 'Male',
                            y: maleCount
                        },
                        {
                            name: 'Female',
                            y: femaleCount
                        },
                        {
                            name: 'Other',
                            y: otherCount
                        }
                    ]
                }]
            });
        });
    </script>


@endsection
