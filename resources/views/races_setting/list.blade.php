@extends('layout.index')
@section('title', 'Races Setting ')

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
                                        <h2 class="content-header-title float-left mb-0">Races Setting</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Races Setting</li>
                                        <li class="breadcrumb-item active" aria-current="page">Races Setting</li>
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
                        <form class="dt_adv_search" action="{{ url('index_mode') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_payment">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-8 float-right">
                                                <h4 class="content-header-title float-left mb-0">Maintenance Mode:</h4>
                                                <div class="custom-control custom-switch custom-switch-success">
                                                    <input type="checkbox" id="maintenanceModeToggle" class="custom-control-input"
                                                           onclick="change_status_mode(event.target,1);" {{ $races_setting == 1 ? 'checked' : '' }} />
                                                    <label class="custom-control-label" for="maintenanceModeToggle">
                                                        <span class="switch-icon-left"></span>
                                                        <span class="switch-icon-right"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <br/><br/>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </form>
                       
                    </div>

                </div>
            </div>
            <!-- Bordered table end -->
        </div>


    </section>

@endsection
<script>
     function change_status_mode(_this,id) {
        var status = $(_this).prop('checked') == true ? 1 : 0;
        
        if (confirm("Are you sure want to change this Races Setting?")) {
            let _token = $('meta[name="csrf-token"]').attr('content');
            // alert(_token);
            $.ajax({
                url: "<?php echo url('user/change_status_mode') ?>",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    mode: status,
                    id: id
                },
                success: function(result) {
                    // console.log(result.sucess);
                    if (result.sucess == 'true') {
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
                error:function(){
                    alert('Some error occured');
                    if(status)
                        $(_this).prop("checked" , false)
                    else
                        $(_this).prop("checked" , true)
                        return false;
                }
            });
        }else{
            if(status)
                $(_this).prop("checked" , false)
            else
                $(_this).prop("checked" , true)
            return false;
        }
    }
</script>

