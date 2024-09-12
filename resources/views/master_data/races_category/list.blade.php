@extends('layout.index')
@section('title', 'Races Category ')

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
                                    <h2 class="content-header-title float-left mb-0">Races Category </h2>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end breadcrumb-wrapper">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mr-1">
                                    <li class="breadcrumb-item">Home</li>
                                    <li class="breadcrumb-item">Races Category</li>
                                    <li class="breadcrumb-item active" aria-current="page">Races Category List</li>
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
                        <form class="dt_adv_search" action="{{ url('type') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_type">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="form-control"> Race Category Name</label>
                                                <input type="text" id="name" class="form-control"
                                                    placeholder="Races Category Name" name="name" value="{{ $search_name }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-3 "> 
                                                <?php 
                                                   $type_status = array(0=>'Inactive',1=>'Active' );    
                                                ?>
                                                <label for="form-control"> Status</label>
                                                <select id="type_status" name="type_status" class="form-control select2 form-control">
                                                    <option value="">Select  Status</option>
                                                    <?php 
                                                        foreach ($type_status as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if(old('type_status',$search_type_status) == $key){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3 "> 
                                                <?php 
                                                   $Show_as_home = array(0=>'No',1=>'Yes' );    
                                                ?>
                                                <label for="form-control"> Show As Home</label>
                                                <select id="Show_as_home" name="Show_as_home" class="form-control select2 form-control">
                                                    <option value="">Select Show As Home </option>
                                                    <?php 
                                                        foreach ($Show_as_home as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if(old('Show_as_home',$search_show_as_home) == $key){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                           

                                            <div class="col-sm-2 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_name) || ($search_type_status != '') || ($search_show_as_home != '') )
                                                    <a title="Clear" href="{{ url('type/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                                
                                            </div>
                                            <div class="col-sm-1 mt-2">
                                                <a href="{{ url('/type/add') }}" class="btn btn-outline-primary float-right pr-2">
                                                    <i data-feather="plus"></i><span>Add </span></a>
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
                                        <th class="text-left">Race Name</th>
                                        {{-- <th class="text-left">User Name</th> --}}
                                        <th class="text-center">Show As Home</th>
                                        <th class="text-center">Image</th>
                                        <th style="text-align: center;">Active</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                
                                    <?php 
                                    if (!empty($type_array)){
                                        $i = $Offset;?>
                                        <?php foreach ($type_array as $val){
                                                $i++;?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-left"><?php echo ucfirst($val->name); ?></td>
                                                <td>{{ !empty($val->show_as_home == 1) ? 'Yes' : 'No'}}</td>
                                                <td class="t-center text-center">
                                                    <?php if(!empty($val->logo)){ ?>
                                                        <a target="_blank" title="View Image" href="{{ asset('uploads/type_images/' . $val->logo) }}">
                                                            <img style="width:50px;"
                                                            src="{{ asset('uploads/type_images/' . $val->logo) }}">
                                                        </a>
                                                    <?php }else{
                                                        echo '-';
                                                    }
                                                    ?>
                                                 </td>
                                                <td class="text-center">
                                                    <div class="custom-control custom-switch custom-switch-success" >
                                                        <input type="checkbox" class="custom-control-input"  id="{{ $val->id }}" 
                                                        {{$val->active ? 'checked' : '' }} onclick="change_status(event.target, {{ $val->id }});" />

                                                        <label class="custom-control-label" style="cursor: pointer;" for="{{ $val->id }}">
                                                            <span class="switch-icon-left"></span>
                                                            <span class="switch-icon-right"></span>
                                                        </label>
                                                    </div>
                                                </td>

                                                <td>
                                                    <a href="{{ route('edit_type', $val->id) }}"><i
                                                            class="fa fa-edit btn btn-primary btn-sm" title="edit"></i></a>
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                        onclick="remove_type({{ $val->id }})" title="delete"></i>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="9" style="text-align: center;">No record found</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-end">
                                {{ $Paginator->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>

<div class="flex-grow-1"></div>

<script>
     function remove_type(iId) {
        // alert(iId);
        var url = '<?php echo url('/type/remove_type') ?>';
        url = url + '/' + iId;
        // alert(url);
        Confirmation = confirm('Are you sure you want to remove this record ?');
        if (Confirmation) {

            window.location.href = url;

        }
    }

    function change_status(_this, id) {
        var active = $(_this).prop('checked') == true ? 1 : 0;

        if (confirm("Are you sure you change this status?")) {
            $.ajax({
                url: "<?php echo url('type/change_status') ?>",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    active: active
                },
                success: function (result) {
                    console.log(result);
                    if (result == 1) {
                        alert('Status changed successfully');
                    } else {
                        alert('Some error occurr ed');
                    }
                },
                error: function () {
                    alert('Some error occurred');
                }
            });
        } else {
            $(_this).prop("checked", !active);
        }
    }
  
</script>
@endsection
