@extends('layout.index')
@section('title', 'Advertisement List')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    {{-- <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2"> 
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Advertisemnt List</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">add</a>
                        </li>
                        <li class="breadcrumb-item active">Add List
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>        --}}

    <div class="content-body">
        <!-- Bordered table start -->
        <div class="row" id="table-bordered">
            <div class="col-12">
                <div class="card">
                    <div class="card-header w-100">
                        <div class="content-header-left">
                            <div class="row breadcrumbs-top">
                                <div class="col-sm-12">
                                    <h2 class="content-header-title float-left mb-0">Advertisemnt List</h2>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end breadcrumb-wrapper">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mr-1">
                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                    <li class="breadcrumb-item">Advertisement</li>
                                    <li class="breadcrumb-item active" aria-current="page">Advertisement List</li>
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
                        <form class="dt_adv_search" action="{{ url('advertisement') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_ad">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <label for="form-control">Advertisement Name:</label>
                                                <input type="text" id="name" class="form-control"
                                                    placeholder="advertisement name" name="name" value="{{ $search_name }}"
                                                    autocomplete="off" />
                                            </div>
                                           
                                            <div class="col-sm-2 ">
                                                <label for="form-control">Start Date:</label>
                                                <input type="datetime-local" id="start_booking_date1" class="form-control"
                                                    placeholder="Start Date" name="start_date"   value="{{ old('start_booking_date', $search_start_booking_date ? \Carbon\Carbon::parse($search_start_booking_date)->format('Y-m-d\TH:i') : '') }}"  
                                                    autocomplete="off" />
                                            </div>
                                           
                                            <div class="col-sm-2">
                                                <label for="form-control">End Date:</label>
                                                <input type="datetime-local" id="end_booking_date1" class="form-control"
                                                    placeholder="End Date" name="end_date"  value="{{ old('end_booking_date', $search_end_booking_date ? \Carbon\Carbon::parse($search_end_booking_date)->format('Y-m-d\TH:i') : '') }}" 
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-2 col-12"> 
                                                <?php 
                                                   $advertisement_status = array(0=>'Inactive',1=>'Active' );    
                                                ?>
                                                <label for="form-control"> Status:</label>
                                                <select id="advertisement_status" name="advertisement_status" class="form-control select2 form-control">
                                                    <option value="">Select  Status</option>
                                                    <?php 
                                                        foreach ($advertisement_status as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if(old('advertisement_status',$search_advertisement_status) == $key){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>


                                            <div class="col-sm-3 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_name) || !empty($search_start_booking_date) || !empty($search_end_booking_date) || ($search_advertisement_status != ''))
                                                    <a title="Clear" href="{{ url('advertisement/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mt-2">
                                        <a href="{{ url('/advertisement/add_edit') }}" class="btn btn-outline-primary float-right">
                                            <i data-feather="plus"></i><span>Add advertisement</span></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-left">Name</th>                                            
                                            <div class="col-xs-12 col-md-12">
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
                                        <th class="text-center">Position</th>
                                        <th class="text-center">Start Date</th>
                                        <th class="text-center">End Date</th>
                                        <th class="text-center">IMAGE</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                
                                    <?php 
                                    if (!empty($ad_array)){
                                        $i = $Offset;?>
                                        <?php foreach ($ad_array as $val){
                                                $i++;?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-left">{{ $val->name }}</td>
                                                <td class="text-left">{{ $val->position }}</td>
                                                <td class="text-left">{{ date('d-m-Y H:i:s', $val->start_time)  }}</td>
                                                <td class="text-left">{{ date('d-m-Y H:i:s', $val->end_time)  }}</td>
                                                <td class="t-center text-center">

                                                    <a target="_blank" href="{{ asset('uploads/images/' . $val->img) }}">
                                                        <img style="width:50px;" src="{{ asset('uploads/images/' . $val->img) }}" alt="Banner Image">
                                                    </a>
                                                </td>


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

                                                <td>
                                                    <a href="{{ url('/advertisement/add_edit', $val->id) }}"><i
                                                            class="fa fa-edit btn btn-primary btn-sm" title="edit"></i></a>
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                        onclick="delAdd({{ $val->id }})" title="delete"></i>
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

@endsection
<script>
    function delAdd(id) {
        // alert(id);
        var url = '<?php echo url('advertisement/delete'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove this advertisement');
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
                url: "<?php echo url('advertisement/change_status') ?>",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function(result) {
                    if(result == 1){
                        // console.log(result);
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