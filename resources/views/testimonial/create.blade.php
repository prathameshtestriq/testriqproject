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
                                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
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
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="testimonial_name">Testimonial Name<span
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

                                        <div class="col-md-6 col-12">
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

                                        <div class="col-md-6 mt-1 col-12">
                                            <div class="form-group">
                                                <label for="description">Description <span style="color:red;">*</span></label>
                                                <input type="text" id="description" class="form-control mt-2"
                                                    placeholder="Description" name="description"
                                                    value="{{ old('description', $description) }}" 
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="description_err"></small></h5>
                                                @error('description')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="testimonial_img">Image <span style="color:red;">*</span></label>
                                                <p style="color:red;">Allowed JPEG, JPG or PNG. Max file size of 2 MB</p>
                                                <input type="file" id="testimonial_img" class="form-control"
                                                    name="testimonial_img"
                                                    accept="image/jpeg, image/png" 
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="testimonial_img_err"></small></h5>
                                                @error('testimonial_img')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                          
                                        <div class="col-sm-2 mt-2">
                                            @if (!empty($testimonial_img))
                                                <a href="{{ asset('uploads/testimonial_images/' . $testimonial_img) }}" target="_blank">
                                                    <img src="{{ asset('uploads/testimonial_images/' . $testimonial_img) }}" alt="Current Image" style="width: 50px;">
                                                </a>
                                                <input type="hidden" name="hidden_testimonial_img" value="{{ old('testimonial_img', $testimonial_img) }}" accept="image/jpeg, image/png">
                                            @endif
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

<script type="text/javascript">
    function validation() {
        var isValid = true;


        $('.error').html('');

     
    }
</script>
