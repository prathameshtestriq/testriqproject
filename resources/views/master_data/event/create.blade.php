@extends('layout.index')
@if (!empty($id))
    @section('title', ' Event ')
@else
    @section('title', ' Event ')
@endif

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

@section('title', 'Category Create')
<!-- Dashboard Ecommerce start -->
@section('content')
    <section>
        <style>
            .ck-editor__editable {
                min-height: 200px; /* Set the minimum height as needed */
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
                                                Edit Event Details
                                            @else
                                                Add Event Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Event</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($id))
                                                Edit Event Details
                                            @else
                                                Add Event Details
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
                                <form class="form" action="" method="post"  enctype="multipart/form-data">
                                    <input type="hidden" name="form_type" value="add_edit_event">
                                {{ csrf_field() }}

                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="name">Event Name <span style="color:red;">*</span></label>
                                                <input type="text" id="name" class="form-control"
                                                    placeholder=" Event Name" name="name" value="{{ old('name', $name) }}" 
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="name_err"></small></h5>
                                                @error('name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="address">Event Address <span style="color:red;">*</span></label>
                                                <input type="text" id="address" class="form-control" placeholder="Event Address"
                                                    name="address"  value="{{ old('address', $address) }}"  autocomplete="off" />
                                                <h5><small class="text-danger" id="address_err"></small></h5>
                                                @error('address')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="start_time">Start Date <span style="color:red;">*</span></label>
                                                <input type="date" id="start_time" class="form-control"
                                                    placeholder="Start Date" name="start_time"
                                                    value="{{ old('start_time', $start_time ? \Carbon\Carbon::parse($start_time)->format('Y-m-d') : '') }}"
                                                    autocomplete="off" onkeydown="return false;" onchange="setEndDateMin()"/>
                                                <h5><small class="text-danger" id="start_time_err"></small></h5>
                                                @error('start_time')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="end_time">End Date <span style="color:red;">*</span></label>
                                                <input type="date" id="end_time" class="form-control"
                                                    placeholder="End Date" name="end_time"
                                                    value="{{ old('end_time', $end_time ? \Carbon\Carbon::parse($end_time)->format('Y-m-d') : '') }}" 
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="end_time_err"></small></h5>
                                                @error('end_time')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="time_zone">Timezone <span style="color:red;">*</span></label>
                                                <select id="time_zone" name="time_zone" class="select2 form-control">
                                                    <option value="">-- Select Timezone --</option>
                                                @foreach($timezones_array as $res)
                                                <option value="{{ $res->id }}" {{ old('time_zone', $time_zone)==$res->id
                                                    ? 'selected' : '' }}>
                                                    {{ $res->area }}
                                                </option>
                                                @endforeach
                                                </select>
                                                    <h5><small class="text-danger" id="time_zone_err"></small></h5>
                                                @error('time_zone')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-sm-6 col-12">
                                            <label for="country">Country: <span style="color:red;">*</span></label>
                                            <select id="country" name="country" class="select2 form-control">
                                                <option value="">All country</option>
                                                <?php  
                                                foreach ($countries as $value)
                                                {  
                                                    $selected = '';
                                                    if(old('country', $country) == $value->id){
                                                        $selected = 'selected';
                                                    }
                                                    ?>
                                                    <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->name; ?></option>
                                                    <?php 
                                                }
                                                ?>
                                            </select>
                                            <h5><small class="text-danger" id="country_err"></small></h5>
                                                @error('country')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                        </div>
                                        <div class="col-sm-6 col-12">
                                            <label for="state">State: <span style="color:red;">*</span></label>
                                            <select id="state" name="state" class="select2 form-control">
                                                <option value="" class="placeholder">All state</option>
                                            </select>  
                                            <h5><small class="text-danger" id="state_err"></small></h5>
                                                @error('state')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                        </div>

                                        <div class="col-sm-6 col-12">
                                            <label for="city">City: <span style="color:red;">*</span></label>
                                            <select id="city" name="city" class="select2 form-control">
                                                <option value="">All City</option>
                                            </select>  
                                            <h5><small class="text-danger" id="city_err"></small></h5>
                                                @error('city')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="event_url">Event URL  <span style="color:red;">*</span></label>
                                                <input type="text" id="event_url" class="form-control" placeholder="Event Address"
                                                    name="event_url"  value="{{ old('event_url', $event_url) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="event_url_err"></small></h5>
                                                @error('event_url')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                      
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="state"> Category </label>
                                                <select class="form-control form-select select2" multiple
                                                style="min-width: 400px;" id="categorySelect" name="category_id[]">
                                                @foreach($Category as $category)
                                                <option value="{{ $category->category_id }}" {{ $category->selected ?
                                                    'selected' : '' }}>
                                                    {{ $category->category_name }}</option>
                                                @endforeach
                                            </select>
                                                    <h5><small class="text-danger" id="category_id_err"></small></h5>
                                                @error('category_id')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="event_keywords">Event Keywords/Metatags <span style="color:red;">*</span></label>
                                                {{-- <input type="text" id="event_keywords" class="form-control" placeholder="Event Keywords"
                                                    name="event_keywords"    value="{{ old('event_keywords', $event_keywords) }}"  autocomplete="off" /> --}}
                                                    <textarea name="event_keywords"   id="event_keywords" value="{{ old('event_keywords') }}" class="form-control" cols="1" rows="1">{{ $event_keywords }}</textarea>   
                                                <h5><small class="text-danger" id="event_keywords_err"></small></h5>
                                                @error('event_keywords')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="event_description">Event Description <span style="color:red;">*</span></label>
                                                <textarea id="event_description" class="form-control" placeholder="Event Description"
                                                name="event_description" autocomplete="off">{{ old('event_description', $event_description) }}</textarea>
                                                <h5><small class="text-danger" id="event_description_err"></small></h5>
                                                @error('event_description')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <label for="img"><b>Event Images</b> <span style="color:red;"><br/></label>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="event_banner_image">Event Banner  <span style="color:red;">*</span>
                                                <span style="color: #949090">(In jpg, jpeg, png formats. Max upto 5MB.   
                                                    Dimensions- 1920 px x 744 px)</span>  
                                                </label>
                                                <input type="file" id="event_banner_image" class="form-control"
                                                    placeholder="Event Banner Image" name="event_banner_image"
                                                    autocomplete="off" accept="image/jpeg, image/png" onchange="previewImage(this);" />
                                                    
                                                 <span class="error" id="event_banner_image_err" style="color:red;"></span>

                                                @error('event_banner_image')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-2 col-12 mt-2">
                                            <span><br /></span>
                                            <!-- Image preview section -->
                                            <div id="imagePreview">
                                                @php
                                                    $imagePath = public_path('uploads/banner_image/' . $banner_image);
                                                @endphp
                                                {{-- <?php if(file_exists($imagePath) && !empty($banner_image)){ ?> --}}
                                                    <?php if(!empty($banner_image)){ ?>
                                                        <a href="{{ asset('uploads/banner_image/' . $banner_image) }}" target="_blank">
                                                            <img id="preview" src="{{ asset('uploads/banner_image/' . $banner_image) }}" alt="Current Image" style="width: 50px;">
                                                        </a>
                                                        <input type="hidden" name="hidden_image" value="{{ old('event_banner_image/', $banner_image) }}" accept="image/jpeg, image/png">
                                                    <?php } else { ?>
                                                        <img id="preview" class="preview-image" src="#" alt="Image Preview" style="display:none; width: 50px;">
                                                    <?php } ?>
                                                {{-- <?php } else { echo '-'; } ?>     --}}
                                            </div>    
                                        </div>
                                     
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="event_communication_creatives">Event Communication Creatives 
                                                <span style="color: #949090">(In jpg, jpeg, png formats. Max upto 5MB.   
                                                    Dimensions- 1920 px x 744 px)</span>  
                                                </label>
                                                <input type="file" id="event_communication_creatives" class="form-control"
                                                placeholder="Event Banner Image" name="event_communication_creatives[]"
                                                autocomplete="off" accept="image/jpeg, image/png"
                                                onchange="eventpreviewImages(this); eventvalidateSize(this);" multiple />
                                                 <span class="error" id="event_communication_creatives_err" style="color:red;"></span>

                                                @error('event_communication_creatives')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                       <div class="col-md-2 "></div>
                                       <div class="col-md-6 "></div>
                                       
                                        <div class="col-md-6 col-12">
                                            <span><br /></span>
                                            <!-- Image preview section --> 
                                            <div id="event_imagePreview" style="display: flex; flex-wrap: wrap; gap: 30px;">
                                                <?php foreach($event_images as $image){ ?>
                                                <?php if(!empty($image)){ ?>
                                                    <div class="image-wrapper" style="position: relative; display: inline-block;">
                                                        <a href="{{ asset('uploads/event_images/' . $image->image) }}" target="_blank">
                                                            <img id="event_preview" src="{{ asset('uploads/event_images/' . $image->image) }}" alt="Current Image" style="width: 50px;">
                                                        </a>
                                                        <input type="hidden" name="hidden_image[]" value="{{ old('event_communication_creatives', $image->image) }}" accept="image/jpeg, image/png">
                                        
                                                        <!-- Cross icon for removing image -->
                                                        <span title="Delete Image" class="remove-image" style="position: absolute; top: -10px; right: -20px; background: rgb(252, 251, 251); color: rgb(221, 19, 19); cursor: pointer; padding: 4px; border-radius: 20%;" onclick="remove_event_image({{ $image->id }} ,{{ $id }})" >
                                                            &#10006;
                                                        </span>
                                                    </div>
                                                <?php } else { ?>
                                                    <img id="event_preview" class="preview-image" src="#" alt="Image Preview" style="display:none; width: 50px;">
                                                <?php } } ?>
                                            </div>
                                        </div>
                                    

                                        
                                        <div class="col-12 text-center mt-4">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
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
{{-- <script src="https://cdn.ckeditor.com/ckeditor5/38.1.1/classic/ckeditor.js"></script> --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src={{ asset('/app-assets/js/scripts/Ckeditor/ckeditor.js') }}></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        ClassicEditor
        .create(document.querySelector('#event_description'), {
            ckfinder: {
                uploadUrl: '{{ route('ckeditor_event_description.upload').'?_token='.csrf_token() }}'
            }
        })
        .catch(error => {
            console.error('Error initializing CKEditor:', error);
        });
      
    });
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

    function previewImage(input) {
        var file = input.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }


    function eventpreviewImages(input) {
        var previewContainer = document.getElementById('event_imagePreview');
        
        // Append new images to the existing images in the container
        if (input.files) {
            for (var i = 0; i < input.files.length; i++) {
                var file = input.files[i];
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    var imgElement = document.createElement('img');
                    imgElement.src = e.target.result;
                    imgElement.style.width = '50px';
                    imgElement.alt = 'Image Preview';
                    
                    var anchorElement = document.createElement('a');
                    anchorElement.href = e.target.result;
                    anchorElement.target = '_blank';
                    anchorElement.appendChild(imgElement);
                    
                    previewContainer.appendChild(anchorElement);
                };
                
                reader.readAsDataURL(file);
            }
        }
    }


    function eventvalidateSize(input) {
        var maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
        var allowedFormats = ['image/jpeg', 'image/png']; // Allowed MIME types
        var errorElement = document.getElementById('event_communication_creatives_err');
        
        for (var i = 0; i < input.files.length; i++) {
            var file = input.files[i];
            
            // Check file format
            if (!allowedFormats.includes(file.type)) {
                errorElement.innerText = "Invalid file format. Only jpg, jpeg, and png are allowed.";
                input.value = ''; // Clear the input
                return;
            }
            
            // Check file size
            if (file.size > maxFileSize) {
                errorElement.innerText = "The image must be 5MB or belows.";
                input.value = ''; // Clear the input
                return;
            }
        }
        
        // Clear the error message if everything is fine
        errorElement.innerText = '';
    }
  
    function remove_event_image(iId,event_id) {
        // alert(event_id);
        var url = '<?php echo url('/event/remove_event_image'); ?>';

        url = url + '/' + event_id + '/' + iId;
        // alert(url);
        Confirmation = confirm('Are you sure you want to remove this record ?');
        if (Confirmation) {

            window.location.href = url;

        }
    } 


</script>
<script>
    function setEndDateMin() {
        const startDateInput = document.getElementById('start_time');
        const endDateInput = document.getElementById('end_time');
        const startDate = startDateInput.value;

        if (startDate) {
            endDateInput.setAttribute('min', startDate);
            if (endDateInput.value && endDateInput.value < startDate) {
                endDateInput.value = '';
            }
        }
    }
</script>
