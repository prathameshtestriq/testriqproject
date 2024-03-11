@extends('layout.index')
@section('title', 'Users List')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Import Farmer Data</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-5 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Data</a>
                        </li>
                        <li class="breadcrumb-item active">Upload Farmer Data
                        </li>
                    </ol>
                </div>
            </div>
        </div>
		<div class="col-md-6">

		</div>
    </div>       
  </section>
    <section>
        <?php if(!empty($success)){ ?>
            <div class="demo-spacing-0 mb-1">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-body">
                        <i class="fa fa-check-circle" style="font-size:16px;" aria-hidden="true"></i>
						<?php echo $success; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        <?php } ?>
        @if ($message = Session::get('error'))
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
        <?php 
            if(!empty($error)){ ?>
                <div class="demo-spacing-0 mb-1">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="alert-body">
                            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                            <?php echo $error; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
         <?php   }
        ?>
        <div class="content-body">
            <section id="multiple-column-form">
                <div class="row justify-content-center">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
							<form class="form" id="uploadfrm" action="" method="post" enctype="multipart/form-data">
								<input type="hidden" name="form_type" value="download_data">
									{{ csrf_field() }}
						
								<div class="row">
									<div class="col-md-4 col-12">
                                            
									 	<div class="form-group">
											<label for="country-column">Select Country <span
											style="color:red;">*</span></label>
										  
											<select name="country" id="myDropdown" class="form-control form-select" >
												<option value="" >Select Country</option>
												<?php  
												foreach ($master_countries as $value)
												{ 
														$selected = '';
                                                        if(old('country') == $value->id){
                                                            $selected = 'selected';
                                                        }
													
													?>
												   
													<option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->country_name; ?></option>
													
													<?php 
												}
												?>

											 
											</select>
											@error('country_ids')
												<span class="text-danger">{{ $message }}</span>
											@enderror
											<h5><small class="text-danger" id="country_ids_err"></small></h5>
										</div>
									</div>

                                    <div class="col-md-4 col-12">
                                        
                                        <div class="form-group">
                                           <label for="brand-column">Select Brand <span
                                           style="color:red;">*</span></label>
                                         
                                           <select name="brand" id="brand_dropdown" class="form-control form-select" onchange="return get_program()">
                                               <option value="">Select Brand</option>
                                               <?php  
                                               foreach ($master_brand as $value)
                                               { 
                                                       $selected = '';
                                                       if(old('brand') == $value->id){
                                                           $selected = 'selected';
                                                       }
                                                   
                                                   ?>
                                                  
                                                   <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->brand_name; ?></option>
                                                   
                                                   <?php 
                                               }
                                               ?>
    
                                            
                                           </select>
                                         
                                           <h5><small class="text-danger" id="brand_ids_err"></small></h5>
                                       </div>
                                   </div>

                                    <div class="col-md-4 col-12">

                                        <div class="form-group">
                                           <label for="program-column">Select Program <span
                                           style="color:red;">*</span></label>
                                       
                                           <select name="program" id="program_dropdown" class="form-control form-select" onchange="return get_tabs()">
                                               <option value=" " >Select Program</option>
                                              
                                           </select>
                                           
                                           <h5><small class="text-danger" id="program_id_err"></small></h5>
                                       </div>
                                   </div>
                                   
							   <div class="col-md-4 col-12">   
                                <div class="form-group">
                                   <label for="tab-column">Select Tab <span
                                   style="color:red;">*</span></label>
                                 
                                   <select name="tab" id="tab_dropdown" class="form-control form-select" onchange="get_form();" >
                                       <option value=" " >Select Tab</option>
                                   </select>
                                 
                                   <h5><small class="text-danger" id="tab_err"></small></h5>
                               </div>
                           </div>

                            <div class="col-md-4 col-12">                                       
                                <div class="form-group">
                                <label for="form-column">Select Form <span
                                style="color:red;">*</span></label>
                                
                                <select name="form" id="form_dropdown" class="form-control form-select" >
                                    <option value="" >Select Form</option>
                                </select>
                                
                                <h5><small class="text-danger" id="form_ids_err"></small></h5>
                            </div>
                        </div>

                        <div class="col-md-3 col-12" style="margin-top:23px">
                            
                            <div class="form-group">
                                <button type="submit" id="uplodebtn" class="btn btn-primary" onClick="return validate()">Download</button>
                            </div>
                        </div>
                      
                    </div>
                {{-- </form> --}}
                {{-- <form class="form" id="uploadform" action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="form_type" value="upload_data"> --}}
                        {{ csrf_field() }}
            
                     <div class="col-md-3 col-12">
                            <div class="form-group">
                                <label for="data_file">Upload Document <span style="color:red;">*</span></label>
                                <input type="file" id="data_file" name="data_file" class="form-control ">
                                @if ($errors->has('data_file'))
                                    <span class="text-danger">{{ $errors->first('data_file') }}</span>
                                @endif
                                <h5><small class="text-danger" id="data_file_err"></small></h5>

                            </div>
                        </div>
                        <div class="col-md-1 col-12" style="margin-top:23px">
                            
                            <div class="form-group">
                                <button type="submit" id="uplodefile" class="btn btn-primary" onclick="return validate1(); ">Upload</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
        
    </div>
            <!-- Bordered table end -->
        </div>


    </section>
		</div>

@endsection

