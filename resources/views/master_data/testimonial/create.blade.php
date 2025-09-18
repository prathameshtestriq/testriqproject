<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
@extends('layout.index')
@if (!empty($id))
    @section('title', ' Testimonial ')
@else
    @section('title', ' Testimonial ')
@endif

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
                                                Edit Testimonial Details
                                            @else
                                                Add Testimonial Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Testimonial</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($id))
                                                Edit Testimonial Details
                                            @else
                                                Add Testimonial Details
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
                                <form method="post" name="transfer" id="transfer" novalidate="novalidate"
                                enctype="multipart/form-data">
                                <input type="hidden" name="form_type" value="add_edit_testimonial">
                                {{ csrf_field() }}

                                    <div class="row">
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="testimonial_name">Testimonial Name <span
                                                        style="color:red;">*</span></label>
                                                <input type="text" id="testimonial_name" class="form-control"
                                                    placeholder=" Testimonial Name" name="testimonial_name"
                                                    value="{{ old('testimonial_name', $testimonial_name) }}"   autocomplete="off" />
                                                <h5><small class="text-danger" id="testimonial_name_err"></small></h5>
                                                @error('testimonial_name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="subtitle">Subtitle <span style="color:red;">*</span></label>
                                                <input type="text" id="subtitle" class="form-control"
                                                    placeholder="Subtitle" name="subtitle"
                                                    value="{{ old('subtitle', $subtitle) }}" 
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="subtitle_err"></small></h5>
                                                @error('subtitle')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="testimonial_image">Testimonial Image <span style="color:red;">*</span>
                                                    <span style="color: #949090">(Allowed JPEG, JPG or PNG. Max file size of 5 MB)</span>  
                                                </label>
                                                
                                                <input type="file" id="testimonial_image" class="form-control"
                                                    name="testimonial_image"
                                                    accept="image/jpeg, image/png" 
                                                    autocomplete="off" onchange="previewImage(this); " />
                                                <span class="error" id="testimonial_image_err" style="color:red;"></span>
                                                @error('testimonial_image')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-1 mt-2">
                                            <span><br /></span>
                                            <!-- Image preview section -->
                                            <div id="imagePreview">

                                                <?php 
                                                    if(!empty($testimonial_img)){ ?>
                                                    <a href="{{ asset('uploads/testimonial_images/' . $testimonial_img) }}" target="_blank">
                                                        <img id="preview" src="{{ asset('uploads/testimonial_images/' . $testimonial_img) }}" alt="Current Image" style="width: 50px;">
                                                    </a>
                                                    <input type="hidden" name="hidden_testimonial_img" value="{{ old('testimonial_img', $testimonial_img) }}" accept="image/jpeg, image/png">
                                                <?php } else { ?>
                                                    <img id="preview" class="preview-image" src="#" alt="Image Preview" style="display:none; width: 50px;">
                                                <?php } ?>
                                            </div>    
                                        </div>

                                        <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="description">Description <span style="color:red;">*</span></label>
                                                    <textarea id="description" class="form-control" placeholder="Description"
                                                    name="description" autocomplete="off">{{ old('description', $description) }}</textarea> 
                                                <h5><small class="text-danger" id="description_err"></small></h5>
                                                @error('description')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/testimonial') }}" type="reset"
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
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src={{ asset('/app-assets/js/scripts/jquerycdn.js') }}></script>
<script src={{ asset('/app-assets/js/scripts/Ckeditor/ckeditor.js') }}></script>
<script>
    // document.addEventListener('DOMContentLoaded', function () {
    //     ClassicEditor
    //         .create(document.querySelector('#description'))
    //         .catch(error => {
    //             console.error('Error initializing CKEditor:', error);
    //         });
    // });
    document.addEventListener('DOMContentLoaded', function () {
        ClassicEditor
        .create(document.querySelector('#description'), {
            ckfinder: {
                uploadUrl: '{{ route('ckeditor_testimonial_description.upload').'?_token='.csrf_token() }}'
            }
        })
        .catch(error => {
            console.error('Error initializing CKEditor:', error);
        });
      
    });
</script>
<script type="text/javascript">
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

</script>
