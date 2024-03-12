@extends('layout.index')
@section('title', 'Farmer Season')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Import Farmer Season</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-5 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Farmer Season</a>
                        </li>
                        <li class="breadcrumb-item active">Upload Farmer Season
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
                        {!! $message !!}
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
							<form class="form" id="uploadfrmseason" action="{{ url('upload_farmer_season') }}" method="post" enctype="multipart/form-data">
								<input type="hidden" name="form_type" value="upload_farmers_season">
									{{ csrf_field() }}
						
								<div class="row">
                                    <div class="col-md-3 col-12">       
                                        <div class="form-group">
                                            <label for="season-column">Select Season <span
                                            style="color:red;">*</span></label>
                                            
                                            <select name="season" id="season_dropdown" class="form-control form-select" onchange="return get_program()">
                                                <option value="">Select season</option>
                                                <?php  
                                                    
                                                    foreach ($master_seasons as $value)
                                                    { 
                                                            $selected = '';
                                                            if(old('season') == $value->season){
                                                                $selected = 'selected';
                                                            }
                                                        
                                                        ?>
                                                        
                                                        <option value="<?php echo $value->season; ?>" <?php echo $selected; ?>><?php echo $value->season; ?></option>
                                                        
                                                        <?php 
                                                    }
                                                    ?>
                                            </select>                                  
                                            <h5><small class="text-danger" id="season_ids_err"></small></h5>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-12">                                          
                                        <div class="form-group">
                                            <label for="country-column">Select Country <span
                                            style="color:red;">*</span></label>
                                            
                                            <select name="country" id="country_dropdown" class="form-control form-select" >
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
                                            @error('country_dropdown')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <h5><small class="text-danger" id="country_dropdown_err"></small></h5>
                                        </div>
                                    </div>

                                   
                                   
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label for="farmer_season_file">Upload Document <span style="color:red;">*</span></label>
                                            <input type="file" id="farmer_season_file" name="farmer_season_file" class="form-control ">
                                            @if ($errors->has('farmer_season_file'))
                                                <span class="text-danger">{{ $errors->first('farmer_season_file') }}</span>
                                            @endif
                                            <h5><small class="text-danger" id="farmer_season_file_err"></small></h5>

                                        </div>
                                    </div>
                                </div>    
                                <div class="col-md-1 col-12" style="margin-top:23px">									
                                    <div class="form-group">
                                        <button type="submit" id="uplodebtn" class="btn btn-primary" onClick="return validate()">Upload</button>
                                    </div>
                                </div>
                               
							</form>
                            <div class="col-md-12 col-12" style="margin-top:5px; ">										
                                <div class="form-group float-lg-right mr-1" >
                                   <button  type="submit" id="downloadbtn" class="btn btn-danger "><a href="{{ url('download/farmer_season_sample') }}" class="text-white">Download </a></button> 
                                </div>                           
                            </div>
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
        if ($('#season_dropdown').val() == ""){
            $('#season_dropdown').parent().addClass('has-error');
            $('#season_ids_err').html('Please Select Season.');
            $('#season_dropdown').focus();
            $('#season_dropdown').keyup(function () {
            $('#season_dropdown').parent().removeClass('has-error');
            $('#season_ids_err').html('');
            });
            return false;
        }

       
        if ($('#country_dropdown').val() == ""){
            $('#country_dropdown').parent().addClass('has-error');
            $('#country_dropdown_err').html('Please Select Country.');
            $('#country_dropdown').focus();
            $('#country_dropdown').keyup(function () {
            $('#country_dropdown').parent().removeClass('has-error');
            $('#country_dropdown_err').html('');
            });
            return false;
        }
       
        
        if ($('#farmer_season_file').val() == ""){
            $('#farmer_season_file').parent().addClass('has-error');
            $('#farmer_season_file_err').html('Please Select file.');
            $('#farmer_season_file').focus();
            $('#farmer_season_file').keyup(function () {
            $('#farmer_season_file').parent().removeClass('has-error');
            $('#farmer_season_file_err').html('');
            });
            return false;
        }

        var fileInput = document.getElementById('farmer_season_file');
        var filePath = fileInput.value;
        // Allowing file type
        var allowedExtensions =
            /(\.csv)$/i;

        if (!allowedExtensions.exec(filePath)) {
            // alert('Invalid file type');
             $('#farmer_season_file_err').html('Please Select CSV file.');
           
            fileInput.value = '';
            return false;
        }
        
	 }

     
</script>
