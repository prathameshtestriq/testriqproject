@extends('layout.index')
@section('title', 'Users List')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Banner List</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Users</a>
                        </li>
                        <li class="breadcrumb-item active">Banner List
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
                        <form class="dt_adv_search" action="{{ url('banner') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_banner">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <input type="text" id="name" class="form-control"
                                                    placeholder="Banner Name" name="name" value="{{ $search_banner }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if ($search_banner)
                                                    <a title="Clear" href="{{ url('banner/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <a href="{{ url('banner/add_edit') }}" class="btn btn-outline-primary float-right">
                                            <i data-feather="plus"></i><span>Add banner</span></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-left">Banner Name</th>                                                <div class="col-xs-12 col-md-12">
                                            <div class="form-group mb-5">
                                                {{-- <label class="col-sm-4 float-left" style="margin-top:20px"  for="mobile" >Contact Number <span style="color:red;">*</span></label> --}}
                                                {{-- <input type="text" id="mobile" class="form-control col-sm-8 float-right" name="mobile"
                                                    placeholder="mobile" autocomplete="off" value="{{ old('mobile',$mobile) }}" /> --}}
                                                    <h5><small class="text-danger" id="mobile_err"></small></h5>
                                                    @error('mobile')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- <th class="text-left">User Name</th> --}}
                                        <th class="text-left">banner image</th>
                                        <th class="text-left">start time</th>
                                        <th class="text-left">end time</th>
                                        <th class="text-left">Country</th>
                                        <th class="text-left">state</th>
                                        <th class="text-left">City</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                
                                    <?php 
                                    if (!empty($banner_array)){
                                        $i = $Offset;?>
                                        <?php foreach ($banner_array as $val){
                                                $i++;?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-left">{{ $val->banner_name }}</td>
                                                {{-- <td class="text-left">{{ $val->username }}</td> --}}
                                                {{-- <td class="text-left">{{ $val->banner_image }}</td> --}}

                                                <td class="t-center text-center">
                                                    <a target="_blank" href="{{ asset('uploads/banner_image/' . $val->banner_image) }}">
                                                        <img style="width:50px;" src="{{ asset('uploads/banner_image/' . $val->banner_image) }}" alt="Banner Image">
                                                    </a>
                                                </td>
                                                

                                            {{-- <td class="text-center"><img src="{{ $val->banner_image }}" alt="Banner Image" style="width: 100px;"></td> --}}
                                            
                                                <td class="text-left">{{ date('Y-m-d H:i:s',$val->start_time) }}</td>
                                                <td class="text-left">{{ date('Y-m-d H:i:s',$val->end_time) }}</td>
                                                <td class="text-left">{{ $val->country}}</td>
                                                <td class="text-left">{{ $val->state}}</td> 
                                                <td class="text-left">{{ $val->city}}</td>

                                                <td class="text-center">
                                                    <div class="custom-control custom-switch custom-switch-success">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="{{ $val->id }}" {{ $val->active ? 'checked' : '' }}
                                                            onclick="change_status(event.target, {{ $val->id }});" />
                                                        <label class="custom-control-label" for="{{ $val->id }}">
                                                            <span class="switch-icon-left"></span>
                                                            <span class="switch-icon-right"></span>
                                                        </label>
                                                    </div>
                                                </td>

                                               

                                                <td>
                                                    <a href="{{ url('/banner/add_edit', $val->id) }}"><i
                                                            class="fa fa-edit btn btn-primary btn-sm" title="edit"></i></a>
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                        onclick="delbanner({{ $val->id }})" title="delete"></i>
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
                                  {{  $Paginator->links()}}
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
//    function delbanner(id) {
//         var bConfirm = confirm('Are you sure you want to remove this banner?');
//         if (bConfirm) {
//             var url = '{{ url('banner/delete') }}/' + id;
//             window.location.href = url;
//         }
//     }


    function delbanner(id) {
        // alert(id);
        var url = '<?php echo url('banner/delete'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove this User');
        if (bConfirm) {
            window.location.href = url;
        } else {
            return false;
        }
    }


    function change_status(_this, id) {
        //  alert(id)
;
        var status = $(_this).prop('checked') == true ? 1 : 0;
        // alert(status);
        
        if (confirm("Are you sure want to change this status?")) {
            let _token = $('meta[name="csrf-token"]').attr('content');
            //alert(_token);
            $.ajax({
                url: "<?php echo url('banner/change_status') ?>",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function(result) {
                    if(result == 1){
                        console.log(result);
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
