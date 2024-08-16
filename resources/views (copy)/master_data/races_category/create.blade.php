@extends('layout.index')
@if (!empty($id))
    @section('title', ' Races Category')
@else
    @section('title', 'Races Category')
@endif

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

@section('title', 'Type Create')
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
                                                Edit Races Category Details
                                            @else
                                                Add Races Category Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                        <li class="breadcrumb-item">Type</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($id))
                                            Edit Type Details
                                            @else
                                            Add Type Details
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
                                <form method="post" name="transfer" id="transfer" novalidate="novalidate"
                                enctype="multipart/form-data">
                                <input type="hidden" name="form_type" value="add_edit_type">
                                {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="name">Race Category Name<span
                                                        style="color:red;">*</span></label>
                                                <input type="text" id="name" class="form-control mt-2"
                                                    placeholder="Race Category Name" name="name"
                                                    value="{{ old('name', $name) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="name_err"></small></h5>
                                                @error('name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="logo">Image <span style="color:red;">*</span></label>
                                                <p style="color:red;">Allowed JPEG, JPG or PNG. Max file size of 2 MB</p>
                                                <input type="file" id="logo" class="form-control"
                                                    placeholder="Logo Name" name="logo"
                                                    style="text-transform: capitalize; display: block; width: 100%;"
                                                    accept="image/jpeg, image/png"
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="logo_err"></small></h5>
                                                @error('logo')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-1">
                                            <span><br/></span>
                                            @if (!empty($logo))
                                                <a href="{{ asset('uploads/type_images/' . $logo) }}" target="_blank">
                                                    <img src="{{ asset('uploads/type_images/' . $logo) }}" alt="Current Image"
                                                    style="width: 50px;">
                                                </a>
                                                <input type="hidden" name="hidden_logo" value="{{ old('logo', $logo) }}"
                                                    accept="image/jpeg, image/png">
                                            @endif
                                        </div>

                                        <div class="col-md-3 col-12">
                                            <div class="form-group mt-2">
                                                <input type="checkbox" id="show_as_home" class="checkbox m-1" name="show_as_home" value="1"<?php if($show_as_home==1){ echo 'checked';};?> autocomplete="off" style="transform: scale(1.5);" />
                                                <label for="show_as_home">Show as home</label>
                                                <h5><small class="text-danger" id="show_as_home_err"></small></h5>
                                                @error('show_as_home')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/type') }}" type="reset"
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

<script type="text/javascript">
    // function validation() {

    //     var isValid = true;
    //     if ($('#name').val() == "") {
    //         $('#name_err').html('Please enter name.');
    //         $('#name').focus();
    //         $('#name').keyup(function () {
    //             $('#name').parent().removeClass('has-error');
    //             $('#name_err').html('');
    //         });
    //         isValid = false;
    //     }

    //     if ($('#type').val() == "") {
    //         $('#type_err').html('Please enter type.');
    //         $('#type').focus();
    //         $('#type').keyup(function () {
    //             $('#type').parent().removeClass('has-error');
    //             $('#type_err').html('');
    //         });
    //         isValid = false;
    //     }

    //     if ($('#status').val() == "") {
    //         $('#status_err').html('Please select status.');
    //         $('#status').focus();
    //         $('#status').keyup(function () {
    //             $('#status').parent().removeClass('has-error');
    //             $('#status_err').html('');
    //         });
    //         isValid = false;
    //     }

    //     $('.error').html('');
    //     var logo = $('#logo').prop('files')[0];
    //     var existingImage = $('input[name="hidden_logo"]').val();
    //     if (!logo && !existingImage) {
    //     //  alert('here');
    //         $('#logo_err').html('Please select a image.');
    //         isValid = false;
    //     } else if (logo) {
    //         var maxSize = 2 * 1024 * 1024; // 2MB in bytes
    //         if (logo.size > maxSize) {
    //             $('#logo_err').html('Please select a file smaller than 2MB.');
    //             $('#logo').val('');
    //             isValid = false;
    //         }

    //         var allowedTypes = ['jpg', 'jpeg', 'png'];
    //         var fileType = logo.name.split('.').pop().toLowerCase();
    //         if ($.inArray(fileType, allowedTypes) === -1) {
    //             $('#logo_err').html('Please select a valid file type (jpg, jpeg, png).');
    //             $('#logo').val('');
    //             isValid = false;
    //         }
    //     }

    //     return isValid;

    // }
</script>
