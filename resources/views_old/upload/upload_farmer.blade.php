@extends('layout.index')
@section('title', 'Users List')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Import Farmers</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-5 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Farmer</a>
                        </li>
                        <li class="breadcrumb-item active">Upload Farmer
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
        @if ($message = Session::get('success'))
            <div class="demo-spacing-0 mb-1">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-body">
                        <i class="fa fa-check-circle" style="font-size:16px;" aria-hidden="true"></i>
						{!! $message !!}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @elseif ($message = Session::get('error'))
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
 
        <div class="content-body">
            <section id="multiple-column-form">
                <div class="row justify-content-center">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
							<form class="form" id="uploadfrm" action="import_farmers" method="post" enctype="multipart/form-data">
								<input type="hidden" name="form_type" value="upload_farmers">
									{{ csrf_field() }}
						
								<div class="row">
									<div class="col-md-3 col-12">
                                            
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
                                    <div class="col-md-3 col-12">
                                        
                                        <div class="form-group">
                                           <label for="program-column">Select Program <span
                                           style="color:red;">*</span></label>
                                         
                                           <select name="program" id="program_dropdown" class="form-control form-select" >
                                               <option value="" >Select Program</option>
                                               <?php  
                                               foreach ($master_program as $value)
                                               { 
                                                       $selected = '';
                                                       if(old('program') == $value->id){
                                                           $selected = 'selected';
                                                       }
                                                   
                                                   ?>
                                                  
                                                   <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->program_name; ?></option>
                                                   
                                                   <?php 
                                               }
                                               ?>

                                            
                                           </select>
                                           
                                           <h5><small class="text-danger" id="program_ids_err"></small></h5>
                                       </div>
                                   </div>
                                   <div class="col-md-3 col-12">   
                                    <div class="form-group">
                                       <label for="brand-column">Select Brand <span
                                       style="color:red;">*</span></label>
                                     
                                       <select name="brand" id="brand_dropdown" class="form-control form-select" >
                                           <option value="" >Select Brand</option>
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
                               
									<div class="col-md-3 col-12">
										<div class="form-group">
											<label for="farmer_file">Upload Document <span style="color:red;">*</span></label>
											<input type="file" id="farmer_file" name="farmer_file" class="form-control ">
											@if ($errors->has('farmer_file'))
												<span class="text-danger">{{ $errors->first('farmer_file') }}</span>
											@endif
											<h5><small class="text-danger" id="farmer_file_err"></small></h5>

										</div>
									</div>
									
                                    <div class="col-md-6 col-12" style="margin-top:23px">
										
										<div class="form-group">
											<button type="submit" id="uplodebtn" class="btn btn-primary" onClick="return validate()">Upload</button>
										</div>
                                       
									</div>
                                    <div class="col-md-6 col-12" style="margin-top:23px; ">
										
										<div class="form-group float-lg-right mr-1" >
                                          <a href="{{ url('download/farmer_sample') }}" class="btn btn-danger text-white">Download </a>
										</div>
                                       
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
        if ($('#program_dropdown').val() == ""){
            $('#program_dropdown').parent().addClass('has-error');
            $('#program_ids_err').html('Please Select Program.');
            $('#program_dropdown').focus();
            $('#program_dropdown').keyup(function () {
            $('#program_dropdown').parent().removeClass('has-error');
            $('#program_ids_err').html('');
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

        if ($('#farmer_file').val() == ""){
            $('#farmer_file').parent().addClass('has-error');
            $('#farmer_file_err').html('Please Select file.');
            $('#farmer_file').focus();
            $('#farmer_file').keyup(function () {
            $('#farmer_file').parent().removeClass('has-error');
            $('#farmer_file_err').html('');
            });
            return false;
        }

        var fileInput = document.getElementById('farmer_file');
        var filePath = fileInput.value;
        // Allowing file type
        var allowedExtensions =
            /(\.csv)$/i;

        if (!allowedExtensions.exec(filePath)) {
            // alert('Invalid file type');
             $('#farmer_file_err').html('Please Select CSV file.');
           
            fileInput.value = '';
            return false;
        }
	 }
</script>
