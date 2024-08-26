@extends('layout.index')
@if (!empty($id))
    @section('title', 'Edit Role Master Details')
@else
    @section('title', 'Add Role Master Details')
@endif

@section('title', 'Marketing Create')
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
                                        <h2 class="content-header-title float-left mb-0">
                                            @if (!empty($id))
                                                Edit Role Master Details
                                            @else
                                                Add Role Master Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Role Master</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($id))
                                                Edit Role Master 
                                            @else
                                                Add Role Master
                                            @endif 
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bordered table end -->
        </div>

        @if ($message = Session::get('error'))
            <div class="demo-spacing-0 mb-1">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body">
                        {{-- <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> --}}
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
            <section id="multiple-column-form">
                <div class="row justify-content-center">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <form class="form" action="" method="post">
                                    <input type="hidden" name="form_type" value="add_edit_role_master"
                                        enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                  
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="role_name">Role Name<span style="color:red;">*</span></label>
                                                <input type="text" id="role_name" class="form-control"
                                                    placeholder="Role Name" name="role_name"  value="{{ old('role_name', $name) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="role_name_err"></small></h5>
                                                @error('role_name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/role_master') }}" type="reset"
                                                class="btn btn-outline-secondary">Cancel</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>
@endsection


