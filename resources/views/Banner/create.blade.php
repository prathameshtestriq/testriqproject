<?php

if (!empty($edit_data)) {
    $id = $edit_data['id'];
    $banner_name = $edit_data['banner_name'];
    $banner_url = $edit_data['banner_url'];
    $banner_image = $edit_data['banner_image'];
    $start_time = date( 'd-m-Y',$edit_data['start_time']);
    $end_time = date( 'd-m-Y',$edit_data['end_time']);
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
<style>
    #imagePreview {
    max-width: 100%;
    height: auto;
}
</style>
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
                                                    placeholder="Enter Banner URL" name="banner_url"
                                                    value="{{ old('banner_url', $banner_url) }}"
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="banner_url_err"></small></h5>
                                                @error('banner_url')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <label for="banner_image">Banner Image <span style="color:red;">* <span style="color: #949090">(Allowed JPEG, JPG or PNG. Max file size of 10 MB 
                                                Preferred Resolution: 1400 x 360. )</span>    
                                            </label>
                                            <div class="row w-100">
                                                <div class="col-md-8 col-12">
                                                    <div class="form-group ">
                                                        <input type="file" id="banner_image_add" class="form-control"
                                                            placeholder="Enter Banner Url" name="banner_image"
                                                            value="{{ old('banner_image', $banner_image) }}"
                                                            autocomplete="off" accept="image/jpeg, image/png, image/jpg, image/gif" onchange="previewImage(this);previewcropImage(this); "  />
                                                        
                                                            <span class="error" id="event_banner_image_err" style="color:red;"></span>
                                                            @error('banner_image')
                                                                <span class="error" style="color:red;">{{ $message }}</span>
                                                            @enderror 
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-12">
                                                    <div class="form-group d-flex flex-column">
                                                    <!-- Image preview section -->
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="form-group ">
                                                                <button type="button" onclick="cropImage()" class="btn btn-warning mr-1">Crop</button>
                                                            </div>

                                                            <div id="imagePreview_banner">
                                                                <?php if(!empty($banner_image)){ ?>
                                                                    <a href="{{ asset('uploads/banner_image/' . $banner_image) }}" target="_blank">
                                                                        <img id="preview_banner" src="{{ asset('uploads/banner_image/' . $banner_image) }}" alt="Current Image" style="width: 50px;" >
                                                                    </a>
                                                                    <input type="hidden" name="hidden_banner_image" value="{{ old('banner_image', $banner_image) }}">
                                                                <?php } else { ?>
                                                                    <img id="preview_banner" class="preview-image" src="#" alt="Image Preview" style="display:none; width: 50px;">
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12"> 
                                            <div class="form-group">
                                                <label for="start_date">Start Date <span style="color:red;">*</span></label>
                                                <input type="date" id="start_date" class="form-control"
                                                    placeholder="Enter Start Date" name="start_date"
                                                    value="{{ old('start_date', $start_time ? \Carbon\Carbon::parse($start_time)->format('Y-m-d') : '') }}" 
                                                    autocomplete="off" onchange="setEndDateMin()" min="{{ date('Y-m-d') }}" />
                                                <h5><small class="text-danger" id="start_date_err"></small></h5>
                                                @error('start_date')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-12" id="crop_image_hide">
                                            <div class="py-2">
                                                <input type="hidden" name="cropped_image_data" id="croppedImageInput">
                                                <img id="imagePreview_crop" style="display:none; max-width:100%;" />
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

                                       <div class="col-sm-6 col-12">
                                        <label for="country">Country:</label>
                                        <select id="country" name="country" class="select2 form-control">
                                            <option value="">Select Country</option>
                                            @foreach($countries as $value)
                                                <option value="{{ $value->id }}" {{ old('country', $country) == $value->id ? 'selected' : '' }}>
                                                    {{ $value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <h5><small class="text-danger" id="country_err"></small></h5>
                                        @error('country')
                                            <span class="error" style="color:red;">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 col-12">
                                        <label for="state">State:</label>
                                        <select id="state" name="state" class="select2 form-control">
                                     <option value="">Select State</option>
                                     @if(!empty($states))
                                     @foreach($states as $s)
                                    <option value="{{ $s->id }}" {{ old('state', $state) == $s->id ? 'selected' : '' }}>
                                       {{ $s->name }}
                                    </option>
                                     @endforeach
                                       @endif
                                    </select>
                                     <h5><small class="text-danger" id="state_err"></small></h5>
                                       @error('state')
                                     <span class="error" style="color:red;">{{ $message }}</span>
                                    @enderror
                                </div>

                                 <div class="col-sm-6 col-12">
                                     <label for="city">City:</label>
                                     <select id="city" name="city" class="select2 form-control">
                                         <option value="">Select City</option>
                                         @if(!empty($cities))
                                             @foreach($cities as $c)
                                                 <option value="{{ $c->id }}" {{ old('city', $city) == $c->id ? 'selected' : '' }}>
                                                     {{ $c->name }}
                                                 </option>
                                             @endforeach
                                         @endif
                                     </select>
                                     <h5><small class="text-danger" id="city_err"></small></h5>
                                     @error('city')
                                         <span class="error" style="color:red;">{{ $message }}</span>
                                     @enderror
                                 </div>

                                 <div class="col-12 text-center mt-1">
                                     <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                     <a href="{{ url('/banner') }}" class="btn btn-outline-secondary">Cancel</a>
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
<!-- Include Cropper.js CSS and JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

<script>
    function previewImage(input) {
        var file = input.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('preview_banner');
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    let cropper;

    // Handle image preview and cropper initialization
    function previewcropImage(input) {
        const file = input.files[0];
        if (!file) {
            return; // If no file selected, exit the function
        }

        const reader = new FileReader();

        reader.onload = function(e) {
            const image = document.getElementById('imagePreview_crop');
            image.style.display = 'block';  // Make sure the preview is visible
            image.src = e.target.result;  // Set image source to the uploaded file

            // Initialize cropper after the image is loaded
            image.onload = function() {
                console.log("Image loaded. Initializing cropper...");

                // Destroy previous cropper instance if it exists
                if (cropper) {
                    cropper.destroy();
                }

                // Initialize the cropper
                cropper = new Cropper(image, {
                    aspectRatio: 1400 / 360,  // Set the aspect ratio to 1400x360
                    viewMode: 1,
                    responsive: true,
                    autoCropArea: 1.0,
                    movable: false,
                    zoomable: false,
                    scalable: false,
                    cropBoxResizable: false
                });
                
                console.log("Cropper initialized:", cropper);
            };
        };
        
        reader.readAsDataURL(file);  // Read the file and trigger onload event
    }

    // Crop the image and get the cropped data
    function cropImage() {
        if (!cropper) {
            alert('Please upload and preview an image first.');
            return;
        }

        const canvas = cropper.getCroppedCanvas({
            width: 1400,
            height: 360
        });

        if (!canvas) {
            alert('Could not create a cropped image. Make sure the image is loaded and try again.');
            return;
        }

        // Get the cropped image as a base64-encoded string
        const croppedImageDataUrl = canvas.toDataURL('image/jpeg');

        // Display the cropped image result in preview
        document.getElementById('preview_banner').src = croppedImageDataUrl;
        document.getElementById('imagePreview_crop').src = croppedImageDataUrl;

        // Set the cropped image data to a hidden input field to send with form submission
        document.getElementById('croppedImageInput').value = croppedImageDataUrl;
    }

    
</script>

<script>
     $(document).ready(function() {
        var CountryId = '<?php echo old('country', $country); ?>';
        var StateId = '<?php echo old('state', $state); ?>';
        var CityId = '<?php echo old('city', $city); ?>';
        var baseUrl = "{{ config('custom.app_url') }}";
        // alert(CountryId);
         //console.log("CountryId "+CountryId);
        // Fetch states based on the selected country
        if (CountryId !== '') {
            // alert("here");
            $.ajax({
                url: baseUrl +'/get_states', // Replace with your URL to fetch states
                type: 'GET',
                data: { country_id: CountryId },
                success: function(states) {
                    $('#state').empty().append('<option value="">Select State</option>');
                    $.each(states, function(key, value) {
                        $('#state').append('<option value="'+ value.id +'" '+ (StateId == value.id ? 'selected' : '') +'>'
                            + value.name +'</option>');
                    });

                    // Fetch cities based on the selected state
                    if (StateId !== '') {
                        $.ajax({
                            url: baseUrl +'/get_cities', // Replace with your URL to fetch cities
                            type: 'GET',
                            data: { state_id: StateId },
                            success: function(cities) {
                                $('#city').empty().append('<option value="">Select City</option>');
                                $.each(cities, function(key, value) {
                                    $('#city').append('<option value="'+ value.id +'" '+ (CityId == value.id ? 'selected' : '') +'>'
                                        + value.name +'</option>');
                                });
                            }
                        });
                    }
                }
            });
        }

        // Handle country change
        $('#country').change(function() {
            var countryId = $(this).val();
            $.ajax({
                url: baseUrl +'/get_states',
                type: 'GET',
                data: { country_id: countryId },
                success: function(states) {
                    $('#state').empty().append('<option value="">Select State</option>');
                    $.each(states, function(key, value) {
                        $('#state').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                    $('#city').empty().append('<option value="">Select City</option>'); // Clear cities
                }
            });
        });

        // Handle state change
        $('#state').change(function() {
            var stateId = $(this).val();
            $.ajax({
                url: baseUrl +'/get_cities',
                type: 'GET',
                data: { state_id: stateId },
                success: function(cities) {
                    $('#city').empty().append('<option value="">Select City</option>');
                    $.each(cities, function(key, value) {
                        $('#city').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                }
            });
        });
    });

</script>


<script>
    function setEndDateMin() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const startDate = startDateInput.value;

        if (startDate) {
            endDateInput.setAttribute('min', startDate);
            if (endDateInput.value && endDateInput.value < startDate) {
                endDateInput.value = '';
            }
        }
    }
</script>
  
   



