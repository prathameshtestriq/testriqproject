@extends('layout.index')
@if (!empty($id))
    @section('title', 'Edit Advertisement Details')
@else
    @section('title', 'Add Advertisement Details')
@endif
<!-- Dashboard Ecommerce start -->
<style>
    * {
        font-size: 15px;
    }
</style>
{{-- {{ dd($a_return) }} --}}
@section('content')
    <section>
        <div class="content-body">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12 d-flex">
                            <h2 class="content-header-title float-start mb-0">
                                @if (!empty($id))
                                   Edit Advertisement Details
                                @else
                                   Add Advertisement Details
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
                                <li class="breadcrumb-item"><a href="#">Advertisement</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    @if (!empty($id))
                                        Edit Advertisement Details
                                    @else
                                        Add Advertisement Details
                                    @endif 
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

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
                                <form class="form" action="" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="form_type" value="add_edit_ad">
                                    {{ csrf_field() }}

                                    <div class="row">

                                        <div class="col-md-6">
                                            {{-- <div class="row">
                                                <div class="col-xs-12 col-md-12"> --}}
                                            <div class="form-group mb-3">
                                                <label for="name">Name <span style="color:red;">*</span></label>
                                                {{-- <input type="text" id="banner_name"
                                                            class="form-control col-sm-8 float-right" name="banner_name"
                                                            placeholder=" Name" autocomplete="off"
                                                            value="{{ old('banner_name', $banner_name) }}" /> --}}
                                                <input type="text" id="name" class="form-control"
                                                    name="name" placeholder="Name" autocomplete="off"
                                                    value="{{ old('name', $name) }}" />
                                                <h5><small class="text-danger" id="name_err"></small></h5>
                                                @error('name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>

                                           

                                            <div class="form-group mb-3">
                                                {{-- <div class="form-group"> --}}
                                                <label for="img" class="form-label">Image <span
                                                        style="color:red">*</span></label>

                                                @if (!empty($img))
                                                    <img src="{{ asset('uploads/images/' . $img) }}"
                                                        alt="Current Image" style="width: 50px;">
                                                    <input type="hidden" name="hidden_image"
                                                        value="{{ old('img', $img) }}"
                                                        accept="image/jpeg, image/png">
                                                @endif

                                                <input type="file" class="form-control" name="img"
                                                    id="img"
                                                    style="text-transform: capitalize; display: block; width: 100%;"
                                                    accept="image/jpeg, image/png" class="form-control">
                                                <span style="color:red;" id="image_err"></span>

                                                @error('img')
                                                    <span class="error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="name">Url <span style="color:red;">*</span></label>

                                                <input type="text" id="url" class="form-control"
                                                    name="url" placeholder="url" autocomplete="off"
                                                    value="{{ old('url', $url) }}" />
                                                <h5><small class="text-danger" id="url_err"></small></h5>
                                                @error('url')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-xs-12 col-md-12">
                                                    <label class="col-sm-2 float-left" for="status"
                                                        style="margin-top:10px">Status</label>
                                                    <div class="form-check mt-1 mb-2">
                                                        
                                                        <?php $activeValue = ''; // Initialize to empty string ?>
                                                        <input class="form-check-input active1" type="radio"
                                                            name="status" id="active1" value="active"
                                                            <?php if ($status == 1) {
                                                                echo 'checked';
                                                            } ?>>
                                                        <label class="form-check-label mr-4" for="active1">Active</label>
                                                        <input class="form-check-input active1" type="radio"
                                                            name="status" id="active2" value="inactive"
                                                            <?php if ($status == 0) {
                                                                echo 'checked';
                                                            } ?>>
                                                        <label class="form-check-label" for="active2">Inactive</label>
                                                    </div>
                                                    <h5><small class="text-danger" id="status_err"></small></h5>
                                                    @error('status')
                                                        <span class="error" style="color:red;">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                            </div>
                                        </div>  
                                        </div>
                                        

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/advertisement') }}" type="reset"
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
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#country').change(function() {
            //alert('here');
            var countryId = $(this).val();
            console.log("Country Id: " + countryId);
            if (countryId) {
                $.ajax({
                    url: "{{ url('get-states') }}?country_id=" + countryId,
                    type: 'GET',
                    success: function(res) {
                        console.log("Response from get-states:");
                        console.log(res);
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
            console.log("State Id: " + stateId);
            if (stateId) {
                $.ajax({
                    url: "{{ url('get-cities') }}?state_id=" + stateId,
                    type: 'GET',
                    success: function(res) {
                        console.log("Response from get-cities:");
                        console.log(res);
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
</script> --}}

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
