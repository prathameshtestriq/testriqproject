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


        <div class="content-body">
            <!-- Bordered table start -->
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card "><br>
                      <form action=""></form>
                        <div class="col-sm-12 mt-2 flaot-end">
                            <a href="{{ url('email_sending/add') }}" class="btn btn-outline-primary float-right">
                                <i data-feather="plus"></i><span>Add Email</span></a>
                        </div><br>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        {{-- <th class="text-center">Event Name</th> --}}
                                        <th class="text-left">Subject</th>          
                                        {{-- <th class="text-left">Message</th> --}}
                                        <th class="text-left">Recipient Type</th>
                                        <th class="text-left">Recipient Count</th>
                                        {{-- <th class="text-left">Email</th> --}}
                                        <th class="text-left">Sent Email Date</th>
                                        <th class="text-left">status</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">

                                    <?php 
                                    if (!empty($Email_details)){
                                        $i = 0;?>
                                    <?php foreach ($Email_details as $val){
                                            $i++;?>
                                    <tr>
                                        <td class="text-center">{{ $i }}</td>
                                        <td class="text-left">{{ !empty($val->subject)?$val->subject :'-' }}</td>
                                        <td class="text-left">{{ !empty($val->recipient_type)?$val->recipient_type : '-'}}</td>
                                        <td class="text-left">{{ !empty($val->recipient_count)?$val->recipient_count:'-'  }}</td>
                                        {{-- <td class="text-left">{{ !empty($val->email)?$val->email:'-' }}</td> --}}
                                        <td class="text-center">{{ date('d-m-Y H:i:s',$val->sent_date_time) }}</td>
                                        <td class="text-center">
                                            <div class="custom-control custom-switch custom-switch-success">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="{{ $val->id }}" {{ $val->status ? 'checked' : '' }}
                                                    onclick="change_status(event.target, {{ $val->id }});" />
                                                <label class="custom-control-label" for="{{ $val->id }}">
                                                    <span class="switch-icon-left"></span>
                                                    <span class="switch-icon-right"></span>
                                                </label>
                                            </div>
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
                                    {{-- {{ $Paginator->links() }} --}}
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
                    if (result == 1) {
                        console.log(result);
                        alert('Status changed successfully')
                        //location.reload(); 
                    } else {
                        alert('Some error occured');
                        if (status)
                            $(_this).prop("checked", false)
                        else
                            $(_this).prop("checked", true)
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
@endsection
