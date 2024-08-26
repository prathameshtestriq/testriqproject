@extends('layout.index')
@section('title', 'Payment ')

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
                                        <h2 class="content-header-title float-left mb-0">Payment List</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Payment</li>
                                        <li class="breadcrumb-item active" aria-current="page">Payment List</li>
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
                        <form class="dt_adv_search" action="{{ url('payment_log') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_payment">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <label for="form-control">Username:</label>
                                                <input type="text" id="name" class="form-control"
                                                    placeholder="User Name" name="name" value="{{$search_user_name }}"
                                                    autocomplete="off" />
                                            </div>
                                         
                                            <div class="col-sm-2 ">
                                                <label for="form-control">Start Date:</label>
                                                <input type="date" id="start_payment_date" class="form-control"
                                                    placeholder="Start Date" name="start_payment_date" value="{{ old('start_payment_date', $search_start_payment_date ? \Carbon\Carbon::parse($search_start_payment_date)->format('Y-m-d') : '') }}"
                                                    autocomplete="off" />
                                            </div>
                                            
                                            <div class="col-sm-2">
                                                <label for="form-control">End Date:</label>
                                                <input type="date" id="end_payment_date" class="form-control"
                                                    placeholder="End Date" name="end_payment_date" value="{{ old('end_payment_date', $search_end_payment_date ? \Carbon\Carbon::parse($search_end_payment_date)->format('Y-m-d') : '') }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-2">
                                                <label for="form-control">Transaction Id:</label>
                                                <input type="text" id="transaction_id_payment" class="form-control"
                                                    placeholder="Transaction Id" name="transaction_id_payment" value=""
                                                    autocomplete="off" />
                                            </div>
                                            
                                            <div class="col-sm-2">
                                                <?php 
                                                   $Transaction_Status = ['success','initiate','failure','Free'];    
                                                ?>
                                                <label for="form-control">Transaction Status:</label>
                                                <select id="transaction_status_payment" name="transaction_status_payment" class="form-control select2 form-control">
                                                    <option value="">Select Transaction Status</option>
                                                    <?php 
                                                        foreach ($Transaction_Status as  $value)
                                                        {
                                                            $selected = '';
                                                            if(old('transaction_status_payment', $search_transaction_status_payment) == $value){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                         
                                            <div class="col-sm-2 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_user_name) || !empty($search_start_payment_date) || !empty($search_end_payment_date) || !empty($search_transaction_id_payment) || !empty($search_transaction_status_payment)  )
                                                    <a title="Clear" href="{{ url('/payment_log/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear 
                                                    </a>
                                                @endif     
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="float-right">
                                        <a href="{{ url('payment_log/export_payment_log') }}" class="btn btn-danger text-white ">Download </a>
                                        <a href="{{ url('/dashboard') }}"  class="btn btn-primary ">
                                            <span>Back</span></a>
                                    </div>   --}}
                                    <div class="col-sm-12 mt-1   float-right">
                                        <a href="{{ url('payment_log/export_payment_log') }}" class="btn btn-danger text-white float-right ml-2 ">Download </a>
                                        <a href="{{ url('/dashboard') }}" class="btn  btn-primary  float-right">
                                            <span>Back</span></a>
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
                                        <th class="text-left">Email</th>
                                        <th class="text-left">Mobile</th>
                                        <th class="text-left">Transaction Id</th>
                                        <th class="text-left">Pay Id</th>
                                        <th class="text-left">Total Amount</th>
                                        <th class="text-left">Payment Date</th>
                                        <th class="text-center">Transaction Status</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">

                                    <?php 
                                    if (!empty($payment_array)){
                                       
                                        $i = $Offset;?>
                                    <?php foreach ($payment_array as $val){

                                        $data = json_decode( $val->post_data , true); // Decode JSON into an associative array

                                        if (isset($data['mihpayid'])) {
                                            $mihpayid = $data['mihpayid'];
                                        } else {
                                            $mihpayid = '-'; // Handle cases where JSON is invalid or mihpayid is missing
                                        }
                                        $i++;?>
                                    <tr> 
                                        <td class="text-center">{{ $i }}</td>
                                        <td class="text-left">{{ $val->firstname }} {{ $val->lastname }}</td>
                                        <td class="text-left">{{ $val->email }}</td>
                                        <td class="text-left">{{ $val->mobile }}</td>
                                        <td class="text-left">{{ $val->txnid }}</td>
                                        <td class="text-left">{{ $mihpayid }}</td>
                                        <td class="text-left">{{ $val->amount }}</td>
                                        <td class="text-left">{{ date('d-m-Y H:i A',$val->created_datetime) }}</td>
                                        <td class="text-center">{{ $val->payment_status }}</td>
                                    </tr>
                                    <?php }
                                    }else{?>
                                    <tr>
                                        <td colspan="16" style="text-align:center; color:red;">No Record Found</td>
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

@endsection
<script>
   

    // function delbanner(id) {
    //     // alert(id);
    //     var url = '<?php echo url('banner/delete'); ?>';
    //     url = url + '/' + id;
    //     //    alert(url);
    //     bConfirm = confirm('Are you sure you want to remove this User');
    //     if (bConfirm) {
    //         window.location.href = url;
    //     } else {
    //         return false;
    //     }
    // }


  
</script>