<script>
 
 function validate()
	 {
       
        if ($('#myDropdown').val() == ""){
            $('#myDropdown').parent().addClass('has-error');
            $('#country_ids_err').html('Please Select Country.');
            $('#myDropdown').focus();
            $('#myDropdown').keyup(function () {
            $('#myDropdown').parent().removeClass('has-error');
            $('#country_ids_err').html('');
            });
            return false;
        }
        if ($('#brand_dropdown').val() == ""){
            $('#brand_dropdown').parent().addClass('has-error');
            $('#brand_ids_err').html('Please Select Brand.');
            $('#brand_dropdown').focus();
            $('#brand_dropdown').keyup(function () {
            $('#brand_dropdown').parent().removeClass('has-error');
            $('#brand_ids_err').html('');
            });
            return false;
        }
       
        if ($('#program_dropdown').val() == ""){
            $('#program_dropdown').parent().addClass('has-error');
            $('#program_id_err').html('Please Select Program.');
            $('#program_dropdown').focus();
            $('#program_dropdown').keyup(function () {
            $('#program_dropdown').parent().removeClass('has-error');
            $('#program_id_err').html('');
            });
            return false;
        }
        
        // if ($('#tab_dropdown').val() == ""){
        //     $('#tab_dropdown').parent().addClass('has-error');
        //     $('#tab_err').html('Please Select Tab.');
        //     $('#tab_dropdown').focus();
        //     $('#tab_dropdown').keyup(function () {
        //     $('#tab_dropdown').parent().removeClass('has-error');
        //     $('#tab_err').html('');
        //     });
        //     return false;
        // }

        if ($('#form_dropdown').val() == ""){
            $('#form_dropdown').parent().addClass('has-error');
            $('#tab_err').html('Please Select Tab.');
            $('#tab_dropdown').focus();
           
            $('#form_ids_err').html('Please Select Form.');
            $('#form_dropdown').focus();
            $('#form_dropdown').keyup(function () {
            $('#form_dropdown').parent().removeClass('has-error');
            $('#form_ids_err').html('');
            });
            return false;
        }
        
       
	 }
    function validate1()
    {
        
        if ($('#data_file').val() == ""){
            $('#data_file').parent().addClass('has-error');
            $('#data_file_err').html('Please Select file.');
            $('#data_file').focus();
            $('#data_file').keyup(function () {
            $('#data_file').parent().removeClass('has-error');
            $('#data_file_err').html('');
            });
            return false;
        }
        var fileInput = document.getElementById('data_file');
        var filePath = fileInput.value;
        // Allowing file type
        var allowedExtensions =
            /(\.csv)$/i;

        if (!allowedExtensions.exec(filePath)) {
            // alert('Invalid file type');
             $('#data_file_err').html('Please Select CSV file.');
           
            fileInput.value = '';
            return false;
        }
       
       validate();
        if ($('#form_dropdown').val() == ""){
            $('#form_dropdown').parent().addClass('has-error');
            $('#tab_err').html('Please Select Tab.');
           
            $('#form_ids_err').html('Please Select Form.');
            $('#form_dropdown').focus();
            $('#form_dropdown').keyup(function () {
            $('#form_dropdown').parent().removeClass('has-error');
            $('#form_ids_err').html('');
            });
            return false;
        }
        
        
    }
   
function get_program(){
//   alert('here');
        var brand_id=$('#brand_dropdown').val();
        var url='';
        $.ajax({
        url: "<?php echo url('fetch_program'); ?>",
        type: 'post',
        data: {
            _token: "{{ csrf_token() }}",
            brand_id:brand_id,
        },
        success: function(result) {             
            if(result != [])
            {
                var options = '';   
                options += '<option value="">Select Program</option>';
                for (var i = 0; i < result.length; i++) {  
                    options += '<option value="' + result[i]['program_id'] + '">' + result[i]['program_name'] + '</option>';  
                }  
                
            }
            $('#program_dropdown').html(options);
        },
        error: function(jqXHR, testStatus, error) {
            // console.log(error);
            alert("Page " + url + " cannot open. Error:" + error);
            $('#loader').hide();
        },
        });
     }

     function get_tabs(){

        var program_id=$('#program_dropdown').val();

        var url='';
        $.ajax({
        url: "<?php echo url('fetch_tab'); ?>",
        type: 'post',
        data: {
            _token: "{{ csrf_token() }}",
            program_id:program_id,
        },
        success: function(tab_result) {   
            // console.log()    
            var options1 = '';      
            if(tab_result != [])
            {
                
                options1 += '<option value=" ">Select Tab</option>';
                for (var i = 0; i < tab_result.length; i++) {  
                    options1 += '<option value="' + tab_result[i]['tab_id'] + '">' + tab_result[i]['tab_title'] + '</option>';  
                }  
                
            }   $('#tab_dropdown').html(options1);
        },
        error: function(jqXHR, testStatus, error) {
            // console.log(error);
            alert("Page " + url + " cannot open. Error:" + error);
            $('#loader').hide();
        },
        });
     }

     function get_form(){
    
        var program_id=$('#program_dropdown').val();
        var tab_id=$('#tab_dropdown').val();

        var url='';
        $.ajax({
        url: "<?php echo url('fetch_forms'); ?>",
        type: 'post',
        data: {
            _token: "{{ csrf_token() }}",
            program_id:program_id,
            tab_id:tab_id,
        },
        success: function(form_result) {   
            console.log(form_result)          
            if(form_result != [])
            {
                var options2 = '';
                options2 += '<option value="">Select form</option>';
                for (var i = 0; i < form_result.length; i++) {  
                    options2 += '<option value="' + form_result[i]['form_id'] + '">' + form_result[i]['form_title'] + '</option>';  
                }  
                
            }   $('#form_dropdown').html(options2);
        },
        error: function(jqXHR, testStatus, error) {
            // console.log(error);
            alert("Page " + url + " cannot open. Error:" + error);
            $('#loader').hide();
        },
        });
    }

</script>
