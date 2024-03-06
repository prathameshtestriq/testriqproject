@extends('layout.index')
@section('title', 'Event Create')
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
                            <h2 class="content-header-title float-start mb-0">Add Event</h2>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <div class="mb-1 breadcrumb-right">
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb" style="justify-content: flex-end">
                                <li class="breadcrumb-item"><a href="#">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Event</a>
                                </li>
                                <li class="breadcrumb-item active">Add Event
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
                                    <input type="hidden" name="form_type" value="add_edit_event">
                                    {{ csrf_field() }}
                                

                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="row">
                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left"  for="name">Event Name <span style="color:red;">*</span></label>
                                                        <input type="text" id="name" class="form-control col-sm-8 float-right" name="name"
                                                            placeholder="name" autocomplete="off" value="{{ old('name',$name) }}" />
                                                            <h5><small class="text-danger" id="name_err"></small></h5>
                                                            @error('name')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" for="start_time" style="margin-top:5px">Start date <span style="color:red;">*</span></label>
                                                        <input type="date" id="start_time" class="form-control col-sm-8 float-right"
                                                            placeholder="Start date" name="start_time" autocomplete="off" value="{{ old('start_time',$start_time) }}" />
                                                            <h5><small class="text-danger" id="start_time_err"></small></h5>
                                                            @error('start_time')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>



                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" for="end_time" style="margin-top:10px">End date <span style="color:red;">*</span></label>
                                                        <input type="date" id="end_time" class="form-control col-sm-8 float-right"
                                                            placeholder="End date" name="end_time" autocomplete="off" value="{{ old('end_time',$end_time) }}" />
                                                            <h5><small class="text-danger" id="end_time_err"></small></h5>
                                                            @error('end_time')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>



                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" style="margin-top:20px"  for="city" >City <span style="color:red;">*</span></label>
                                                        <input type="text" id="city" class="form-control col-sm-8 float-right" name="city"
                                                            placeholder="city" autocomplete="off" value="{{ old('city',$city) }}" />
                                                            <h5><small class="text-danger" id="city_err"></small></h5>
                                                            @error('city')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left"  for="email" style="margin-top:20px">Country<span style="color:red;">*</span></label>
                                                        <input type="text" id="country" class="form-control col-sm-8 float-right" name="country"
                                                            placeholder="country" autocomplete="off" value="{{ old('country',$country) }}" />
                                                            <h5><small class="text-danger" id="country_err"></small></h5>
                                                            @error('country')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left"  for="email" style="margin-top:20px">State <span style="color:red;">*</span></label>
                                                        <input type="text" id="state" class="form-control col-sm-8 float-right" name="state"
                                                            placeholder="state" autocomplete="off" value="{{ old('state',$state) }}" />
                                                            <h5><small class="text-danger" id="state_err"></small></h5>
                                                            @error('state')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                               
                                            

                                            </div>
                                        </div>

                                      


                                                
                                            
                                                

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1" onClick="return validation()">Submit</button>
                                            <a href="{{ url('/event') }}" type="reset"
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

<!-- <script type="text/javascript"> 


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
                // village_options="<option value='0'>Select village</option>"
                // result.villages.forEach(element => {
                //     village_options+='<option value="' + element.id + '">' + element.village_name + '</option>';
                // });
                // $('#village').html(village_options);
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
      
        if ($('#username').val() == ""){
            $('#username').parent().addClass('has-error');
            $('#username_err').html('Please Enter Username.');
            $('#username').focus();
            $('#username').keyup(function () {
            $('#username').parent().removeClass('has-error');
            $('#username_err').html('');
            });
            return false;
        }
        
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

        if ($('#contact_number').val() == "") {
            $('#contact_number').parent().addClass('has-error');
            $('#contact_number_err').html('Please Enter Mobile Number.');
            $('#contact_number').focus();
            $('#contact_number').keyup(function() {
                $('#contact_number').parent().removeClass('has-error');
                $('#contact_number_err').html('');
            });
            return false;
        }else if($('#contact_number').val().length < 10) {
            $('#contact_number').parent().addClass('has-error');
            $('#contact_number_err').html('Please Enter Valid Mobile Number');
            $('#contact_number').focus();
            $('#contact_number').keyup(function() {
                $('#contact_number').parent().removeClass('has-error');
                $('#contact_number_err').html('');
            });
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
        
       

        if ($('#user_role').val() == ""){
            $('#user_role').parent().addClass('has-error');
            $('#user_role_err').html('Please Enter First Name.');
            $('#user_role').focus();
            $('#user_role').keyup(function () {
            $('#user_role').parent().removeClass('has-error');
            $('#user_role_err').html('');
            });
            return false;
        }
        if($('#user_role').val() == "2"){
            
            if ($('#country').val() == ""){
                $('#country').parent().addClass('has-error');
                $('#country_err').html('Please Select First Name.');
                $('#country').focus();
                $('#country').keyup(function () {
                $('#country').parent().removeClass('has-error');
                $('#country_err').html('');
                });
                return false;
            }
            if ($('#state').val() == ""){
                $('#state').parent().addClass('has-error');
                $('#state_err').html('Please Select State Name.');
                $('#state').focus();
                $('#state').keyup(function () {
                $('#state').parent().removeClass('has-error');
                $('#state_err').html('');
                });
                return false;
            }
            if ($('#district').val() == ""){
                $('#district').parent().addClass('has-error');
                $('#district_err').html('Please Select District Name.');
                $('#district').focus();
                $('#district').keyup(function () {
                $('#district').parent().removeClass('has-error');
                $('#district_err').html('');
                });
                return false;
            }
            if ($('#block').val() == ""){
                $('#block').parent().addClass('has-error');
                $('#block_err').html('Please Select Block Name.');
                $('#block').focus();
                $('#block').keyup(function () {
                $('#block').parent().removeClass('has-error');
                $('#block_err').html('');
                });
                return false;
            }
            if ($('#village').val() == ""){
                $('#village').parent().addClass('has-error');
                $('#village_err').html('Please Select Village Name.');
                $('#village').focus();
                $('#village').keyup(function () {
                $('#village').parent().removeClass('has-error');
                $('#village_err').html('');
                });
                return false;
            }
        }
       
      
    }
</script> -->
