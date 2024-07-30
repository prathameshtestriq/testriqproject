@extends('layout.index')
@section('title', 'Payment List')

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
                                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
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
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <label for="form-control">Username:</label>
                                                <input type="text" id="name" class="form-control"
                                                    placeholder="User Name" name="name" value="{{$search_user_name }}"
                                                    autocomplete="off" />
                                            </div>
                                         
                                            <div class="col-sm-2 ">
                                                <label for="form-control">Start Date:</label>
                                                <input type="datetime-local" id="start_payment_date" class="form-control"
                                                    placeholder="Start Date" name="start_payment_date" value="{{ old('start_payment_date', $search_start_payment_date ? \Carbon\Carbon::parse($search_start_payment_date)->format('Y-m-d\TH:i') : '') }}"
                                                    autocomplete="off" />
                                            </div>
                                            
                                            <div class="col-sm-2">
                                                <label for="form-control">End Date:</label>
                                                <input type="datetime-local" id="end_payment_date" class="form-control"
                                                    placeholder="End Date" name="end_payment_date" value="{{ old('end_payment_date', $search_end_payment_date ? \Carbon\Carbon::parse($search_end_payment_date)->format('Y-m-d\TH:i') : '') }}"
                                                    autocomplete="off" />
                                            </div>

                                         


                                            <div class="col-sm-3 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_user_name) || !empty($search_start_payment_date) || !empty($search_end_payment_date) )
                                                    <a title="Clear" href="{{ url('/payment_log/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-sm-4 mt-2">
                                        <a href="{{ url('banner/add_edit') }}" class="btn btn-outline-primary float-right">
                                            <i data-feather="plus"></i><span>Add banner</span></a>
                                    </div> --}}
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
                                                $i++;?>
                                    <tr>
                                        <td class="text-center">{{ $i }}</td>
                                        <td class="text-left">{{ $val->firstname }} {{ $val->lastname }}</td>
                                        <td class="text-left">{{ $val->email }}</td>
                                        <td class="text-left">{{ $val->mobile }}</td>
                                        <td class="text-left">{{ $val->txnid }}</td>
                                        <td class="text-left">{{ $val->paymentId }}</td>
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
