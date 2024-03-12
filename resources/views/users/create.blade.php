@extends('layout.index')
@section('title', 'User Create')
<!-- Dashboard Ecommerce start -->

<style>
    *{
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
                            <h2 class="content-header-title float-start mb-0">Add User</h2>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <div class="mb-1 breadcrumb-right">
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb" style="justify-content: flex-end">
                                <li class="breadcrumb-item"><a href="#">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Users</a>
                                </li>
                                <li class="breadcrumb-item active">Add User
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
                                <form class="form" action="" method="post">
                                    <input type="hidden" name="form_type" value="add_edit_user">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="user_id" id="user_id" value="{{ $id }}">

                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="row">
{{--                                                 
                                                --}}

                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" style="margin-top:20px"  for="mobile" >First name <span style="color:red;">*</span></label>
                                                        <input type="firstname" id="firstname" class="form-control col-sm-8 float-right" name="firstname"
                                                            placeholder="First Name" autocomplete="off" value="{{ old('firstname',$firstname) }}" />
                                                            <h5><small class="text-danger" id="firstname_err"></small></h5>
                                                            @error('firstname')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" style="margin-top:20px"  for="mobile" >Last Number <span style="color:red;">*</span></label>
                                                        <input type="text" id="lastname" class="form-control col-sm-8 float-right" name="lastname"
                                                            placeholder="lastname" autocomplete="off" value="{{ old('lastname',$lastname) }}" />
                                                            <h5><span class="text-danger" id="lastname_err"></span></h5>
                                                            @error('lastname')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" style="margin-top:20px"  for="mobile" >contact number <span style="color:red;">*</span></label>
                                                        <input type="text" id="mobile" class="form-control col-sm-8 float-right" name="mobile"
                                                            placeholder="mobile" autocomplete="off" value="{{ old('mobile',$mobile) }}" />
                                                            <h5><small class="text-danger" id="mobile_err"></small></h5>
                                                            @error('mobile')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left"  for="email" style="margin-top:20px">Email <span style="color:red;">*</span></label>
                                                        <input type="text" id="email" class="form-control col-sm-8 float-right" name="email"
                                                            placeholder="Email" autocomplete="off" value="{{ old('email',$email) }}" />
                                                            <h5><small class="text-danger" id="email_err"></small></h5>
                                                            @error('email')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                               
                                                

                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row">
                                                <h4 class="m-1">Credential:</h4>
                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" style="margin-top:20px"  for="mobile" >password <span style="color:red;">*</span></label>
                                                        <input type="password" id="password" class="form-control col-sm-8 float-right" name="password"
                                                            placeholder="password" autocomplete="off"/>
                                                            <h5><small class="text-danger" id="password_err"></small></h5>
                                                            @error('password')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" style="margin-top:20px"  for="mobile" >Confirm password <span style="color:red;">*</span></label>
                                                        <input type="password" id="password_confirmation" class="form-control col-sm-8 float-right" name="password_confirmation"
                                                            placeholder="Confirm Password" autocomplete="off"/>
                                                            <h5><small class="text-danger" id="password_confirmation_err"></small></h5>
                                                            @error('password_confirmation')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-md-12">
                                                    <label class="col-sm-4 float-left" for="password_confirmation" style="margin-top:10px">Status</label>
                                                    <div class="form-check mt-1 mb-2">
                                                        <input class="form-check-input status1" type="radio" name="status" id="status1" value="active" <?php if($is_active==1){ echo 'checked';};?>>
                                                        <label class="form-check-label mr-4" for="status1">
                                                            Active
                                                        </label>
                                                        <input class="form-check-input status1" type="radio" name="status" id="status2" value="inactive"  <?php if($is_active==0){ echo 'checked';};?> >
                                                        <label class="form-check-label" for="status2">
                                                            Inactive
                                                        </label>
                                                    </div>
                                                     <h5><small class="text-danger" id="status_err"></small></h5>
                                                     @error('status')
                                                        <span class="error" style="color:red;">{{ $message }}</span>
                                                    @enderror
                                                </div> 
                                    
                                                <div class="col-xs-12 col-md-12 ">
                                                <div class="form-group mb-5">
                                            
                                                    <label for="role" class="col-sm-4 float-left"> Type <span style="color:red;">*</span></label>
                                                    <select name="type" id="type" class="form-control form-select col-sm-8 float-right" onchange="return get_info()">
                                                        <option value="">Select Role</option>
                                                        @foreach ($type as $role)
                                                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <h5><small class="text-danger" id="user_role_err"></small></h5>
                                                    @error('user_role')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                </div>
                                                </div>
                                                
                                            </div>
                                        </div>

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1" onClick="return validation()">Submit</button>
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
</script>