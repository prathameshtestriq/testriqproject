
<?php
 if(!empty($edit_data)){
     $id        = $edit_data->id;
     $firstname = $edit_data->firstname;
     $lastname  = $edit_data->lastname;
     $mobile    = $edit_data->mobile;
     $email     = $edit_data->email;
     $is_active = $edit_data->is_active;
     $type      = $edit_data->type;
 }else{
     $id        = '';
     $firstname = '';
     $lastname  = '';
     $mobile    = '';
     $email     = '';
     $is_active = '';
     $type      = '';
 }

?>

@extends('layout.index')
@if (!empty($id))
    @section('title', 'Edit User Details')
@else
    @section('title', 'Add User Details')
@endif

@section('title', 'User Create')
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
                                            Edit User Details
                                        @else
                                             Add User Details
                                        @endif</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                        <li class="breadcrumb-item">User</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($id))
                                                Edit User
                                            @else
                                                Add User
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
                                    <input type="hidden" name="form_type" value="add_edit_user">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="user_id" id="user_id" value="{{ $id }}">

                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="firstname">First Name <span style="color:red;">*</span></label>
                                                <input type="text" id="firstname" class="form-control"
                                                    placeholder="First Name" name="firstname" value="{{ old('firstname', $firstname) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="firstname_err"></small></h5>
                                                @error('firstname')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="lastname">Last Name <span style="color:red;">*</span></label>
                                                <input type="text" id="lastname" class="form-control"
                                                    placeholder="Last Name" name="lastname" value="{{ old('lastname',$lastname) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="lastname_err"></small></h5>
                                                @error('lastname')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="mobile">Contact Number <span style="color:red;">*</span></label>
                                                <input type="number" id="mobile" class="form-control" name="mobile"
                                                    placeholder="Contact Number" autocomplete="off" value="{{ old('mobile',$mobile) }}" />
                                                <h5><small class="text-danger" id="email_err"></small></h5>
                                                @error('mobile')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="email">Email <span style="color:red;">*</span></label>
                                                <input type="text" id="email" class="form-control" name="email"
                                                    placeholder="Email" autocomplete="off" value="{{ old('email',$email) }}" />
                                                <h5><small class="text-danger" id="email_err"></small></h5>
                                                @error('email')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="password">Password <span style="color:red;">*</span></label>
                                                <input type="password" id="password" class="form-control"
                                                    name="password" placeholder="Password" autocomplete="off" />
                                                <h5><small class="text-danger" id="password_err"></small></h5>
                                                @error('password')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="password_confirmation">Confirm Password </label>
                                                <input type="password" id="password_confirmation" class="form-control"
                                                    name="password_confirmation" placeholder="Confirm Password" />
                                                <h5><small class="text-danger" id="password_confirmation_err"></small>
                                                </h5>
                                                @error('password_confirmation')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="type">Type<span style="color:red;">*</span></label>
                                                <select id="type" name="type" class="select2 form-control">
                                                    <option value="">Select Role</option>

                                                    <option value="1" <?php echo !empty($type) && $type == 1 ? 'selected' : ''   ?> >Superadmin</option>
                                                    <option value="2" <?php echo !empty($type) && $type == 2 ? 'selected' : ''   ?> >Organizer/Admin</option>
                                                    <option value="3" <?php echo !empty($type) && $type == 3 ? 'selected' : '' ?>>User</option>
                                                </select>
                                                    <h5><small class="text-danger" id="type_err"></small></h5>
                                                @error('type')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-12"><br>
                                            <label for="password_confirmation m-2">Status :</label> <br/>
                                            <div class="demo-inline-spacing">
                                                <div class="custom-control custom-radio mt-0">
                                                    <input type="radio" id="customRadio1" name="status"
                                                        class="custom-control-input" value="active" <?php if($is_active==1){ echo 'checked';};?> />
                                                    <label class="custom-control-label" for="customRadio1">Active</label>
                                                </div>
                                                <div class="custom-control custom-radio mt-0">
                                                    <input type="radio" id="customRadio2" name="status"
                                                        class="custom-control-input" value="inactive"  <?php if($is_active==0){ echo 'checked';};?> />
                                                    <label class="custom-control-label" for="customRadio2">Inactive</label>
                                                </div>
                                            </div>
                                            <h5><small class="text-danger" id="gender_err"></small></h5>
                                            @error('status')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/users') }}" type="reset"
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
            $('#mobile').keyup(function () {
            $('#mobile').parent().removeClass('has-error');
            $('#mobile_err').html('');
            return false;
        } else if ($('#mobile').val().length < 10) {
            $('#mobile').parent().addClass('has-error');
            $('#mobile_err').html('Please Enter Valid Mobile Number');
            $('#mobile').focus();
            $('#mobile').keyup(function () {
            $('#mobile').parent().removeClass('has-error');
            $('#mobile_err').html('');
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
</script>
