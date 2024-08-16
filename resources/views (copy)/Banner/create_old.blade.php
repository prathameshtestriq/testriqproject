<?php

if(!empty($edit_data)){
     $id        = $edit_data['id'];
     $banner_name = $edit_data['banner_name'];
     $banner_url  = $edit_data['banner_url'];
     $banner_image    = $edit_data['banner_image'];
     $start_time     = $edit_data['start_time'];
     $end_time = $edit_data['end_time'];
     $country      = $edit_data['country'];
     $state      = $edit_data['state'];
     $city      = $edit_data['city'];
     $active      = $edit_data['active'];
 }else{
     $id        = '';
     $banner_name = '';
     $banner_url  = '';
     $banner_image    = '';
     $start_time     = '';
     $end_time = '';
     $country      = '';
     $state      = '';
     $city      = '';
     $active      = '';
 }

?>
@extends('layout.index')
@if (!empty($id))
@section('title', 'Edit Banner Details')
@else
@section('title', 'Add Banner Details')
@endif

<!-- Dashboard Ecommerce start -->
<style>
    * {
        font-size: 15px;
    }
</style>
@section('content')
<section>
    <div class="content-body">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12 d-flex">
                        <h2 class="content-header-title float-start mb-0">
                            @if (!empty($id))
                            Edit Banner Details
                            @else
                            Add Banner Details
                            @endif
                        </h2>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <div class="mb-1 breadcrumb-right">
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb" style="justify-content: flex-end">
                            <li class="breadcrumb-item"><a href="#">Home</a>
                            </li>
                            <li class="breadcrumb-item"><a href="#">Banner</a>
                            </li>
                            <li class="breadcrumb-item active">
                                @if (!empty($id))
                                Edit Banner Details
                                @else
                                Add Banner Details
                                @endif
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif -->

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if ($message = Session::get('error'))
    <div class="demo-spacing-0 mb-1">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="alert-body">
              
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
                            <form class="form" action="" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="form_type" value="add_edit_banner">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="name">Banner Name <span style="color:red;">*</span></label>
                                            <input type="text" id="banner_name" class="form-control" name="banner_name"
                                                placeholder="Banner Name" autocomplete="off"
                                                value="{{ old('banner_name', $banner_name) }}" />
                                            <h5><small class="text-danger" id="banner_name_err"></small></h5>
                                            @error('banner_name')
                                            <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="name">Banner Url <span style="color:red;">*</span></label>

                                            <input type="text" id="banner_url" class="form-control" name="banner_url"
                                                placeholder="Bannerurl" autocomplete="off"
                                                value="{{ old('banner_url', $banner_url) }}" />
                                            <h5><small class="text-danger" id="banner_url_err"></small></h5>
                                            @error('banner_url')
                                            <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="banner_image" class="form-label">Banner Image <span
                                                    style="color:red">*</span></label>
                                          
                                            <input type="file" class="form-control" name="banner_image"
                                                id="banner_image"
                                                style="text-transform: capitalize; display: block; width: 100%;"
                                                accept="image/jpeg, image/png" class="form-controlBanner Image" />                   
                                                <span style="color:red;" id="banner_image_err"></span>
                                            <h5><small class="text-danger" id="banner_image_err"></small></h5>
                                            @error('banner_image')
                                            <span class="error">{{ $message }}</span>
                                            @enderror
                                           
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-12">
                                        <div class="form-group">
                                            <span><br></span>
                                        
                                            <?php if(!empty($banner_image)){ ?>
                                                <a href="{{ asset('uploads/banner_image/' . $banner_image) }}" target="_blank"><img src="{{ asset('uploads/banner_image/' . $banner_image) }}"
                                                    alt="Current Image" style="width: 50px;"></a> 
                                                <input type="hidden" name="hidden_banner_image"
                                                    value="{{ old('banner_image', $banner_image) }}" accept="image/jpeg, image/png">
                                            <?php } //else{ ?>
                                                <!-- <div id="imagePreview">
                                                    <img id="preview" class="preview-image" src="#" alt="Image Preview">
                                                </div> -->
                                            <?php //} ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="start_time">Start Date <span style="color:red;">*</span></label>
                                            {{-- <input type="datetime-local" id="start_time" class="form-control"
                                                name="start_time" autocomplete="off"
                                                value="{{ old('start_time', $start_time ? \Carbon\Carbon::parse($start_time)->format('Y-m-d\TH:i') : '') }}" />
                                            --}}
                                            <input type="datetime-local" id="start_time" class="form-control"
                                                name="start_time" autocomplete="off"
                                                value="{{ old('start_time', $start_time ? \Carbon\Carbon::parse($start_time)->format('Y-m-d\TH:i:s') : '') }}" />
                                            <small class="text-danger">{{ $errors->first('start_time') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="end_time">End Date <span style="color:red;">*</span></label>
                                                <input type="datetime-local" id="end_time" class="form-control"
                                                    name="end_time" autocomplete="off"
                                                    value="{{ old('end_time', $end_time ? \Carbon\Carbon::parse($end_time)->format('Y-m-d\TH:i') : '') }}" />
                                                <small class="text-danger">{{ $errors->first('end_time') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="country">Country <span style="color:red">*</span></label>
                                            <select name="country" id="country" class="form-control">
                                                <option value="">Select Country</option>
                                                @foreach ($countries_array as $res)
                                                @php
                                                $selected = '';
                                                if (old('country', $country) == $res->id) {
                                                $selected = 'selected';
                                                }
                                                @endphp
                                                <option value="{{ $res->id }}" {{ $selected }}>
                                                    {{ $res->name }}</option>
                                                @endforeach
                                            </select>
                                            <span style="color:red;" id="country_err">
                                            </span>
                                            @error('country')
                                            <span class="error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="state">State <span style="color:red;">*</span></label>
                                            <select class="form-control" name="state" id="state">
                                                <option value="">Select State</option>
                                                @foreach ($states_array as $res)
                                                @php
                                                $selected = '';
                                                if (old('state', $state) == $res->id) {
                                                $selected = 'selected';
                                                }
                                                @endphp
                                                <option value="{{ $res->id }}" {{ $selected }}>
                                                    {{ $res->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger">{{ $errors->first('state') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="city">city <span style="color:red;">*</span></label>
                                            <select class="form-control" name="city" id="city">
                                                <option value="">Select city</option>
                                                @foreach ($cities_array as $res)
                                                @php
                                                $selected = '';
                                                if (old('city', $city) == $res->id) {
                                                $selected = 'selected';
                                                }
                                                @endphp

                                                <option value="{{ $res->id }}" {{ $selected }}>
                                                    {{ $res->name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger">{{ $errors->first('city') }}</small> 
                                        </div>
                                    </div>
                                </div>
                          <!--       <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-2 float-left" for="password_confirmation"
                                                    style="margin-top:9px">Status<span
                                                        style="color:red;">*</span></label>
                                                <div class="form-check mt-1 mb-2">
                                                    <?php //$activeValue = ''; // Initialize to empty string ?>
                                                    <input class="form-check-input active1" type="radio" name="active"
                                                        id="active1" value="active" <?php //if ($active==1) {
                                                        //echo 'checked' ; } ?>>
                                                    <label class="form-check-label mr-4" for="active1">Active</label>
                                                    <input class="form-check-input active1" type="radio" name="active"
                                                        id="active2" value="inactive" <?php //if ($active==0) {
                                                        //echo 'checked' ; } ?>>
                                                    <label class="form-check-label" for="active2">Inactive</label>
                                                </div>
                                                <h5><small class="text-danger" id="active_err"></small></h5>
                                                @error('active')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                        </div>
                                    </div>
                                </div> -->

                                <div class="col-12 text-center mt-1">
                                    <button type="submit" class="btn btn-primary mr-1"
                                        onClick="return validation()">Submit</button>
                                    <a href="{{ url('/banner') }}" type="reset"
                                        class="btn btn-outline-secondary">Cancel</a>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
        function previewImage() {
            var event = document.getElementById('banner_image');
            console.log('ss');
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.file[0]);
        }

        
    </script>

<script>
    $(document).ready(function() {
        $('#country').change(function() {
            //alert('here');
            var countryId = $(this).val();
            // console.log("Country Id: " + countryId);
            if (countryId) {
                $.ajax({
                    url: "{{ url('get-states') }}?country_id=" + countryId,
                    type: 'GET',
                    success: function(res) {
                        // console.log("Response from get-states:");
                        // console.log(res);
                        $('#state').empty();
                        $('#state').append('<option value="">Select</option>');
                        $.each(res, function(key, value) {
                            $('#state').append('<option value="' + key + '">' +
                                value + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                $('#state').empty();
                $('#city').empty();
            }
        });

        $('#state').change(function() {
            var stateId = $(this).val();
            // console.log("State Id: " + stateId);
            if (stateId) {
                $.ajax({
                    url: "{{ url('get-cities') }}?state_id=" + stateId,
                    type: 'GET',
                    success: function(res) {
                        // console.log("Response from get-cities:");
                        // console.log(res);
                        $('#city').empty();
                        $('#city').append('<option value="">Select</option>');
                        $.each(res, function(key, value) {
                            $('#city').append('<option value="' + key + '">' +
                                value + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                $('#city').empty();
            }
        });
    });
</script>

{{-- <script type="text/javascript">
    function validation() {  
        
        if ($('#firstname').val() == ""){
            $('#firstname').parent().addClass('has-error');
            $('#firstname_err').html('Please Enter First Name.');
            $('#firstname').focus();
            $('#firstname').keyup(function () {
            $('#firstname').parent().removeClass('has-error');
            $('#firstname_err').html('');
            });
            return false;
        }
        else{
            var filter= /^[a-zA-z]*$/;
            var txt_firstname = $('#firstname').val();
            if (!filter.test(txt_firstname)){
                $('#firstname').parent().addClass('has-error');
                $('#firstname_err').html('The firstname must only contain letters..');
                $('#firstname').focus();
                $('#firstname').keyup(function () {
                $('#firstname').parent().removeClass('has-error');
                $('#firstname_err').html('');
                });   
            return false;
            }
        } 

        if ($('#lastname').val() == ""){
            $('#lastname').parent().addClass('has-error');
            $('#lastname_err').html('Please Enter Last Name.');
            $('#lastname').focus();
            $('#lastname').keyup(function () {
            $('#lastname').parent().removeClass('has-error');
            $('#lastname_err').html('');
            });
            return false;
        }else{
            var filter= /^[a-zA-z]*$/;
            var txt_lastname = $('#lastname').val();
            if (!filter.test(txt_lastname)){
                $('#lastname').parent().addClass('has-error');
                $('#lastname_err').html('The lastname must only contain letters..');
                $('#lastname').focus();
                $('#lastname').keyup(function () {
                $('#lastname').parent().removeClass('has-error');
                $('#lastname_err').html('');
                });   
            return false;
            }
        } 

        if ($('#mobile').val() == "") {
            $('#mobile').parent().addClass('has-error');
            $('#mobile_err').html('Please Enter Mobile Number.');
            $('#mobile').focus();
            return false;
        } else if ($('#mobile').val().length < 10) {
            $('#mobile').parent().addClass('has-error');
            $('#mobile_err').html('Please Enter Valid Mobile Number');
            $('#mobile').focus();
            return false;
        }


        if ($('#email').val() == ""){
            $('#email').parent().addClass('has-error');
            $('#email_err').html('Please Enter Email.');
            $('#email').focus();
            $('#email').keyup(function () {
            $('#email').parent().removeClass('has-error');
            $('#email_err').html('');
            });
            return false;
        } else{
            var filter= /^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
            var txt_email = $('#email').val();
            if (!filter.test(txt_email)){
                $('#email').parent().addClass('has-error');
                $('#email_err').html('Please Enter valid Email.');
                $('#email').focus();
                $('#email').keyup(function () {
                $('#email').parent().removeClass('has-error');
                $('#email_err').html('');
                });   
            return false;
            }
        } 

        var emp = $('#user_id').val();
        if (emp == 0) {
            if ($('#password').val() == ""){
                $('#password').parent().addClass('has-error');
                $('#password_err').html('Please Enter Password.');
                $('#password').focus();
                $('#password').keyup(function () {
                $('#password').parent().removeClass('has-error');
                $('#password_err').html('');
                });
                return false;
            } 
        
            if ($('#password_confirmation').val() == ""){
                $('#password_confirmation').parent().addClass('has-error');
                $('#password_confirmation_err').html('Please Enter Confirm Password.');
                $('#password_confirmation').focus();
                $('#password_confirmation').keyup(function () {
                $('#password_confirmation').parent().removeClass('has-error');
                $('#password_confirmation_err').html('');
                });
                return false;
            }else if($('#password').val() != $('#password_confirmation').val()) {
                $('#password_confirmation').parent().addClass('has-error');
                $('#password_confirmation_err').html('Password Not Matches With Confirm Password.');
                $('#password_confirmation').focus();
                $('#password_confirmation').keyup(function () {
                $('#password_confirmation').parent().removeClass('has-error');
                $('#password_confirmation_err').html('');
                });   
                return false;
            }
        }
        
        }
</script> --}}