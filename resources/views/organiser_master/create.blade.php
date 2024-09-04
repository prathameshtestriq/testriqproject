@extends('layout.index')
@if (!empty($id))
    @section('title', ' Organiser Master')
@else
    @section('title', 'Organiser Master')
@endif

@section('title', 'Edit Organiser Master  ')
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
                                            Add Organiser Master  
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Organiser Master   </li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Add Organiser Master      
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
                                    <input type="hidden" name="form_type" value="add_edit_organiser">
                                    {{ csrf_field() }}
                                   
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="organiser_name">Oraniser Name<span style="color:red;">*</span></label>
                                                <input type="text" id="organiser_name" class="form-control"
                                                    placeholder="Enter Oraniser Name" name="organiser_name" value="{{ old('organiser_name', $name) }}"   autocomplete="off" pattern="[A-Za-z\s]*" 
                                                    oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');" />
                                                <h5><small class="text-danger" id="organiser_name_err"></small></h5>
                                                @error('organiser_name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="email">Email <span style="color:red;">*</span></label>
                                                <input type="text" id="email" class="form-control" name="email" value="{{ old('email', $email) }}"
                                                    placeholder="Enter Email" autocomplete="off" value="{{ old('email') }}" />
                                                <h5><small class="text-danger" id="email_err"></small></h5>
                                                @error('email')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="contact_number">Contact Number <span style="color:red;">*</span></label>
                                                <input type="text" id="contact_number" class="form-control" name="contact_number" value="{{ old('contact_number', $mobile) }}"
                                                    placeholder="Enter Contact Number" autocomplete="off" value="{{ old('contact_number') }}"
                                                    inputmode="numeric" pattern="\d*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" />
                                                <h5><small class="text-danger" id="contact_number_err"></small></h5>
                                                @error('contact_number')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-12 mt-2">
                                            <div class="form-group">
                                                <label for="about">About <span
                                                        style="color:red;">*</span></label>
                                                 <textarea name="about" id="about"  class="form-control" cols="30" rows="10">{{ old('about',$about) }}</textarea>   
                                                <h5><small class="text-danger" id="about_err"></small></h5>
                                                @error('about')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="contact_person_name">Contact Person Name</label>
                                                <input type="text" id="contact_person_name" class="form-control" value="{{ old('contact_person_name', $contact_person) }}"
                                                    placeholder="Enter Contact Person Name" name="contact_person_name" autocomplete="off" pattern="[A-Za-z\s]*" 
                                                    oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');" />
                                                <h5><small class="text-danger" id="contact_person_name_err"></small></h5>
                                                @error('contact_person_name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="contact_person_contact">Contact person contact </label>
                                                <input type="text" id="contact_person_contact" class="form-control" name="contact_person_contact"
                                                    placeholder="Enter Contact person contact" autocomplete="off" value="{{ old('contact_person_contact',$contact_no) }}"
                                                    inputmode="numeric" pattern="\d*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" />
                                                <h5><small class="text-danger" id="contact_person_contact_err"></small></h5>
                                                @error('contact_person_contact')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-md-12 col-12">
                                            <div class="form-group col-md-3">
                                                <div class="custom-control custom-switch custom-switch-success">
                                                    <input type="checkbox" class="custom-control-input" id="gstCheckbox"  value= 1  {{ old('gst', $gst) == 1 ? 'checked' : '' }}  name="gst"   onclick="checkedgst();"/>
                                                    <label class="custom-control-label" for="gstCheckbox"> GST
                                                        <span class="switch-icon-left"></span>
                                                        <span class="switch-icon-right"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- GST Number Field -->
                                        <div class="col-md-6 col-12 gst-dependent" style="display: none;">
                                            <div class="form-group">
                                                <label for="gst_number">GST Number <span style="color:red;">*</span></label>
                                                <input type="text" id="gst_number" class="form-control" name="gst_number"
                                                    placeholder="Enter GST Number" autocomplete="off" value="{{ old('gst_number',$gst_number) }}"
                                                    inputmode="numeric" />
                                                <h5><small class="text-danger" id="gst_number_err"></small></h5>
                                                @error('gst_number')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- GST Percentage Field -->
                                        <div class="col-md-6 col-12 gst-dependent" style="display: none;">
                                            <div class="form-group">
                                                <label for="contact_gst_percentage">GST Percentage <span style="color:red;">*</span></label>
                                                <input type="text" id="contact_gst_percentage" class="form-control" name="contact_gst_percentage"
                                                    placeholder="Enter GST Percentage" autocomplete="off" value="{{ old('contact_gst_percentage',$gst_percentage) }}"
                                                    inputmode="numeric" pattern="\d*"  value="{{ old('contact_gst_percentage',$gst_number) }}" />
                                                <h5><small class="text-danger" id="contact_gst_percentage_err"></small></h5>
                                                @error('contact_gst_percentage')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="organiser_logo_image">Organiser Logo Image <span style="color:red;">*</span>
                                                    <span style="color: #949090">(In jpg, jpeg, png formats. Max upto 5MB.
                                                        Dimensions- 1920 px x 744 px)</span>  
                                                </label>
                                                <input type="file" id="organiser_logo_image" class="form-control"
                                                    placeholder="Enter Organiser Logo Image " name="organiser_logo_image"
                                                    value="{{ old('organiser_logo_image',$logo_image) }}"
                                                    autocomplete="off" accept="image/jpeg, image/png" onchange="previewLogoImage(this); LogovalidateSize(this);"  />
                                                   
                                                    <span class="error" id="organiser_logo_image_err" style="color:red;"></span>
                                                    @error('organiser_logo_image')
                                                        <span class="error" style="color:red;">{{ $message }}</span>
                                                    @enderror 
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-2 col-12"></div> --}}
                                        <div class="col-md-2 col-12">
                                            <div class="form-group">
                                                <span><br></span>
                                               <!-- Image preview section -->
                                                <div id="imagePreview" style="position: absolute; top: 45px; bottom: 85px;">
                                                    <?php if(!empty($logo_image)){ ?>
                                                        <a href="{{ asset('uploads/organiser/logo_image/' . $logo_image) }}" target="_blank">
                                                            <img id="preview" src="{{ asset('uploads/organiser/logo_image/' . $logo_image) }}" alt="Current Image" style="width: 50px;">
                                                        </a>
                                                        <input type="hidden" name="hidden_organiser_logo_image" value="{{ old('organiser_logo_image', $logo_image) }}">
                                                    <?php } else { ?>
                                                        <img id="preview" class="preview-image" src="#" alt="Image Preview" style="display:none; width: 50px;">
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="banner_image">Banner Image 
                                                    <span style="color: #949090">(In jpg, jpeg, png formats. Max upto 5MB.
                                                        Dimensions- 1920 px x 744 px)</span>  
                                                </label>
                                                <input type="file" id="banner_image" class="form-control"
                                                    placeholder="Enter Banner Image" name="banner_image"
                                                    value="{{ old('banner_image') }}"
                                                    autocomplete="off" accept="image/jpeg, image/png" onchange="previewBannerImage(this); BannervalidateSize(this);"  />
                                                   
                                                    <span class="error" id="banner_image_err" style="color:red;"></span>
                                                    @error('banner_image')
                                                        <span class="error" style="color:red;">{{ $message }}</span>
                                                    @enderror 
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-12">
                                            <div class="form-group">
                                                <span><br></span>
                                               <!-- Image preview section -->
                                                <div id="imagePreview_banner" style="position: absolute; top: 45px; bottom: 85px;">
                                                    <?php if(!empty($banner_image)){ ?>
                                                        <a href="{{ asset('uploads/organiser/banner_image/' . $banner_image) }}" target="_blank">
                                                            <img id="preview_banner" src="{{ asset('uploads/organiser/banner_image/' . $banner_image) }}" alt="Current Image" style="width: 50px;">
                                                        </a>
                                                        <input type="hidden" name="hidden_organiser_logo_image" value="{{ old('banner_image', $banner_image) }}">
                                                    <?php } else { ?>
                                                        <img id="preview_banner" class="preview-image" src="#" alt="Image Preview" style="display:none; width: 50px;">
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="registered_pancard">Registered Pancard
                                                    <span style="color: #949090">(In jpg, jpeg, png formats. Max upto 5MB.
                                                        Dimensions- 1920 px x 744 px)</span>  
                                                </label>
                                                <input type="file" id="registered_pancard" class="form-control"
                                                    placeholder="Enter Registered Pancard" name="registered_pancard"
                                                    value="{{ old('registered_pancard') }}"
                                                    autocomplete="off" accept="image/jpeg, image/png" onchange="previewPancardImage(this); PancardvalidateSize(this);"  />
                                                   
                                                    <span class="error" id="registered_pancard_err" style="color:red;"></span>
                                                    @error('registered_pancard')
                                                        <span class="error" style="color:red;">{{ $message }}</span>
                                                    @enderror 
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-12">
                                            <div class="form-group">
                                                <span><br></span>
                                               <!-- Image preview section -->
                                                <div id="imagePreviewpancard" style="position: absolute; top: 45px; bottom: 85px;">
                                                    <?php if(!empty($company_pan)){ ?>
                                                        <a href="{{ asset('uploads/organiser/company_pancard/' . $company_pan) }}" target="_blank">
                                                            <img id="preview_pancard" src="{{ asset('uploads/organiser/company_pancard/' . $company_pan) }}" alt="Current Image" style="width: 50px;">
                                                        </a>
                                                        <input type="hidden" name="hidden_registered_pancard" value="{{ old('registered_pancard', $logo_image) }}">
                                                    <?php } else { ?>
                                                        <img id="preview_pancard" class="preview-image" src="#" alt="Image Preview" style="display:none; width: 50px;">
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="registered_gst_certificate">Registered GST Certificate
                                                    <span style="color: #949090">(In jpg, jpeg, png formats. Max upto 5MB.
                                                        Dimensions- 1920 px x 744 px)</span>  
                                                </label>
                                                <input type="file" id="registered_gst_certificate" class="form-control"
                                                    placeholder="Enter Registered GST Certificate" name="registered_gst_certificate"
                                                    value="{{ old('registered_gst_certificate') }}"
                                                    autocomplete="off" accept="image/jpeg, image/png" onchange="previewGSTImage(this); GSTvalidateSize(this);"  />
                                                   
                                                    <span class="error" id="registered_gst_certificate_err" style="color:red;"></span>
                                                    @error('registered_gst_certificate')
                                                        <span class="error" style="color:red;">{{ $message }}</span>
                                                    @enderror 
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-12">
                                            <div class="form-group">
                                                <span><br></span>
                                               <!-- Image preview section -->
                                                <div id="imagePreviewGST" style="position: absolute; top: 45px; bottom: 85px;">
                                                    <?php if(!empty($gst_certificate)){ ?>
                                                        <a href="{{ asset('uploads/organiser/gst_certificate/' . $gst_certificate) }}" target="_blank">
                                                            <img id="preview_gst" src="{{ asset('uploads/organiser/gst_certificate/' . $gst_certificate) }}" alt="Current Image" style="width: 50px;">
                                                        </a>
                                                        <input type="hidden" name="hidden_registered_gst_certificate" value="{{ old('registered_gst_certificate', $gst_certificate) }}">
                                                    <?php } else { ?>
                                                        <img id="preview_gst" class="preview-image" src="#" alt="Image Preview" style="display:none; width: 50px;">
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                   
                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/organiser_master') }}" type="reset"
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
<script>
      function checkedgst() {
        var gstCheckbox = document.getElementById('gstCheckbox');
        var gstFields = document.querySelectorAll('.gst-dependent');

        // Check the state of the checkbox and show/hide fields accordingly
        if (gstCheckbox.checked) {
            gstFields.forEach(function(field) {
                field.style.display = 'block';
            });
        } else {
            gstFields.forEach(function(field) {
                field.style.display = 'none';
            });
        }
    }

    // Call the function on page load to ensure fields are displayed if the checkbox was previously checked
    window.onload = function() {
        checkedgst();
    };
</script>
<script src={{ asset('/app-assets/js/scripts/Ckeditor/ckeditor.js') }}></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        ClassicEditor
            .create(document.querySelector('#about'))
            .catch(error => {
                console.error('Error initializing CKEditor:', error);
            });
    });
</script>
<script>
    // logo image
    function previewLogoImage(input) {
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

    function LogovalidateSize(input) {
        var isValid = true;
      const fileSize = input.files[0].size / 1024 / 1024; // in 2 MB
      var banner_image = $('#organiser_logo_image').val().trim();
 
    
      if(fileSize > 5) {
         // alert('File size exceeds 2 MB');
         if (banner_image !== "") {
            // alert("here");
            $('#organiser_logo_image').parent().addClass('has-error');
            $('#organiser_logo_image_err').html('The image must be 5MB or belows.');
            $('#organiser_logo_image').focus();
            $('#organiser_logo_image').keyup(function() {
                $('#organiser_logo_image').parent().removeClass('has-error');
                $('#organiser_logo_image_err').html('');
            });
            isValid = false;
        }

        return isValid;
      }
    }

    // banner image
    function previewBannerImage(input) {
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

    function BannervalidateSize(input) {
        var isValid = true;
      const fileSize = input.files[0].size / 1024 / 1024; // in 2 MB
      var banner_image = $('#banner_image').val().trim();
 
    
      if(fileSize > 5) {
         // alert('File size exceeds 2 MB');
         if (banner_image !== "") {
            // alert("here");
            $('#banner_image').parent().addClass('has-error');
            $('#banner_image_err').html('The image must be 5MB or belows.');
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


    // // Pancard
    function previewPancardImage(input) {
        var file = input.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('preview_pancard');
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    function PancardvalidateSize(input) {
        var isValid = true;
      const fileSize = input.files[0].size / 1024 / 1024; // in 2 MB
      var banner_image = $('#registered_pancard').val().trim();
 
    
      if(fileSize > 5) {
         // alert('File size exceeds 2 MB');
         if (banner_image !== "") {
            // alert("here");
            $('#registered_pancard').parent().addClass('has-error');
            $('#registered_pancard_err').html('The image must be 5MB or belows.');
            $('#registered_pancard').focus();
            $('#registered_pancard').keyup(function() {
                $('#registered_pancard').parent().removeClass('has-error');
                $('#registered_pancard_err').html('');
            });
            isValid = false;
        }

        return isValid;
      }
    }


    // // gst certificate
    function previewGSTImage(input) {
        var file = input.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('preview_gst');
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    function GSTvalidateSize(input) {
        var isValid = true;
      const fileSize = input.files[0].size / 1024 / 1024; // in 2 MB
      var banner_image = $('#registered_gst_certificate').val().trim();
 
    
      if(fileSize > 5) {
         // alert('File size exceeds 2 MB');
         if (banner_image !== "") {
            // alert("here");
            $('#registered_gst_certificate').parent().addClass('has-error');
            $('#registered_gst_certificate_err').html('The image must be 5MB or belows.');
            $('#registered_gst_certificate').focus();
            $('#registered_gst_certificate').keyup(function() {
                $('#registered_gst_certificate').parent().removeClass('has-error');
                $('#registered_gst_certificate_err').html('');
            });
            isValid = false;
        }

        return isValid;
      }
    }
</script>
 

