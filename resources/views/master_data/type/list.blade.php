@extends('layout.index')
@section('title', 'Type List')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Type List</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Type</a>
                        </li>
                        <li class="breadcrumb-item active">Type List
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
                        <form class="dt_adv_search" action="{{ url('type') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_type">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <input type="text" id="name" class="form-control"
                                                    placeholder="User Name" name="name" value="{{ $search_name }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if ($search_name)
                                                    <a title="Clear" href="{{ url('type/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <a href="{{ url('/type/add') }}" class="btn btn-outline-primary float-right">
                                            <i data-feather="plus"></i><span>Add Type</span></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-center">Name</th>
                                        {{-- <th class="text-left">User Name</th> --}}
                                       
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
                                                <td><?php echo $val->name; ?></td>

                                                <td class="t-center text-center"><a target="_blank"
                                href="{{ asset('uploads/type_images/' . $val->logo) }}"><img
                                    style="width:50px ;"
                                    src="{{ asset('uploads/type_images/' . $val->logo) }}"></a>
                        </td>
                        <td class="text-center">

<div class="custom-control custom-switch custom-switch-success" >
    <input type="checkbox" class="custom-control-input"  id="{{ $val->id }}" {{
        $val->active ? 'checked' : '' }}
    onclick="change_status(event.target, {{ $val->id }});" />

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
        Confirmation = confirm('Are you sure you want to remove this type');
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
