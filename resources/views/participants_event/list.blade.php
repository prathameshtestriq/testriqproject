@extends('layout.index')
@section('title', 'Participants  ')

<!-- Dashboard Ecommerce start -->
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
                                    <h2 class="content-header-title float-left mb-0">Participants List</h2>
                                </div>
                                
                              <?php if($event_id != 0){ ?>
                                <h5 class="content-header-title float-left mb-0 ml-2"><b>Event Name:-</b> {{$event_name[0]->name}}</h5>
                              <?php } ?>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end breadcrumb-wrapper">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mr-1">
                                    <li class="breadcrumb-item">Home</li>
                                    <li class="breadcrumb-item">Participant</li>
                                    <li class="breadcrumb-item active" aria-current="page">Participants List</li>
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
                        {{ $message }}
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
                        {{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif

        <div class="content-body">
            <!-- Bordered table start -->
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card ">
                        <form class="dt_adv_search"  method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_participant_event">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-2 col-12">
                                                <label for="form-control">Participant name</label>
                                                <input type="text" id="search_participant_name" class="form-control"
                                                    placeholder="Participant name" name="participant_name" value="{{ $search_participant_name }}"
                                                    autocomplete="off" />
                                            </div>


                                            <div class="col-sm-2 col-12">
                                                    <?php 
                                                       $Transaction_Status = array(0=>'Inprocess',1=>'Success', 2=>'Fail', 3=>'Free' );    
                                                       // $Transaction_Status = array('initiate'=>'Inprocess','success'=>'Success', 'failure'=>'Fail', 'ree'=>'Free' );    
                                                    ?>
                                                    <label for="form-control">Payment Status</label>
                                                    <select id="transaction_status" name="transaction_status" class="form-control select2 form-control">
                                                        <option value="">Select Payment Status</option>
                                                        <?php 
                                                            foreach ($Transaction_Status as $key => $value)
                                                            {
                                                                $selected = '';
                                                                if(old('transaction_status', $search_transaction_status) == $key){
                                                                    $selected = 'selected';
                                                                }
                                                                ?>
                                                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                                <?php 
                                                            }
                                                        ?>
                                                    </select>
                                            </div>

                                            <div class="col-sm-2 col-12">
                                                <label for="form-control">Registration Id</label>
                                                <input type="text" id="registration_id" class="form-control"
                                                    placeholder="Registration Id" name="registration_id" value="{{ $search_registration_id }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-2 col-12 ">
                                                <label for="form-control">Email/Mobile Id</label>
                                                <input type="text" id="email_id" class="form-control"
                                                    placeholder="Email/Mobile Id" name="email_id" value="{{ $search_email_id }}"
                                                    autocomplete="off" />
                                            </div>
                                            {{-- <div class="col-sm-2 col-12 ">
                                                <label for="form-control">Category</label>
                                                <input type="text" id="category" class="form-control"
                                                    placeholder="Category" name="category" value="{{ $search_category }}"
                                                    autocomplete="off" />
                                            </div> --}}

                                            <div class="col-sm-2 col-12">
                                                <label for="form-control"> Category</label>
                                                <select id="category" name="category" class="form-control select2 form-control">
                                                    <option value="">Select  category</option>
                                                    <?php 
                                                        foreach ($Categories as $value)
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
                                            </div>


                                            <div class="col-sm-2 col-12 ">
                                                <label for="form-control">Start Booking Date</label>
                                                <input type="date" id="start_booking_date" class="form-control"
                                                    placeholder="Start Date" name="start_booking_date" value="{{ old('start_booking_date', $search_start_booking_date ? \Carbon\Carbon::parse($search_start_booking_date)->format('Y-m-d') : '') }}"
                                                    autocomplete="off" onkeydown="return false;" onchange="setEndDateMin()"/>
                                            </div>
                                          
                                            <div class="col-sm-2 col-12 mt-2">
                                                <label for="form-control">End Booking Date</label>
                                                <input type="date" id="end_booking_date" class="form-control"
                                                    placeholder="End Date" name="end_booking_date" value="{{ old('end_booking_date', $search_end_booking_date ? \Carbon\Carbon::parse($search_end_booking_date)->format('Y-m-d') : '') }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-2 col-12 mt-2">
                                                <label for="form-control">Transaction/Order Id</label>
                                                <input type="text" id="transaction_order_id" class="form-control"
                                                    placeholder="Transaction/Order Id" name="transaction_order_id" value="{{ $search_transaction_order_id }}"
                                                    autocomplete="off" />
                                            </div>
                                            
                                            <?php if(empty($event_id)){ ?>
                                                <div class="col-sm-2 col-12 mt-2">
                                                    <label for="form-control"> Event</label>
                                                    <select id="event_name" name="event_name" class="form-control select2 form-control">
                                                        <option value="">Select  Event</option>
                                                        <?php 
                                                            foreach ($EventsData as $value)
                                                            {
                                                                $selected = '';
                                                                if(old('search_event', $search_event) == $value->id){
                                                                    $selected = 'selected';
                                                                }
                                                                ?>
                                                                <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo ucfirst($value->name); ?></option>
                                                                <?php 
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            <?php } ?>
                                            
                                            <?php if(empty($event_id)){ ?>
                                            <div class="col-sm-6 mt-3">
                                            <?php }else{ ?>
                                            <div class="col-sm-8 mt-3">
                                            <?php } ?>
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_participant_name) || !empty($search_registration_id) || !empty($search_mobile_no) 
                                                || !empty($search_email_id) || !empty($search_category) || !empty($search_start_booking_date) 
                                                || !empty($search_end_booking_date) ||  $search_transaction_status !== '' || !empty($search_transaction_order_id) || !empty($search_event))
                                                {{-- {{ url('/participants_event/'.$event_participants[0]->event_id.'/clear_search') }} --}}
                                                <?php  if($event_id > 0 && $dashboard_id == 0){ ?> 
                                                    <a title="Clear" href="{{ url('/participants_event/'.$event_id.'/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                <?php }else{?>
                                                    <a title="Clear" href="{{ url('/participants_event/'.$event_id.'/'.$dashboard_id.'/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                <?php } ?>
                                                @endif

                                                <div class="float-right">

                                                    <?php if((!empty($search_event) || !empty($event_id)) && $dashboard_id == 0){ ?>
                                                        <a href="{{ url('participants_event/'.$event_id.'/0/export_revenue') }}" class="btn btn-danger text-white " title = "Revenue">Revenue </a>
                                                    <?php }else{ ?>
                                                        <a href="{{ url('participants_event/'.$event_id.'/'.$dashboard_id.'/export_revenue') }}" class="btn btn-danger text-white " title = "Revenue">Revenue </a>
                                                    <?php } ?>    
                                                  <!--   @if (!empty($event_participants))
                                                      <a href="{{ url('participants_event/'.$event_id.'/export_download') }}" class="btn btn-danger text-white " title = "Download">Download </a>
                                                    @endif -->
                                                     @if (!empty($event_participants))
                                                        <?php if((!empty($search_event) || !empty($event_id)) && $dashboard_id == 0) { ?>
                                                            <a href="{{ $ParticipantsExcelLink }}" class="btn btn-danger text-white " title = "Download" download>Download </a>
                                                        <?php }else{ ?>
                                                            <a href="{{ $ParticipantsExcelLink }}" class="btn btn-danger text-white " title = "Download" download>Download </a>
                                                        <?php } ?>    
                                                    @endif
                                                    <?php  if($event_id > 0 && $dashboard_id == 0){ ?>
                                                        <a href="{{ url('/event') }}"  class="btn btn-primary ">
                                                            <span>Back</span></a>
                                                    <?php  }else{ ?>    
                                                        <a href="{{ url('/dashboard') }}"  class="btn btn-primary ">
                                                            <span>Back</span></a>
                                                    <?php } ?>
                                                </div>    
                                                  
                                            </div>
                                         
                                        </div>
                                    </div>  
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-left">Participant Name</th>  
                                        <th class="text-center">Booking Date</th>
                                        <th class="text-center">Transaction/Order Id</th>
                                        <th class="text-center">Registration Id</th>
                                        <th class="text-center">Payu Id</th>
                                        <th class="text-center">Payment Status</th>
                                        <th class="text-center">Email/Mobile Number</th>
                                        {{-- <th class="text-center">Mobile Number</th> --}}
                                        <th class="text-center">Category Name</th>
                                        <th class="text-center">Total Ticket Amount</th>
                                        <th class="text-center">Final Amount</th>
                                        <th class="text-center">View</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                <br/><br/><br/>
                                    <?php 
                                    if (!empty($event_participants)){
                                        $i = $Offset;
                                        // $i = 0;
                                        ?>
                                        <?php 
                                       
                                        foreach ($event_participants as $val){
                                        
                                                $i++;?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-left">{{ ucfirst($val->user_name) }}</td>
                                                <td class="text-left" style="min-width: 100px">{{ date('d-m-Y ', $val->booking_date) }}</td>
                                                <td class="text-left">{{ $val->Transaction_order_id }}</td>
                                                <td class="text-left">{{ $val->registration_id }}</td>
                                                <td class="text-left">{{ $val->payu_id }}</td>
                                                <td class="text-left">
                                                    <!-- Form for each row -->
                                                    <form class="dt_adv_search" method="POST" id="transactionForm_{{ $val->event_booking_id }}">
                                                        @csrf
                                                        <input type="hidden" name="form_type" value="transaction_status_add">
                                                        <input type="hidden" name="event_booking_id[{{ $val->event_booking_id }}]" value="{{ $val->event_booking_id }}">
                                                        <input type="hidden" name="booking_payment_details_id[{{ $val->event_booking_id }}]" value="{{ $val->booking_payment_details_id }}">
        
                                                        <?php 
                                                        $Transaction_Status = array(0=>'Inprocess', 1=>'Success', 2=>'Failure', 3=>'Free');    
                                                        ?>
                                                        <select id="list_transaction_status_{{ $val->event_booking_id }}" name="list_transaction_status[{{ $val->event_booking_id }}]" class="form-control select2 form-control" onchange="this.form.submit()">
                                                            <option value="">Select Payment Status</option>
                                                            <?php 
                                                            foreach ($Transaction_Status as $key => $value) {
                                                                $selected = '';
                                                                if (old('list_transaction_status.' . $val->event_booking_id, $val->transaction_status) == $key) {
                                                                    $selected = 'selected';
                                                                }
                                                                ?>
                                                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                                <?php 
                                                            }
                                                            ?>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td class="text-left">{!! $val->email . '<br>' . $val->mobile !!}</td>
                                                {{-- <td class="text-left">{{ $val->mobile }}</td> --}}
                                                <td class="text-center">
                                                 {{ ucfirst($val->category_name) }}
                                                </td>

                                                <td class="text-center">{{ $val->total_amount }}</td>
                                                <td class="text-center">{{ number_format($val->amount,2) }}</td>
                                               
                                                <td>
                                                    <div class="d-flex" style="gap: 5px;">
                                                           <a data-toggle="modal" id="smallButton" data-target="#smallModal" href="javascript:void(0);" onClick="showDetails({{ $val->id }},{{$val->event_id}})" title="show" data-bs-toggle="modal" data-bs-target="#exampleModallaptop1">
                                                        <i class="fa fa-eye btn btn-success btn-sm "></i>
                                                    </a>

                                                     <a data-toggle="modal" id="smallButton" data-target="#smallModal" href="javascript:void(0);" onClick="showCategoryDetails({{ $val->id }},{{$val->event_id}})" title="Change Races Category" data-bs-toggle="modal" data-bs-target="#exampleModallaptop2">
                                                        <i class="fa fa-ticket btn btn-warning btn-sm "></i>
                                                    </a>
                                                    </div>
                                                 
                                                </td>                                               
                                                <td>
                                                    {{-- <a href=""><i
                                                            class="fa fa-edit btn btn-primary btn-sm" title="edit"></i></a> --}}
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm" onclick="remove_type({{ $val->id }},{{$val->event_id}})" title="Delete"></i>
                                                </td>
                                            </tr>
                                      <?php }
                                    }else{?>
                                        <tr>
                                            <td colspan="17" style="text-align:center; color:red;">No Record Found</td>
                                        </tr>
                                  <?php }?>
                                </tbody>
                            </table>
                            <div class="card-body">
                                <div class="d-flex justify-content-end">
                                    {{ $Paginator->links() }}
                                </div>
                            </div>
                        </div>
                       
                        <div class="modal fade" id="participant_details_modal" tabindex="-1" role="dialog" aria-labelledby="participant_details_modal" aria-hidden="true" >
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                              <div class="modal-content">
                                <div class="col-xl-12">
                                  <div class="card social-profile mb-0">
                      
                                    <div class="card-body" >
                                        <form class="form" id="model" action="" method="post">
                                            @csrf
                                            <div class="card-body" id="participant_details_body">
                                            
                                            </div> 
                                        </form>
                                    </div> 
                                    <br>
                                  </div>
                                </div>  
                              </div>
                            </div>
                        </div>

                        <!-- Changes Races Category -->
                         <div class="modal fade" id="change_category_modal" tabindex="-1" role="dialog" aria-labelledby="change_category_modal" aria-hidden="true" >
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                              <div class="modal-content">
                                <div class="col-xl-12">
                                  <div class="card social-profile mb-0">
                      
                                    <div class="card-body" >
                                        <form class="form" id="model" action="" method="post">
                                            @csrf
                                            <div class="card-body" id="change_category_body">
                                            
                                            </div> 
                                        </form>
                                    </div> 
                                    <br>
                                  </div>
                                </div>  
                              </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <!-- Bordered table end -->
        </div>


    </section>
    
    <script>
            
            function remove_type(iId,event_id) {
                // alert(iId);
                var url = '<?php echo url('/participants_event') ?>';
                url = url + '/'+ event_id + '/delete/' + iId;
               
                Confirmation = confirm('Are you sure you want to remove this record ?');
                if (Confirmation) {

                    window.location.href = url;

                }
            }


            function showDetails(attendance_booking_id,event_id) {
               
                var Url= '<?php echo url('participants_event') ?>';
                url = Url + '/'+ event_id + '/view/' + attendance_booking_id;
                // alert(url);
                // var url;
                $.ajax({
                    url:  url ,
                    //  alert(url);
                    beforeSend: function() {
                        $('#loader').show();
                    },
                    // return the result
                    success: function(result) {
                        // console.log(result);

                        $('#participant_details_body').html(result);
                        $('#participant_details_modal').modal("show");
                    }
                    , complete: function() {
                        $('#loader').hide();
                    }
                    , error: function(jqXHR, testStatus, error) {
                        console.log(error);
                        alert("Page " + url + " cannot open. Error:" + error);
                        $('#loader').hide();
                    }
                    , timeout: 8000
                })
            }

            function showCategoryDetails(attendance_booking_id,event_id) {
               
                var Url= '<?php echo url('participants_event') ?>';
                url = Url + '/'+ event_id + '/change_category/' + attendance_booking_id;
                // alert(url);
                // var url;
                $.ajax({
                    url:  url ,
                    //  alert(url);
                    beforeSend: function() {
                        $('#loader').show();
                    },
                    // return the result
                    success: function(result) {
                        // console.log(result);

                        $('#change_category_body').html(result);
                        $('#change_category_modal').modal("show");
                    }
                    , complete: function() {
                        $('#loader').hide();
                    }
                    , error: function(jqXHR, testStatus, error) {
                        console.log(error);
                        alert("Page " + url + " cannot open. Error:" + error);
                        $('#loader').hide();
                    }
                    , timeout: 8000
                })
            }

    </script>
     <script>
        function setEndDateMin() {
            const startDateInput = document.getElementById('start_booking_date');
            const endDateInput = document.getElementById('end_booking_date');
            const startDate = startDateInput.value;
    
            if (startDate) {
                endDateInput.setAttribute('min', startDate);
                if (endDateInput.value && endDateInput.value < startDate) {
                    endDateInput.value = '';
                }
            }
        }
    </script>
    

@endsection
