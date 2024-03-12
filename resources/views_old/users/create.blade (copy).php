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
                                                        <label class="col-sm-4 float-left" for="firstname" style="margin-top:5px">First Name <span style="color:red;">*</span></label>
                                                        <input type="text" id="firstname" class="form-control col-sm-8 float-right"
                                                            placeholder="First Name" name="firstname" autocomplete="off" value="{{ old('firstname',$firstname) }}" />
                                                            <h5><small class="text-danger" id="firstname_err"></small></h5>
                                                            @error('firstname')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>



                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" for="lastname" style="margin-top:10px">Last Name <span style="color:red;">*</span></label>
                                                        <input type="text" id="lastname" class="form-control col-sm-8 float-right"
                                                            placeholder="Last Name" name="lastname" autocomplete="off" value="{{ old('lastname',$lastname) }}" />
                                                            <h5><small class="text-danger" id="lastname_err"></small></h5>
                                                            @error('lastname')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>



                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" style="margin-top:20px"  for="mobile" >Contact Number <span style="color:red;">*</span></label>
                                                        <input type="text" id="mobile" class="form-control col-sm-8 float-right" name="contact_number"
                                                            placeholder="mobile" autocomplete="off" value="{{ old('contact_number',$mobile) }}" />
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
                                                
                                                <div class="col-md-12 col-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left"  for="password">Password <span style="color:red;">*</span></label>
                                                        <input type="password" id="password" class="form-control col-sm-8 float-right" name="password"
                                                            placeholder="Password"  autocomplete="off"/>
                                                            <h5><small class="text-danger" id="password_err"></small></h5>
                                                            @error('password')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left"  for="password_confirmation" style="margin-top:10px">Confirm Password </label>
                                                        <input type="password" id="password_confirmation" class="form-control col-sm-8 float-right"
                                                            name="password_confirmation" placeholder="Confirm Password" />
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
                                                        @foreach ($type as $value)
                                                            <option value="{{ $value->id }}" {{ old('user', $type) == $value->id ? 'selected' : '' }}>{{ $value->role_name }}</option>
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


    function get_country_data()
    {
        var country_id=$('#country').val();
        var user_role= $('#user_role').val();
     
    	var url='';
		if(country_id!='')
		{ 
			$.ajax({
			url: "<?php echo url('get_country_info'); ?>",
			type: 'post',
			data: {
				_token: "{{ csrf_token() }}",
				country_id:country_id,
				
			},
			success: function(result) {
				// console.log(result)
                state_options="<option value=''>Select state</option>";
                result.states.forEach(element => {
                    state_options+='<option value="' + element.id + '">' + element.state_name + '</option>';
                });
                $('#state').html(state_options);

                $("#state").change(function(){
                   statename= $(this).val();
                  country_val= $('#country').val();
                 
                  district_options="<option value=''>Select district</option>";
                    result.districts.forEach(element => {
                        if(country_val==element.country_id && statename==element.state_id){
                            district_options+='<option value="' + element.id + '">' + element.district_name + '</option>';

                        }
                    });  $('#district').html(district_options);
                });
                
                $("#district").change(function(){
                   district_val= $(this).val();
                   state_val= $('#state').val();
                  country_val= $('#country').val();
                 
                  block_options="<option value=''>Select block</option>";
                  result.blocks.forEach(element => {
                    if(country_val==element.country_id && state_val==element.state_id && district_val==element.district_id){
                           
                        block_options+='<option value="' + element.id + '">' + element.block_name + '</option>';
                    }
                    });
                    $('#block').html(block_options);
                });
           
                    
                $("#block").change(function(){
                  block_val= $(this).val();
                   state_val= $('#state').val();
                  country_val= $('#country').val();
                  district_val=$("#district").val();
                 
                  village_options="<option value=''>Select village</option>";
                  result.villages.forEach(element => {
                    if(country_val==element.country_id && state_val==element.state_id && district_val==element.district_id && block_val==element.block_id){
                           
                        village_options+='<option value="' + element.id + '">' + element.village_name + '</option>';
                    }
                    });
                    $('#village').html(village_options);
                });
            
			},
			error: function(jqXHR, testStatus, error) {
				// console.log(error);
				alert("Page " + url + " cannot open. Error:" + error);
				$('#loader').hide();
			},
			});
		}
	}
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