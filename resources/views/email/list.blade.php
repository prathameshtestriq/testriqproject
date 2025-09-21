@extends('layout.index')
@section('title', 'Email ')

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
                                        <h2 class="content-header-title float-left mb-0">Email</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Email</li>
                                        <li class="breadcrumb-item active" aria-current="page">Email List</li>
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
            <!-- Bordered table start -->
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card "><br>
                        <form class="dt_adv_search" action="{{ url('email_sending') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_email_send">
                            <div class="card-header w-100 m-0"> 
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-3 col-12">
                                               <?php 
                                                  $Email_Type = array(
                                                      1 => 'Manual Emails',
                                                      2 => 'Upload CSV'
                                                  ); 
                                               ?>
                                               <label for="email_type">Email Type</label>
                                               <select id="email_type" name="search_email_type" class="form-control select2">
                                                   <!-- Always selected placeholder -->
                                                   <option value="" disabled selected>Select Email Type</option>
                                           
                                                   <?php 
                                                       foreach ($Email_Type as $key => $value) {
                                                           echo "<option value=\"$key\">$value</option>";
                                                       }
                                                    ?>
                                                </select>
                                            </div>

                                              <!-- Initialize select2 with placeholder only -->
                                              <script>
                                              $(document).ready(function() {
                                                  $('#email_type').select2({
                                                      placeholder: "Select Email Type",
                                                      width: '100%'
                                                  });
                                              });
                                              </script>


                                            <div class="col-sm-3 col-12 ">
                                                <?php 
                                                    $Receivers = ['All Organizer','All Registration','All Participant'];  
                                                ?>
                                                <label for="form-control"> Receiver</label>
                                                <select id="receiver" name="search_receiver" class="form-control select2 form-control">
                                                    <option value="">Select  receiver</option>
                                                    <?php 
                                                        foreach ($Receivers as $value)
                                                        {
                                                            $selected = '';
                                                            if(old('search_receiver',$search_receiver) == $value){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3 col-12 ">
                                                <label for="form-control">Event</label>
                                                <select id="search_event" name="search_event" class="select2 form-control">
                                                    <option value="">All Event</option>
                                                    <?php  
                                                    foreach ($EventsData as $value)
                                                    {  
                                                        $selected = '';
                                                        if(old('search_event',$search_event) == $value->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo ucfirst($value->name); ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                           
                                            <div class="col-sm-3 ">
                                                <label for="form-control">Start Date</label>
                                                <input type="datetime-local" id="send_email_start_date" class="form-control"
                                                    placeholder="Start Date" name="send_email_start_date" value="{{ old('send_email_start_date', $search_send_email_start_date ? \Carbon\Carbon::parse($search_send_email_start_date)->format('Y-m-d\TH:i:s') : '') }}"
                                                    autocomplete="off" onkeydown="return false;" onchange="setEndDateMin()"/>
                                            </div>
                                        
                                            <div class="col-sm-3 mt-1">
                                                <label for="form-control">End Date</label>
                                                <input type="datetime-local" id="send_email_end_date" class="form-control"
                                                    placeholder="End Date" name="send_email_end_date" value="{{ old('send_email_end_date', $search_send_email_end_date ? \Carbon\Carbon::parse($search_send_email_end_date)->format('Y-m-d\TH:i:s') : '') }}"
                                                    autocomplete="off" />
                                            </div>
                                            
                                            
                                         
                                            <div class="col-sm-3 mt-1">
                                                <label for="form-control">&nbsp;</label><br>
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_email_type) || !empty($search_receiver) || !empty($search_event) || !empty($search_send_email_start_date) || !empty($search_send_email_end_date))
                                               
                                                    <a title="Clear" href="{{ url('email_sending/clear_search') }}"
                                                        type="button" class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif 
                                            </div>
                                            <div class="col-sm-6 mt-2 p-1 flaot-end">
                                                <a href="{{ url('email_sending/add') }}" class="btn btn-outline-primary float-right pr-2">
                                                    <i data-feather="plus"></i><span>Add </span></a>
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
                                        <th class="text-left">Email Type</th> 
                                        <th class="text-left">Recipient Type</th>
                                        <th class="text-left">Subject</th>    
                                        <th class="text-left">Event Name</th>              
                                        <th class="text-center">Recipient Count</th>
                                        <th class="text-left">Sent Email Date/Time</th>
                                        <!-- <th class="text-left">status</th> -->
                                    </tr>
                                </thead>
                                <tbody class="text-center">

                                    <?php 
                                    if (!empty($Email_details)){
                                        $i = 0;
                                        ?>
                                    <?php foreach ($Email_details as $val){
                                       
                                            $i++;?>
                                    <tr>
                                        <td class="text-center">{{ $i }}</td>
                                        <td class="text-left">
                                            @php
                                                if (!empty($val->email_type)) {
                                                    if ($val->email_type == '1') {
                                                        echo 'Select Filter';
                                                    } elseif ($val->email_type == '2') {
                                                        echo 'Manual Emails';
                                                    } elseif ($val->email_type == '3') {
                                                        echo 'Upload CSV';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                            @endphp
                                        </td>
                                        <td class="text-left">{{ !empty($val->recipient_type)?$val->recipient_type : '-'}}</td>
                                        <td class="text-left">{{ !empty($val->subject)?ucfirst($val->subject) :'-' }}</td>
                                        <td class="text-left">{{ !empty($val->event_names) ?ucfirst($val->event_names) :'-' }}</td>
                                       
                                        <td class="text-center">{{ !empty($val->recipient_count)?$val->recipient_count:'-'  }}</td>
                                        {{-- <td class="text-left">{{ !empty($val->email)?$val->email:'-' }}</td> --}}
                                        <td class="text-left">{{ date('d-m-Y H:i:s',$val->sent_date_time) }}</td>
                                        <!-- <td class="text-center">
                                            <div class="custom-control custom-switch custom-switch-success">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="{{ $val->id }}" {{ $val->status ? 'checked' : '' }}
                                                    onclick="change_status(event.target, {{ $val->id }});" />
                                                <label class="custom-control-label" for="{{ $val->id }}">
                                                    <span class="switch-icon-left"></span>
                                                    <span class="switch-icon-right"></span>
                                                </label>
                                            </div>
                                        </td> -->
                                    </tr>
                                    <?php }
                                    }else{?>
                                    <tr>
                                        <td colspan="8" style="text-align:center; color:red;">No Record Found</td>
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
                    </div>

                </div>
            </div>
            <!-- Bordered table end -->
        </div>


    </section>
<script>
    function change_status(_this, id) {
        //  alert(id)
        // ;
        var status = $(_this).prop('checked') == true ? 1 : 0;
        // alert(status);

        if (confirm("Are you sure want to change this status?")) {
            let _token = $('meta[name="csrf-token"]').attr('content');
            //alert(_token);
            $.ajax({
                url: "<?php echo url('email_sending/change_status'); ?>",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function(result) {
                    if (result.sucess == 'true') {
                        // console.log(result);
                        // alert(result.message); 
                        $("#success-message").text(result.message); // Update success message
                        $("#success-alert").show(); // Show the success alert
                        // Optionally hide the alert after a few seconds
                        setTimeout(function() {
                            $("#success-alert").fadeOut();
                        }, 2000); // Adjust time (2000 = 2 seconds)

                    }else{
                        alert('Some error occured');
                        if(status)
                            $(_this).prop("checked" , false)
                        else
                            $(_this).prop("checked" , true)
                            return false;
                    }
                },
                error: function() {
                    alert('Some error occured');
                    if (status)
                        $(_this).prop("checked", false)
                    else
                        $(_this).prop("checked", true)
                    return false;
                }
            });
        } else {
            if (status)
                $(_this).prop("checked", false)
            else
                $(_this).prop("checked", true)
            return false;
        }
    }
</script>    
<script>
    function setEndDateMin() {
        const startDateInput = document.getElementById('send_email_start_date');
        const endDateInput = document.getElementById('send_email_end_date');
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
