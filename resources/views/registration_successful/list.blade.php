@extends('layout.index')
@section('title', ' Registration ')

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
                                    <h2 class="content-header-title float-left mb-0">Registration List</h2>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end breadcrumb-wrapper">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mr-1">
                                    <li class="breadcrumb-item">Home</li>
                                    <li class="breadcrumb-item">Registration</li>
                                    <li class="breadcrumb-item active" aria-current="page">Registration List</li>
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
                            <input type="hidden" name="form_type" value="search_registration_successful">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="form-control">User name</label>
                                                <input type="text" id="search_registration_user_name" class="form-control"
                                                    placeholder="User name" name="registration_user_name" value="{{ $search_registration_user_name }}"
                                                    autocomplete="off" />
                                            </div>


                                            <div class="col-sm-3">
                                                    <?php 
                                                       $Transaction_Status = array(0=>'Initiate',1=>'Success', 2=>'Fail', 3=>'Free' );    
                                                    ?>
                                                    <label for="form-control">Payment Status</label>
                                                    <select id="registration_transaction_status" name="registration_transaction_status" class="form-control select2 form-control">
                                                        <option value="">Select Payment Status</option>
                                                        <?php 
                                                            foreach ($Transaction_Status as $key => $value)
                                                            {
                                                                $selected = '';
                                                                if(old('registration_transaction_status',$search_registration_transaction_status) == $key){
                                                                    $selected = 'selected';
                                                                }
                                                                ?>
                                                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                                <?php 
                                                            }
                                                        ?>
                                                    </select>
                                            </div>

                                            <div class="col-sm-3 col-12">
                                                <label for="form-control">Email Id</label>
                                                <input type="text" id="registration_email_id" class="form-control"
                                                    placeholder="Email Id" name="registration_email_id" value="{{ $search_registration_email }}"
                                                    autocomplete="off" />
                                            </div>
                                            
                                            <div class="col-sm-3 col-12">
                                                <label for="form-control">Mobile No</label>
                                                <input type="text" id="registration_mobile_no" class="form-control"
                                                    placeholder="Mobile No" name="registration_mobile_no" value="{{ $search_registration_mobile }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-3 ">
                                                <label for="form-control">Start Booking Date</label>
                                                <input type="date" id="start_registration_booking_date" class="form-control"
                                            value="{{ old('start_booking_date', $search_start_registration_booking_date ? \Carbon\Carbon::parse($search_start_registration_booking_date)->format('Y-m-d') : '') }}"
                                            placeholder="Start Date" name="start_registration_booking_date" 
                                                    autocomplete="off" onkeydown="return false;" onchange="setEndDateMin()"/>
                                            </div>
                                           
                                            <div class="col-sm-3 ">
                                                <label for="form-control">End Booking Date</label>
                                                <input type="date" id="end_registration_booking_date" class="form-control"
                                                    placeholder="End Date" name="end_registration_booking_date"  value="{{ old('end_booking_date', $search_end_registration_booking_date ? \Carbon\Carbon::parse($search_end_registration_booking_date)->format('Y-m-d') : '') }}"
                                                    autocomplete="off" />
                                            </div>

                                          

                                            <div class="col-sm-6 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_registration_user_name)|| $search_registration_transaction_status !== ''||!empty($search_registration_email)||!empty($search_registration_mobile) || !empty($search_start_registration_booking_date) || !empty($search_end_registration_booking_date)  )

                                                    <a title="Clear" href="{{ url('/registration_successful/'.$event_id.'/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                               
                                                <div class="float-right">
                                                    @if (!empty($Registration_successful))
                                                       <a href="{{ url('/registration_successful/'.$event_id.'/export_registration') }}" class="btn btn-danger text-white ">Download </a>
                                                    @endif
                                                    <?php  if($event_id > 0){ ?>
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
                                        <th class="text-left">User Name</th>  
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Mobile</th></th>
                                        <th class="text-center">Number of tickets</th></th>
                                        <th class="text-center">Total Amount</th>
                                        <th class="text-center">Booking Date</th>
                                        <th class="text-center">Payment Status</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                <br/><br/><br/>
                                    <?php 
                                    if (!empty($Registration_successful)){
                                        $i = $Offset;
                                        // $i = 0;
                                        ?>
                                        <?php 
                                       
                                        foreach ($Registration_successful as $val){
                                        
                                                $i++;?>
                                            <tr>
                                                <td class="text-center">{{$i}}</td>
                                                <td class="text-left">{{ ucfirst($val->firstname).' '.ucfirst($val->lastname) }}</td>
                                                <td class="text-left">{{ $val->email }}</td>
                                                <td class="text-left">{{ $val->mobile }}</td>
                                                <td class="text-left">{{ $val->TotalTickets }}</td>
                                                <td class="text-left">{{ $val->TotalAmount }}</td>
                                                <td class="text-left">{{ date('d-m-Y H:i:s',$val->booking_date) }}</td>
                                                <td class="text-left">
                                                    <?php
                                                 {{ 
                                                    if($val->transaction_status ==0) {
                                                        echo "Initiate";
                                                    }elseif ($val->transaction_status == 1) {
                                                        echo "Success";
                                                    }elseif ($val->transaction_status == 2) {
                                                        echo "Fail";
                                                    }elseif ($val->transaction_status == 3) {
                                                        echo "Free";
                                                    }
    
                                                 }}  ?>
                                                </td>
                                                
                                             
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
{{-- 
    <script>
            function remove_type(iId,event_id) {
                // alert(iId);
                var url = '<?php echo url('/participants_event') ?>';
                url = url + '/'+ event_id + '/delete/' + iId;
               
                Confirmation = confirm('Are you sure you want to remove this type');
                if (Confirmation) {

                    window.location.href = url;

                }
            }

    </script> --}}

    <script>
        function setEndDateMin() {
            const startDateInput = document.getElementById('start_registration_booking_date');
            const endDateInput = document.getElementById('end_registration_booking_date');
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
