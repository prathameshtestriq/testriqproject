@extends('layout.index')
@section('title', 'Participant Bulk Upload')
@section('content')

<style>
    .loading-overlay {
    display: none;
    background: rgba(0, 0, 0, 0.477);
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    top: 0;
    z-index: 9998;
    align-items: center;
    justify-content: center;
    display: flex;
}
</style>

<?php //dd($search_event); ?>
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
                                        <h2 class="content-header-title float-left mb-0">Participant Bulk Upload</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item active" aria-current="page">Participant Bulk Upload</li>
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
                            <span class="mr-5">{{session('success')['message']}}</span>
                            <?php if(isset(session('success')['success_count']) || isset(session('success')['fail_count'])){ ?>
                                |<span class="mx-5">Success count : <mark style="background: #28c76f91; border-radius: 3px; color: white; font-weight: 900;">{{session('success')['success_count']}}</mark></span>|
                                <span class="mx-5">Failed count : <mark style="background: #28c76f91; border-radius: 3px; color: white; font-weight: 900;">{{session('success')['fail_count']}}</mark></span>
                            <?php } ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
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
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
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
                    <div class="card ">
                        <form class="dt_adv_search" action="{{ url('participan_work_upload/export_download') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="Participant_work_upload">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="form-control">Event Name</label>
                                                <select id="search_event" name="search_event" class="form-control select2 form-control">
                                                    <option value="">Select Event</option>
                                                    <?php 
                                                        foreach ($EventsData as $value)
                                                        {
                                                            $selected = '';
                                                            if($search_event == $value->id){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo ucfirst($value->name); ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                              

                                            <div class="col-sm-3 mt-2">
                                                    <button type="submit" class="btn btn-primary">Search</button>
                                                    <?php if(!empty($search_event)) { ?>
                                                    <a title="Clear" href="{{ url('/participan_work_upload/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary"><i data-feather="rotate-ccw" class="me-25"></i> Clear Search </a>
                                                    <?php } ?>
                                            </div>

                                            <div class="col-sm-6 mt-2 text-right" >
                                                <?php if(!empty($search_event)) { ?>
                                                    <a href="{{ $ParticipantsExcelLink }}" class="btn btn-primary" title = "Download" download>Download Excel</a>
                                                <?php } ?>
                                            </div>

                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </form>
                        
                        <!-- style="display: none;" -->
                        <!-- <div id="loader" >
                            <img src="{{ asset('uploads/images/running.gif') }}" alt="Loading...">
                        </div> -->

                        <div class="loading-overlay" id="loader" style="display: none;">
                            <span>
                                <img src="{{ asset('uploads/images/running.gif') }}" alt="" style="width:100px;">
                            </span>
                        </div>


                        <?php if(!empty($search_event)) { ?>  
                            <div class="row px-1">
                                <div class="col-sm-8 float-right">
                                    <h3 class="content-header-title float-left mb-0">Sample Excel Format</h3>
                                </div>
                            </div>
                        <?php } ?>

                          <div class="table-responsive mt-2">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <?php 
                                            if (!empty($HeaderData)){
                                                foreach ($HeaderData as $res){
                                        ?>
                                           <th class="text-left">{{$res->question_form_name}}</th>
                                       <?php }} ?>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <tr>
                                        <?php 
                                            if (!empty($HeaderData)){
                                                foreach ($HeaderData as $res){
                                        ?>
                                           <td class="text-left">{{$res->answer_value}}</td>
                                       <?php }} ?>
                                    </tr>
                                   
                                </tbody>
                            </table>
                        </div>

                        {{-- Neha Working on 15-11-24  start working --}}<br/>
                            <?php if(!empty($search_event)) { ?>
                                <div class="row" id="table-bordered">
                                    <div class="col-12">
                                        <div class="card ">
                                            <div class="row px-1">
                                                <div class="col-sm-8 float-right">
                                                    <h3 class="content-header-title float-left mb-0">Ticket Details</h3>
                                                </div>
                                            </div> <br>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">Sr. No</th>
                                                                <th class="text-left">Ticket Name</th>
                                                                <th class="text-left">Ticket Status</th>
                                                                <th class="text-center">Ticket Amount</th>
                                                                <th class="text-center">Total Quantity</th>
                                                                <th class="text-center">Early Bird Discount</th>
                                                                <th class="text-left">Start Date</th>
                                                                <th class="text-left">End Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-center">
                                                            <?php 
                                                            if (!empty($Ticket_details)){
                                                                $i = 1;
                                                                foreach ($Ticket_details as $val){
                                                            ?>
                                                                <tr>
                                                                    <td class="text-center">{{ $i }}</td>
                                                                    <td class="text-left">{{ ucfirst($val->ticket_name) }}</td>
                                                                    <td class="text-center">
                                                                        <?php
                                                                        {{ 
                                                                        if ($val->ticket_status == 1) {
                                                                            echo "Paid";
                                                                        }elseif ($val->ticket_status == 2) {
                                                                            echo "Free";
                                                                        }elseif ($val->ticket_status == 3) {
                                                                            echo "Donation";
                                                                        }
                        
                                                                        }}  ?>
                                                                    </td>
                                                                    <td class="text-center">{{ $val->ticket_price }}</td>
                                                                    <td class="text-center">{{ $val->total_quantity }}</td>
                                                                    <td class="text-center">
                                                                        <?php
                                                                        {{ 
                                                                        if ($val->early_bird == 0) {
                                                                            echo "No";
                                                                        }elseif ($val->early_bird == 1) {
                                                                            echo "Yes";
                                                                        }
                        
                                                                        }}  ?>
                                                                    </td>
                                                                    <td class="text-left">{{ date('d-m-Y h:i A',$val->ticket_sale_start_date) }}</td>
                                                                    <td class="text-left">{{ date('d-m-Y h:i A',$val->ticket_sale_end_date) }}</td>
                                                                </tr>
                                                            <?php
                                                            $i++;
                                                            }  
                                                            }else{
                                                            ?>
                                                                <tr>
                                                                    <td colspan="16" style="text-align:center; color:red;">No Record Found</td>
                                                                </tr>
                                                            <?php }?>
                                                        
                                                        </tbody>
                                                    </table>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        {{-- neha end working --}}

                    </div>
                </div>
            </div>

            

            <!-- ------------------------------------------ -->
            <?php if(!empty($search_event)) { ?>
                <div class="row" id="table-bordered">
                    <div class="col-12">
                        <div class="card ">
                            <form class="dt_adv_search" action="{{ url('participan_bulk_upload/import_participant') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="form_type" value="Participant_work_upload">
                                <div class="card-header w-100 m-0">
                                    <div class="row w-100">
                                        <div class="col-sm-12">
                                            <div class="row">
                                               
                                                <div class="col-sm-3 col-12">
                                                    <label for="form-control">Bulk Upload Group Name</label>
                                                    <input type="text" id="group_name" class="form-control"
                                                    placeholder="Enter Group Name" name="group_name"  value="{{ old('group_name', $group_name) }}" autocomplete="off" />
                                                   
                                                </div>

                                                <div class="col-sm-3 col-12">
                                                    <label for="form-control">Participant Upload Excel <span style="color:red;">*</span></label>
                                                    <input type="file" class="form-control" name="participant_file" id="participant_file" accept=".xlsx" onchange="validateFileType()">
                                                </div>
                                                  
                                                <div class="col-sm-3 mt-2">
                                                    <button type="submit" class="btn btn-primary">Upload</button>
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
                                                <th class="text-left">Group Name</th>
                                                <th class="text-left">Transaction Id</th>
                                                <th class="text-left">Created Date/Time</th>
                                                <th class="text-center">Transaction Status</th>
                                                <th class="text-right">Total Amount</th>
                                                <th class="text-center">Participant Count</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <?php 
                                             // dd($ParticipantDetails);
                                              if (!empty($ParticipantDetails)){
                                                $i = 1;
                                                foreach ($ParticipantDetails as $val){
                                            ?>
                                                <tr>
                                                    <td class="text-center">{{ $i }}</td>
                                                    <td class="text-left">{{ ucfirst($val->bulk_upload_group_name) }}</td>
                                                    <td class="text-left">{{ $val->txnid }}</td>
                                                    <td class="text-left">{{ date('d-m-Y h:i A', $val->created_datetime) }}</td>
                                                    <td class="text-center">{{ ucfirst($val->payment_status) }}</td>
                                                    <td class="text-right">{{ number_format($val->amount,2) }}</td>
                                                    <td class="text-center">{{ $val->participant_count }}</td>
                                                    <td>
                                                       
                                                        <i class="fa fa-envelope btn btn-warning btn-sm"
                                                            onclick="send_email_to_all_participant({{$val->id}},{{$val->event_id}},{{$val->created_by}})"  title="Send Email To Participant"></i>
                                                        <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                            onclick="delete_record({{ $val->id }})" title="Delete"></i>
                                                    </td>
                                                </tr>
                                            <?php
                                              $i++;
                                               }  
                                             }else{
                                            ?>
                                                <tr>
                                                    <td colspan="16" style="text-align:center; color:red;">No Record Found</td>
                                                </tr>
                                            <?php }?>
                                           
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                </div>
               <?php } ?>
        </div>


    </section>

@endsection

<script src={{ asset('/app-assets/js/scripts/jquerycdn.js') }}></script>
<script>
    function delete_record(id) {
        // alert(id);
        var url = '<?php echo url('participan_bulk_upload/delete'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove this record ?');
        if (bConfirm) {
            window.location.href = url;
        } else {
            return false;
        }
    }

    function send_email_to_all_participant(id,event_id,created_by) {
        // alert(id+'--------'+event_id+'--------'+created_by);
       
        // var url = '<?php echo url('participan_bulk_upload/send_email'); ?>';
        // url = url + '/' + id + '/' + event_id + '/' + created_by;

        // window.location.href = url;
           
        var bConfirm = confirm('Are you sure you want to send email this record ?');
            if (bConfirm) {
                // Show loader
                $('#loader').show();

                // var url = '<?php echo url('participan_bulk_upload/send_email'); ?>';
                // url = url + '/' + id + '/' + event_id + '/' + created_by;
                let _token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: "<?php echo url('participan_bulk_upload/send_email') ?>",
                    type: 'post',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        event_id: event_id,
                        created_by: created_by
                    },
                    success: function(result) {
                        // console.log(result);
                        $('#loader').hide();

                        $("#success-message").text(result.message); // Update success message
                        $("#success-alert").show(); // Show the success alert
                        setTimeout(function() {
                            $("#success-alert").fadeOut();
                        }, 5000); 
                    },
                    error:function(){
                        // alert('Some error occured');
                        $('#loader').hide();
                        return false;
                    }
                });

            } else {
                return false;
            }

    }

    function validateFileType() {
        const fileInput = document.getElementById('participant_file');
        const filePath = fileInput.value;
        const allowedExtensions = /(\.xlsx)$/i;  // Regular expression to check .xlsx extension
        
        // Check if the file extension is .xlsx
        if (!allowedExtensions.exec(filePath)) {
            alert("Please upload a valid Excel file with .xlsx extension.");
            fileInput.value = ''; 
            return false;
        }
        return true;
    }
</script>