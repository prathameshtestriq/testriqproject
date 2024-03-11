@extends('layout.index')
@section('title', 'Users List')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Users List</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-5 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Upload</a>
                        </li>
                        <li class="breadcrumb-item active">Farmers List
                        </li>
                    </ol>
                </div>
            </div>
        </div>
		<div class="col-md-5"></div>
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
                        {{-- <form class="dt_adv_search" action="{{ url('users') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_user">
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
                                                    <a title="Clear" href="{{ url('user/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <a href="{{ url('/user/add_edit') }}" class="btn btn-outline-primary float-right">
                                            <i data-feather="plus"></i><span>Add User</span></a>
                                    </div>
                                </div>
                            </div>
                        </form> --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-left">Name</th>
                                        <th class="text-left">User Name</th>
                                        <th class="text-left">Email ID</th>
                                        <th class="text-left">Contact Number</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                
                                    {{-- <?php 
                                    if (!empty($user_array)){
                                       $i =0;?>
                                        <?php foreach ($user_array as $val){
                                                $i++;?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-left">{{ $val->firstname }} {{ $val->lastname }}</td>
                                                <td class="text-left">{{ $val->username }}</td>
                                                <td class="text-left">{{ $val->email }}</td>
                                                <td class="text-left">{{ $val->contact_number }}</td>


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

                                                
                                            </tr>
                                      <?php }
                                    }else{?>
                                        <tr>
                                            <td colspan="8" style="text-align:center; color:red;">No Record Found</td>
                                        </tr>
                                  <?php }?> --}}
                                </tbody>
                            </table>
                            <div class="card-body">
                                <div class="d-flex justify-content-end">
                                    {{-- {{ $Paginator }} --}}
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
