@extends('layout.index')
@section('title', 'Audit Log ')


@section('content')
    <section>
        <div class="content-body">
            <!-- Bordered table start -->
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header w-100">
                            <div class="content-header-left">
                                <div class="row breadcrumbs-top">
                                    <div class="col-sm-12">
                                        <h2 class="content-header-title float-left mb-0">Audit Log</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Audit Log </li>
                                        <li class="breadcrumb-item active" aria-current="page">Audit Log List</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bordered table end -->
        </div> 
       
        @if ($message = Session::get('success'))
            <div class="demo-spacing-0 mb-1">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-body">
                        <i class="fa fa-check-circle" style="font-size:16px;" aria-hidden="true"></i>
                        {!! $message !!}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @elseif ($message = Session::get('error'))
            <div class="demo-spacing-0 mb-1">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                        {!! $message !!}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif

        <div class="alert alert-success p-1" id="success-alert" style="display: none;">
            <i class="fa fa-check-circle" style="font-size:16px;" aria-hidden="true"></i>
            <span id="success-message"></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="alert alert-danger p-1" id="error-alert" style="display: none;">
            <i class="fa fa-exclamation-triangle" style="font-size:16px;" aria-hidden="true"></i>
            <span id="error-message"></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>


        <div class="content-body">
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card">
                        <form class="dt_adv_search" action="" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_audit_log">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="form-control">Transaction Id</label>
                                                <input type="text" id="search_transaction_id" class="form-control"
                                                    placeholder="Search Transaction Id" name="search_transaction_id" value="{{ $search_transaction_id }}"
                                                    autocomplete="off" <?php if(!empty($search_email_address_audit)) echo 'readonly'; ?> />
                                            </div>
                                            
                                            <div class="col-sm-3">
                                                <label for="form-control">Email Address</label>
                                                <input type="text" id="search_email_address" class="form-control"
                                                    placeholder="Search Email Address" name="search_email_address" value="{{ $search_email_address_audit }}"
                                                    autocomplete="off" <?php if(!empty($search_transaction_id)) echo 'readonly'; ?> />
                                            </div>
                                            <div class="col-sm-3 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_transaction_id) || !empty($search_email_address_audit))
                                                    <a title="Clear" href="{{ url('/audit_log/clear_search') }}"
                                                        type="button" class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- booking payment details --}}
                            <div class="row px-2">
                                <?php if((!empty($event_name))|| (!empty($user_name))){?>
                                    <div class="col-sm-12 float-right">
                                        <p  class="content-header-title float-left"><b>Event Name:</b><p>{{$event_name}}</p></p>
                                    </div>
                                    <div class="col-sm-12 float-right">
                                        <p  class="content-header-title float-left"><b>User Name:</b><p>{{$user_name}}</p></p>
                                    </div>
                                    <?php } ?>
                                    
                                <div class="col-sm-8 float-right">
                                    <h2 class="content-header-title float-left mb-0">Booking Payment Details</h2>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sr. No</th>
                                            <th class="text-center" >Id</th>
                                            <th class="text-center" >Event Id</th>
                                            <th class="text-left">Event Name</th>
                                            <th class="text-left">Transcation Id</th>
                                            {{-- <th class="text-left">User Name</th> --}}
                                            <th class="text-left">email</th>
                                            <th class="text-left">phone no</th>
                                            <th class="text-center">amount</th>
                                            <th class="text-left">Date</th>
                                            <th class="text-left">ticket name</th>
                                            <th class="text-center">registration id</th>
                                            <th class="text-center">send email flag</th>
                                            <th class="text-center">change status</th>
                                            <th class="text-left">payment status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($ad_log_array)) {
                                            $i =0;
                                            if (!empty($search_transaction_id) || !empty($search_email_address_audit)) {
                                                foreach ($ad_log_array as $val) {
                                                    $i++;
                                        ?>
                                                    <tr>
                                                        <td class="text-center">{{ $i }}</td>
                                                        <td class="text-center">{{ !empty($val->id) ? $val->id :'-' }}</td>
                                                        <td class="text-center">{{ !empty($val->event_id) ? $val->event_id :'-' }}</td>
                                                        <td class="text-center">{{ !empty($val->event_name) ? $val->event_name :'-' }}</td>
                                                        <td class="text-left">{{ !empty($val->txnid) ? $val->txnid :'-' }}</td>
                                                        {{-- <td class="text-center">{{ (!empty($val->firstname) || !empty($val->lastname)) ? $val->firstname.' '.$val->lastname :'-' }}</td> --}}
                                                        <td class="text-left">{{ !empty($val->email) ? $val->email :'-' }}</td>
                                                        <td class="text-left">{{ !empty($val->phone_no) ? $val->phone_no :'-' }}</td>
                                                        <td class="text-center">{{ !empty($val->amount) ? number_format($val->amount, 2) :'-' }}</td>
                                                        <td class="text-left">{{ !empty($val->created_datetime) ? date('d-m-Y',$val->created_datetime) :'-' }}</td>
                                                        <td class="text-left">{{ !empty($val->ticket_names) ? $val->ticket_names : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->registration_ids) ? $val->registration_ids : '-'}}</td>
                                                        <td class="text-center">
                                                            <?php  if(!empty($val->send_email_flag)){
                                                                echo 'Yes';
                                                                }else{
                                                                    echo 'No';
                                                                } ?>    
                                                        </td>
                                                        <td class="text-center">
                                                            <?php  if(!empty($val->change_status_manual)){
                                                                echo 'Yes';
                                                                }else{
                                                                    echo 'No';
                                                                } ?>    
                                                        </td>
                                                        <td class="text-left">{{ !empty($val->payment_status) ? $val->payment_status :'-' }}</td>
                                                        
                                                    </tr>
                                        <?php 
                                                } // End of foreach
                                            } else { // When $search_transaction_id is empty
                                        ?>
                                                <tr>
                                                    <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                                </tr>
                                        <?php
                                            } // End of if $search_transaction_id
                                        } else { // When $ad_log_array is empty
                                        ?>
                                            <tr>
                                                <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    
                                </table>
                            
                                <div class="card-body">
                                    <div class="d-flex justify-content-end">
                                        {{-- {{ $Paginator->links() }} --}}
                                    </div>
                                </div>
                            </div>

                        {{-- Temp booking tickets details --}}
                            <div class="row px-2">
                                <div class="col-sm-8 float-right">
                                    <h2 class="content-header-title float-left mb-0">Temp Booking Ticket Details</h2>
                                </div>
                                <div class="col-sm-4 d-flex justify-content-end float-right">
                                    <div class="d-flex justify-content-end">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sr. No</th>
                                            <th class="text-center" >Booking Payment Id</th>
                                            <th class="text-center" >Event Id</th>
                                            <th class="text-center">Total Attendees</th>
                                            <th class="text-center">Total Price</th>
                                            <th class="text-center">Total Discount</th>
                                            <th class="text-center">Utm Campaign</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($tem_booking_ticket_array)) {
                                            $i =0;
                                            if (!empty($search_transaction_id) || !empty($search_email_address_audit)) {
                                                foreach ($tem_booking_ticket_array as $val) {
                                                    $i++;
                                        ?>
                                                    <tr>
                                                        <td class="text-center">{{ $i }}</td>
                                                        <td class="text-center">{{ !empty($val->booking_pay_id) ? $val->booking_pay_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->event_id) ? $val->event_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->total_attendees) ? $val->total_attendees : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->TotalPrice) ? $val->TotalPrice : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->TotalDiscount) ? $val->TotalDiscount : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->UtmCampaign) ? $val->UtmCampaign : '-' }}</td>    
                                                    </tr>
                                        <?php 
                                                } // End of foreach
                                            } else { // When $search_transaction_id is empty
                                        ?>
                                                <tr>
                                                    <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                                </tr>
                                        <?php
                                            } // End of if $search_transaction_id
                                        } else { // When $ad_log_array is empty
                                        ?>
                                            <tr>
                                                <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    
                                </table>
                            
                                <div class="card-body">
                                    <div class="d-flex justify-content-end">
                                        {{-- {{ $Paginator->links() }} --}}
                                    </div>
                                </div>
                            </div>

                        {{-- Event booking details --}}
                            <div class="row px-2">
                                <div class="col-sm-8 float-right">
                                    <h2 class="content-header-title float-left mb-0">Event Booking Details</h2>
                                </div>
                                <div class="col-sm-4 d-flex justify-content-end float-right">
                                    <div class="d-flex justify-content-end">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sr. No</th>
                                            <th class="text-center" >Id</th>
                                            <th class="text-center" >Booking Payment Id</th>
                                            <th class="text-center" >Event Id</th>
                                            <th class="text-center" >User Id</th>
                                            <th class="text-center">Total Amount</th>
                                            <th class="text-center">Total Discount</th>
                                            <th class="text-center">Cart Details</th>
                                            <th class="text-left">Transaction Status</th>
                                            <th class="text-center">Utm Campaign</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($event_booking_array)) {
                                            $i =0;
                                            if (!empty($search_transaction_id) || !empty($search_email_address_audit)) {
                                                foreach ($event_booking_array as $val) {
                                                    $cartDetails = json_decode($val->cart_details, true);
                                                    $i++;
                                        ?>
                                                    <tr>
                                                        <td class="text-center">{{ $i }}</td>
                                                        <td class="text-center">{{ !empty($val->id) ? $val->id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->booking_pay_id) ? $val->booking_pay_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->event_id) ? $val->event_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->user_id) ? $val->user_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->total_amount) ? (number_format($val->total_amount, 2)) : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->total_discount) ? $val->total_discount : '-' }}</td>
                                                        <td class="text-center">
                                                            <?php  if(!empty($cartDetails)){
                                                                echo 'Yes';
                                                                }else{
                                                                    echo 'No';
                                                                } ?>    
                                                        </td>
                                                        <td class="text-left">
                                                            <?php
                                                                {{ 
                                                                    if($val->transaction_status ==0) {
                                                                        echo " 0 (Initiate)";
                                                                    }elseif ($val->transaction_status == 1) {
                                                                        echo " 1  (Success)";
                                                                    }elseif ($val->transaction_status == 2) {
                                                                        echo " 2  (Fail)";
                                                                    }elseif ($val->transaction_status == 3) {
                                                                        echo "3  (Free)";
                                                                    }
                    
                                                                }} 
                                                            ?>
                                                        </td>    
                                                        <td class="text-left">{{ !empty($val->utm_campaign) ? $val->utm_campaign : '-' }}</td>
                                                        
                                                    </tr>
                                        <?php 
                                                } // End of foreach
                                            } else { // When $search_transaction_id is empty
                                        ?>
                                                <tr>
                                                    <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                                </tr>
                                        <?php
                                            } // End of if $search_transaction_id
                                        } else { // When $ad_log_array is empty
                                        ?>
                                            <tr>
                                                <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    
                                </table>
                            
                                <div class="card-body">
                                    <div class="d-flex justify-content-end">
                                        {{-- {{ $Paginator->links() }} --}}
                                    </div>
                                </div>
                            </div>

                        {{-- Booking details --}}
                            <div class="row px-2">
                                <div class="col-sm-8 float-right">
                                    <h2 class="content-header-title float-left mb-0">Booking Details</h2>
                                </div>
                                <div class="col-sm-4 d-flex justify-content-end float-right">
                                    <div class="d-flex justify-content-end">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sr. No</th>
                                            <th class="text-center" >Id</th>
                                            <th class="text-center" >Event Booking Id</th>
                                            <th class="text-center" >Event Id</th>
                                            <th class="text-center" >User Id</th>
                                            <th class="text-center" >Ticket Id</th>
                                            <th class="text-center" >Quantity</th>
                                            <th class="text-center">Ticket Amount</th>
                                            <th class="text-left" >Booking Date Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($booking_details_array)) {
                                            $i =0;
                                            if (!empty($search_transaction_id) || !empty($search_email_address_audit)) {
                                                foreach ($booking_details_array as $val) {
                                                    $i++;
                                        ?>
                                                    <tr>
                                                        <td class="text-center">{{ $i }}</td>
                                                        <td class="text-center">{{ !empty($val->id) ? $val->id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->booking_id) ? $val->booking_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->event_id) ? $val->event_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->user_id) ? $val->user_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->ticket_id) ? $val->user_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->quantity) ? $val->quantity : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->ticket_amount) ? number_format($val->ticket_amount, 2) : '-' }}</td>
                                                        <td class="text-left">{{ !empty($val->booking_date) ? $val->booking_date.' ('. date('d-m-Y h:i',$val->booking_date) .')' : '-' }}</td>
                                                    </tr>
                                        <?php 
                                                } // End of foreach
                                            } else { // When $search_transaction_id is empty
                                        ?>
                                                <tr>
                                                    <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                                </tr>
                                        <?php
                                            } // End of if $search_transaction_id
                                        } else { // When $ad_log_array is empty
                                        ?>
                                            <tr>
                                                <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    
                                </table>
                            
                                <div class="card-body">
                                    <div class="d-flex justify-content-end">
                                        {{-- {{ $Paginator->links() }} --}}
                                    </div>
                                </div>
                            </div>

                        {{--Attendance Booking details --}}
                            <div class="row px-2">
                                <div class="col-sm-8 float-right">
                                    <h2 class="content-header-title float-left mb-0">Attendance Booking Details</h2>
                                </div>
                                <div class="col-sm-4 d-flex justify-content-end float-right">
                                    <div class="d-flex justify-content-end">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sr. No</th>
                                            <th class="text-center" >Id</th>
                                            <th class="text-center" >Booking Details Id</th>
                                            <th class="text-center" >Ticket Id</th>
                                            <th class="text-left" >First Name</th>
                                            <th class="text-left" >Last Name</th>
                                            <th class="text-left" >Email</th>
                                            <th class="text-center">Mobile</th>
                                            <th class="text-left">Attendee Details</th>
                                            <th class="text-left">Registration Id</th>
                                            <th class="text-center">Date Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($attendee_booking_details_array)) {
                                            $i =0;
                                            if (!empty($search_transaction_id) || !empty($search_email_address_audit)) {
                                                foreach ($attendee_booking_details_array as $val) {
                                                    $attendeeDetails = json_decode($val->attendee_details, true);
                                                    $i++;
                                        ?>
                                                    <tr>
                                                        <td class="text-center">{{ $i }}</td>
                                                        <td class="text-center">{{ !empty($val->id) ? $val->id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->booking_details_id) ? $val->booking_details_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->ticket_id) ? $val->ticket_id : '-' }}</td>
                                                        <td class="text-left">{{ !empty($val->firstname) ? $val->firstname : '-' }}</td>
                                                        <td class="text-left">{{ !empty($val->lastname) ? $val->lastname : '-' }}</td>
                                                        <td class="text-left">{{ !empty($val->email) ? $val->email : '-' }}</td>
                                                        <td class="text-left">{{ !empty($val->mobile) ? $val->mobile : '-' }}</td>
                                                        <td class="text-left">
                                                            <?php  if(!empty($attendeeDetails)){
                                                            echo 'Yes';
                                                            }else{
                                                                echo 'No';
                                                            } ?>
                                                        </td>
                                                        <td class="text-left">{{ !empty($val->registration_id) ? $val->registration_id : '-' }}</td>
                                                        <td class="text-left">{{ $val->created_at ? date('d-m-Y h:i',$val->created_at) : '-' }}</td>
                                                    </tr>
                                        <?php 
                                                } // End of foreach
                                            } else { // When $search_transaction_id is empty
                                        ?>
                                                <tr>
                                                    <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                                </tr>
                                        <?php
                                            } // End of if $search_transaction_id
                                        } else { // When $ad_log_array is empty
                                        ?>
                                            <tr>
                                                <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    
                                </table>
                            
                                <div class="card-body">
                                    <div class="d-flex justify-content-end">
                                        {{-- {{ $Paginator->links() }} --}}
                                    </div>
                                </div>
                            </div>

                        {{-- Email Log --}}

                            <div class="row px-2">
                                <div class="col-sm-8 float-right">
                                    <h2 class="content-header-title float-left mb-0">Email Log Details</h2>
                                </div>
                                <div class="col-sm-4 d-flex justify-content-end float-right">
                                    <div class="d-flex justify-content-end">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sr. No</th>
                                            <th class="text-center" >Send Mail To</th>
                                            <th class="text-left" >Subject</th>
                                            <th class="text-left" >message</th>
                                            <th class="text-center" >Type</th>
                                            <th class="text-left" >Date Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($email_log)) {
                                            $i =0;
                                            if (!empty($search_transaction_id) || !empty($search_email_address_audit)) {
                                                foreach ($email_log as $val) {
                                                    $i++;
                                        ?>
                                                    <tr>
                                                        <td class="text-center">{{ $i }}</td>
                                                        <td class="text-center">{{ !empty($val->send_mail_to) ? $val->send_mail_to : '-'  }}</td>
                                                        <td class="text-left">{{ !empty($val->subject) ? $val->subject :'-' }}</td>
                                                        <td class="text-left">
                                                            <button type="button" class="btn btn-primary open-popup-btn" data-message="{{ !empty($val->message) ? $val->message : '-' }}">
                                                            Message
                                                            </button>
                                                        </td>
                                                        <td class="text-center">{{ !empty($val->type) ? $val->type : '-' }}</td>
                                                        <td class="text-left">{{ !empty($val->datetime) ? $val->datetime.'('.date('d-m-Y h:i',$val->datetime).')' : '-' }}</td>
                                                    </tr>
                                        <?php 
                                                } // End of foreach
                                            } else { // When $search_transaction_id is empty
                                        ?>
                                                <tr>
                                                    <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                                </tr>
                                        <?php
                                            } // End of if $search_transaction_id
                                        } else { // When $ad_log_array is empty
                                        ?>
                                            <tr>
                                                <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    
                                </table>
                            
                                <div class="card-body">
                                    <div class="d-flex justify-content-end">
                                        {{-- {{ $Paginator->links() }} --}}
                                    </div>
                                </div>
                                <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="messageModalLabel">Message Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                        <!-- Message content will be inserted here -->
                                        </div>
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>

                            </div>
                        {{-- Applied Coupon --}}
                            <div class="row px-2">
                                <div class="col-sm-8 float-right">
                                    <h2 class="content-header-title float-left mb-0">Applied Coupon Details</h2>
                                </div>
                                <div class="col-sm-4 d-flex justify-content-end float-right">
                                    <div class="d-flex justify-content-end">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sr. No</th>
                                            <th class="text-center" >Id</th>
                                            <th class="text-center" >Event Id</th>
                                            <th class="text-left" >Event Name</th>
                                            <th class="text-center" >Coupon Id</th>
                                            <th class="text-left" >Coupon Name</th>
                                            <th class="text-center" >Ticket Id</th>
                                            <th class="text-center" >Amount</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Booking Id</th>
                                            <th class="text-center">Booking Details Id</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($applied_coupon_array)) {
                                            $i =0;
                                            if (!empty($search_transaction_id) || !empty($search_email_address_audit)) {
                                                foreach ($applied_coupon_array as $val) {
                                                    $i++;
                                        ?>
                                                    <tr>
                                                        <td class="text-center">{{ $i }}</td>
                                                        <td class="text-center">{{ !empty($val->id) ? $val->id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->event_id) ? $val->event_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->event_name) ? $val->event_name : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->coupon_id) ? $val->coupon_id : '-' }}</td>
                                                        <td class="text-left">{{ !empty($val->DiscountCode) ? $val->DiscountCode : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->ticket_ids) ? $val->ticket_ids : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->amount) ? number_format($val->amount,2) : '-' }}</td>
                                                        <td class="text-left">{{ !empty($val->created_at) ? date('d-m-Y h:i',$val->created_at) : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->booking_id) ? $val->booking_id : '-' }}</td>
                                                        <td class="text-center">{{ !empty($val->booking_detail_id) ? $val->booking_detail_id : '-' }}</td>
                                                    </tr>
                                        <?php 
                                                } // End of foreach
                                            } else { // When $search_transaction_id is empty
                                        ?>
                                                <tr>
                                                    <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                                </tr>
                                        <?php
                                            } // End of if $search_transaction_id
                                        } else { // When $ad_log_array is empty
                                        ?>
                                            <tr>
                                                <td colspan="14" class="text-center" style="color: red">No Record Found</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    
                                </table>
                            
                                <div class="card-body">
                                    <div class="d-flex justify-content-end">
                                        {{-- {{ $Paginator->links() }} --}}
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search_email_address').on('input', function() {
            $('#search_transaction_id').prop('readonly', true);
        });

        $('#search_transaction_id').on('input', function() {
            $('#search_email_address').prop('readonly', true);
        });
    });


  $(document).ready(function() {
    $('.open-popup-btn').on('click', function() {
        // Get the message content from the data attribute
        var messageContent = $(this).data('message');
        
        // Insert the message content into the modal body
        $('#messageModal .modal-body').html(messageContent);
        
        // Show the modal
        $('#messageModal').modal('show');
    });
});


</script>




