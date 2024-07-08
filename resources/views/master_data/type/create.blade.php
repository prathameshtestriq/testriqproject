@extends('layout.index')
@if (!empty($id))
@section('title', 'Edit Type Details')
@else
@section('title', 'Add Type Details')
@endif
<?php //dd($Category_array)?>

<!-- Include necessary stylesheets -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* Adjust font size for better readability */
    * {
        font-size: 15px;
    }
</style>

@section('content')
<section>
    <div class="content-body">
        <!-- Page header -->
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12 d-flex">
                        <h2 class="content-header-title float-start mb-0">
                            @if (!empty($id))
                            Edit Type Details
                            @else
                            Add Type Details
                            @endif
                        </h2>
                    </div>
                </div>
            </div>
            <!-- Breadcrumbs -->
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <div class="mb-1 breadcrumb-right">
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb" style="justify-content: flex-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Type</a></li>
                            <li class="breadcrumb-item active">
                                @if (!empty($id))
                                Edit Type Details
                                @else
                                Add Type Details
                                @endif
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Display error message if any -->
    @if ($message = Session::get('error'))
    <div class="demo-spacing-0 mb-1">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="alert-body">
                {{ $message }}
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
                            <!-- Form start -->
                            <form method="post" name="transfer" id="transfer" novalidate="novalidate"
                                enctype="multipart/form-data">
                                <input type="hidden" name="form_type" value="add_edit_type">
                                {{ csrf_field() }}

                                <div class="row">
                                    <!-- Column 1: Input fields -->
                                    <div class="col-md-6">
                                        <!-- Name input field -->
                                        <div class="form-group mb-3">
                                            <label for="name">Type Name <span style="color:red;">*</span></label>
                                            <input type="text" id="name" class="form-control" name="name"
                                                placeholder="Event Name" autocomplete="off"
                                                value="{{ old('name', $name) }}" />
                                            <span style="color:red;" id="name_err"></span>
                                            @error('name')
                                            <span class="error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                       
                                    </div>

                                    <!-- Column 2: Image and Status -->
                                    <div class="col-md-4">
                                        <!-- Image input field -->
                                        <div class="form-group mb-3">
                                            <label for="logo" class="form-label">Image <span
                                                    style="color:red">*</span></label>
                                           
                                            <input type="file" name="logo" id="logo"
                                                style="text-transform: capitalize; display: block; width: 100%;"
                                                accept="image/jpeg, image/png" class="form-control">
                                            <span style="color:red;" id="logo_err"></span>
                                            @error('logo')
                                            <span class="error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-2">
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

                                </div>

                                <!-- Submit Button -->
                                <div class="col-12 text-center mt-1">
                                    <button type="submit" class="btn btn-primary mr-1"
                                        onClick="return validation()">Submit</button>
                                    <a href="{{ url('/type') }}" type="reset"
                                        class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </form>
                            <!-- Form end -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>
@endsection

<script>
    function validation() {
        var isValid = true;
        if ($('#name').val() == "") {
            $('#name_err').html('Please enter name.');
            $('#name').focus();
            $('#name').keyup(function () {
                $('#name').parent().removeClass('has-error');
                $('#name_err').html('');
            });
            isValid = false;
        }

        if ($('#type').val() == "") {
            $('#type_err').html('Please enter type.');
            $('#type').focus();
            $('#type').keyup(function () {
                $('#type').parent().removeClass('has-error');
                $('#type_err').html('');
            });
            isValid = false;
        }

        if ($('#status').val() == "") {
            $('#status_err').html('Please select status.');
            $('#status').focus();
            $('#status').keyup(function () {
                $('#status').parent().removeClass('has-error');
                $('#status_err').html('');
            });
            isValid = false;
        }

        $('.error').html('');
        var logo = $('#logo').prop('files')[0];
        var existingImage = $('input[name="hidden_logo"]').val();
        if (!logo && !existingImage) {
        //  alert('here');
            $('#logo_err').html('Please select a image.');
            isValid = false;
        } else if (logo) {
            var maxSize = 2 * 1024 * 1024; // 2MB in bytes
            if (logo.size > maxSize) {
                $('#logo_err').html('Please select a file smaller than 2MB.');
                $('#logo').val('');
                isValid = false;
            }

            var allowedTypes = ['jpg', 'jpeg', 'png'];
            var fileType = logo.name.split('.').pop().toLowerCase();
            if ($.inArray(fileType, allowedTypes) === -1) {
                $('#logo_err').html('Please select a valid file type (jpg, jpeg, png).');
                $('#logo').val('');
                isValid = false;
            }
        }

        return isValid;
    }


</script>