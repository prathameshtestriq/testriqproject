@extends('layout.index')
@section('title', 'Brands List')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Brands List</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Master</a>
                        </li>
                        <li class="breadcrumb-item active">Brands List
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>       
  </section>
    <section>
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
                            <input type="hidden" name='form_type' value='search_master_brand'>
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <a href="{{ url('/master_brands/add') }}" class="btn btn-outline-primary float-right">
                                            <i data-feather="plus"></i><span>Add Brand</span></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-left">Brand Name</th>
                                        <th class="text-left">Active</th>
                                        <th class="text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                
                                    <?php 
                                    if (!empty($master_brand)){
                                       $i =0;?>
                                        <?php foreach ($master_brand as $val){
                                          
                                                $i++;?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-left">{{ $val->brand_name }}</td>
                                                <td class="text-center">
                                                    <div class="custom-control custom-switch custom-switch-success">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="{{ $val->id }}" {{ $val->is_active ? 'checked' : '' }}
                                                            onclick="change_status(event.target, {{ $val->id }});" />
                                                        <label class="custom-control-label" for="{{ $val->id }}">
                                                            <span class="switch-icon-left"></span>
                                                            <span class="switch-icon-right"></span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a data-toggle="modal" id="smallbtn" data-target="#smallModal"
                                                        href="javascript:void(0);" onClick="programbrand({{ $val->id }})"  title="add" data-bs-toggle="modal" data-bs-target="#exampleModallaptop1" >
                                                    <i class="btn btn-success btn-sm " >Add Program</i>
                                                    </a>
                                                    <a data-toggle="modal" id="smallButton" data-target="#smallModal"
                                                    href="javascript:void(0);" onClick="showDetails({{ $val->id }})" title="show" data-bs-toggle="modal" data-bs-target="#exampleModallaptop1" >
                                                    <i class="btn btn-success btn-sm " >View</i>
                                                    </a>
                                                    <a href="{{ url('/master_brands/edit', $val->id) }}"><i
                                                            class="fa fa-edit btn btn-primary btn-sm " title="edit"></i></a>
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                        onclick="delBrand({{ $val->id }})" title="delete"></i>
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
                        </div>
                    </div>

                </div>
            </div>
            <!-- Bordered table end -->
        </div>
       

    </section>
    <div class="modal fade" id="program_details_modal" tabindex="-1" role="dialog" aria-labelledby="business_customer_details_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">
            <div class="col-xl-12">
              <div class="card social-profile mb-0">
  
                <div class="card-body" id="program_details_body">
                   
                </div>         
                
              </div>
            </div>  
          </div>
        </div>
    </div>

    <div class="modal fade" id="add_brand_program_modal" tabindex="-1" role="dialog" aria-labelledby="add_brand_program_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">
            <div class="col-xl-12">
              <div class="card social-profile mb-0" id>
  
                <div class="card-body" id="add_brand_program_body">
                   
                </div>         
                <br>
              </div>
            </div>  
          </div>
        </div>
    </div>
    

@endsection
<script>
    function delBrand(id) {
        // alert(id);
        var url = '<?php echo url('master_brands/remove'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove this Brand');
        if (bConfirm) {
            window.location.href = url;
        } else {
            return false;
        }
    }

    function showDetails(brand_id){
        // alert(brand_id);
        var url;
        $.ajax({
                url: "<?php echo url('/master_brands/view') ?>/"+brand_id,
            //  alert(url);
            beforeSend: function() {
                $('#loader').show();
            },
            // return the result
            success: function(result) {
                // console.log(result);
                
                $('#program_details_body').html(result);
                $('#program_details_modal').modal("show");
            },
            complete: function() {
                $('#loader').hide();
            },
            error: function(jqXHR, testStatus, error) {
                console.log(error);
                alert("Page " + url + " cannot open. Error:" + error);
                $('#loader').hide();
            },
            timeout: 8000
        })
    }
       
    function programbrand(brand_id){
        // alert(brand_id);
        var url;
        $.ajax({
                url: "<?php echo url('/master_brands/brand_program_ajx') ?>/"+brand_id,
            //  alert(url);
            beforeSend: function() {
                $('#loader').show();
            },
            data:{
                    'brand_id':brand_id,
                },
            // return the result
            success: function(result) {
                // console.log(result);
                
                $('#add_brand_program_body').html(result);
                $('#add_brand_program_modal').modal("show");
            },
            complete: function() {
                $('#loader').hide();
            },
            error: function(jqXHR, testStatus, error) {
                console.log(error);
                alert("Page " + url + " cannot open. Error:" + error);
                $('#loader').hide();
            },
            timeout: 8000
        })

    }

    function change_status(_this, id) {
        // alert(id);
        var status = $(_this).prop('checked') == true ? 1 : 0;
        // alert(status);
        
        if (confirm("Are you sure want to change this status?")) {
            let _token = $('meta[name="csrf-token"]').attr('content');
            //alert(_token);
            $.ajax({
                url: "<?php echo url('master_brands/change_status') ?>",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function(result) {
                    console.log(result);
                    if(result == 1){
                        alert('Status changed successfully')
                        //location.reload(); 
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

