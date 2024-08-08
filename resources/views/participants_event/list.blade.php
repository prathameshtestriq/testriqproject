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
                            </div>
                        </div>
                        <div class="d-flex justify-content-end breadcrumb-wrapper">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mr-1">
                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
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
                                                <label for="form-control">Participant name:</label>
                                                <input type="text" id="search_participant_name" class="form-control"
                                                    placeholder="Participant name" name="participant_name" value="{{ $search_participant_name }}"
                                                    autocomplete="off" />
                                            </div>


                                            <div class="col-sm-2 col-12">
                                                    <?php 
                                                       $Transaction_Status = array(0=>'Inprocess',1=>'Success', 2=>'Fail', 3=>'Free' );    
                                                    ?>
                                                    <label for="form-control">Payment Status:</label>
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
                                                <label for="form-control">Registration Id:</label>
                                                <input type="text" id="registration_id" class="form-control"
                                                    placeholder="Registration Id" name="registration_id" value="{{ $search_registration_id }}"
                                                    autocomplete="off" />
                                            </div>

                                            
                                            {{-- <div class="col-sm-2 col-12">
                                                <label for="form-control">Mobile No:</label>
                                                <input type="text" id="mobile_no" class="form-control"
                                                    placeholder="Mobile No" name="mobile_no" value="{{ $search_mobile_no }}"
                                                    autocomplete="off" />
                                            </div> --}}

                                            <div class="col-sm-2 col-12 ">
                                                <label for="form-control">Email/Mobile Id:</label>
                                                <input type="text" id="email_id" class="form-control"
                                                    placeholder="Email/Mobile Id" name="email_id" value="{{ $search_email_id }}"
                                                    autocomplete="off" />
                                            </div>
                                            <div class="col-sm-2 col-12 ">
                                                <label for="form-control">Category:</label>
                                                <input type="text" id="category" class="form-control"
                                                    placeholder="Category" name="category" value="{{ $search_category }}"
                                                    autocomplete="off" />
                                            </div>
                                            <div class="col-sm-2 col-12 ">
                                                <label for="form-control">Start Booking Date:</label>
                                                <input type="datetime-local" id="start_booking_date" class="form-control"
                                                    placeholder="Start Date" name="start_booking_date" value="{{ old('start_booking_date', $search_start_booking_date ? \Carbon\Carbon::parse($search_start_booking_date)->format('Y-m-d\TH:i') : '') }}"
                                                    autocomplete="off" />
                                            </div>
                                          
                                            <div class="col-sm-2 col-12 mt-2">
                                                <label for="form-control">End Booking Date:</label>
                                                <input type="datetime-local" id="end_booking_date" class="form-control"
                                                    placeholder="End Date" name="end_booking_date" value="{{ old('end_booking_date', $search_end_booking_date ? \Carbon\Carbon::parse($search_end_booking_date)->format('Y-m-d\TH:i') : '') }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-2 col-12  mt-2">
                                                <label for="form-control">Transaction/Order Id:</label>
                                                <input type="text" id="transaction_order_id" class="form-control"
                                                    placeholder="Transaction/Order Id" name="transaction_order_id" value="{{ $search_transaction_order_id }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-8 mt-3">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_participant_name) || !empty($search_registration_id) || !empty($search_mobile_no) 
                                                || !empty($search_email_id) || !empty($search_category) || !empty($search_start_booking_date) 
                                                || !empty($search_end_booking_date) ||  $search_transaction_status !== '' || !empty($search_transaction_order_id))
                                                {{-- {{ url('/participants_event/'.$event_participants[0]->event_id.'/clear_search') }} --}}
                                                    <a title="Clear" href="{{ url('/participants_event/'.$event_id.'/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                                <div class="float-right">
                                                    <a href="{{ url('participants_event/'.$event_id.'/export_revenue') }}" class="btn btn-danger text-white ">Revenue </a>
                                                    @if (!empty($event_participants))
                                                      <a href="{{ url('participants_event/'.$event_id.'/export_download') }}" class="btn btn-danger text-white ">Download </a>
                                                    @endif
                                                    <a href="{{ url('/event') }}"  class="btn btn-primary ">
                                                        <span>Back</span></a>
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
                                        <th class="text-center">Transaction/Payment Status</th>
                                        <th class="text-center">Email Address</th>
                                        <th class="text-center">Mobile Number</th>
                                        <th class="text-center">Category Name</th>
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
                                                <td class="text-left">{{ $val->user_name }}</td>
                                                <td class="text-left">{{ date('d-m-Y H:i:s', $val->booking_date) }}</td>
                                                <td class="text-left">{{ $val->Transaction_order_id }}</td>
                                                <td class="text-left">{{ $val->registration_id }}</td>
                                                <td class="text-left">{{ $val->payu_id }}</td>
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
                                                <td class="text-left">{{ $val->email }}</td>
                                                <td class="text-left">{{ $val->mobile }}</td>
                                                <td class="text-left">
                                                 {{ $val->category_name }}
                                                </td>
                                             
                                              
                                               
                                                <td>
                                                    {{-- <a href=""><i
                                                            class="fa fa-edit btn btn-primary btn-sm" title="edit"></i></a> --}}
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm" onclick="remove_type({{ $val->id }},{{$val->event_id}})" title="delete"></i>
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
               
                Confirmation = confirm('Are you sure you want to remove this type');
                if (Confirmation) {

                    window.location.href = url;

                }
            }

    </script>

@endsection
