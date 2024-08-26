<?php

if (!empty($edit_data)) {
    $id = $edit_data['id'];
    $banner_name = $edit_data['banner_name'];
    $banner_url = $edit_data['banner_url'];
    $banner_image = $edit_data['banner_image'];
    $start_time = $edit_data['start_time'];
    $end_time = $edit_data['end_time'];
    $country = $edit_data['country'];
    $state = $edit_data['state'];
    $city = $edit_data['city'];
    $active = $edit_data['active'];
} else {
    $id = '';
    $banner_name = '';
    $banner_url = '';
    $banner_image = '';
    $start_time = '';
    $end_time = '';
    $country = '';
    $state = '';
    $city = '';
    $active = '';
}

?>
@extends('layout.index')
@if (!empty($id))
    @section('title', 'Banner ')
@else
    @section('title', ' Banner ')
@endif

@section('title', 'Category Create')
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
                                                Edit Banner Details
                                            @else
                                                Add Banner Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Banner</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($id))
                                                Edit Banner 
                                            @else
                                                Add Banner 
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
                                <form class="form" action="" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="form_type" value="add_edit_banner">
                                    {{ csrf_field() }}
    

                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="name">Banner Name <span
                                                        style="color:red;">*</span></label>
                                                <input type="text" id="banner_name" class="form-control"
                                                    placeholder="Enter Banner Name" name="banner_name"
                                                    value="{{ old('banner_name', $banner_name) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="banner_name_err"></small></h5>
                                                @error('banner_name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="banner_url">Banner URL <span style="color:red;">*</span></label>
                                                <input type="text" id="banner_url" class="form-control"
                                                    placeholder="Enter Banner Url" name="banner_url"
                                                    value="{{ old('banner_url', $banner_url) }}"
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="banner_url_err"></small></h5>
                                                @error('banner_url')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="banner_image">Banner Image <span style="color:red;">*</span>
                                                    <span style="color: #949090">(Allowed JPEG, JPG or PNG. Max file size of 2 MB)</span>  
                                                </label>
                                                <input type="file" id="banner_image" class="form-control"
                                                    placeholder="Enter Banner Url" name="banner_image"
                                                    value="{{ old('banner_image', $banner_image) }}"
                                                    autocomplete="off" onchange="validateSize(this)"  />
                                                   
                                                    <span class="error" id="banner_image_err" style="color:red;"></span>
                                                    @error('banner_image')
                                                        <span class="error" style="color:red;">{{ $message }}</span>
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
                                                <label for="start_date">Start Date <span style="color:red;">*</span></label>
                                                <input type="date" id="start_date" class="form-control"
                                                    placeholder="Enter Start Date" name="start_date"
                                                    value="{{ old('start_date', $start_time ? \Carbon\Carbon::parse($start_time)->format('Y-m-d') : '') }}" 
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="start_date_err"></small></h5>
                                                @error('start_date')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="end_date">End Date <span style="color:red;">*</span></label>
                                                <input type="date" id="end_date" class="form-control"
                                                    placeholder="Enter End Date" name="end_date"
                                                    value="{{ old('end_date', $end_time ? \Carbon\Carbon::parse($end_time)->format('Y-m-d') : '') }}"  
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="end_date_err"></small></h5>
                                                @error('end_date')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="country">Country <span style="color:red;">*</span></label>
                                                <select id="country" name="country" class="select2 form-control">
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
                                                <h5><small class="text-danger" id="country_err"></small></h5>
                                                @error('country')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="state">State <span style="color:red;">*</span></label>
                                                <select id="state" name="state" class="select2 form-control">
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
                                                <h5><small class="text-danger" id="state_err"></small></h5>
                                                @error('state')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div> 
                                        </div>
                                      
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="city">City <span style="color:red;">*</span></label>
                                                <select id="city" name="city" class="select2 form-control">
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
                                                <h5><small class="text-danger" id="city_err"></small></h5>
                                                @error('city')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/banner') }}" type="reset"
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
  
    function validateSize(input) {
        var isValid = true;
      const fileSize = input.files[0].size / 1024 / 1024; // in 2 MB
      var banner_image = $('#banner_image').val().trim();
 
    
      if(fileSize > 2) {
         // alert('File size exceeds 2 MB');
         if (banner_image !== "") {
            // alert("here");
            $('#banner_image').parent().addClass('has-error');
            $('#banner_image_err').html('The image must be 2MB or below.');
            $('#banner_image').focus();
            $('#banner_image').keyup(function() {
                $('#banner_image').parent().removeClass('has-error');
                $('#banner_image_err').html('');
            });
            isValid = false;
        }

        return isValid;
      }
      
   }
   

</script>

