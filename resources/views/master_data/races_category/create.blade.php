@extends('layout.index')
@if (!empty($id))
    @section('title', ' Races Category')
@else
    @section('title', 'Races Category')
@endif

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

@section('title', 'Type Create')
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
                                                Edit Races Category Details
                                            @else
                                                Add Races Category Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Type</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($id))
                                            Edit Type Details
                                            @else
                                            Add Type Details
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
                                <input type="hidden" name="form_type" value="add_edit_type">
                                {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="race_category_name">Race Category Name <span
                                                        style="color:red;">*</span></label>
                                                <input type="text" id="race_category_name" class="form-control "
                                                    placeholder="Race Category Name" name="race_category_name"
                                                    value="{{ old('race_category_name', $name) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="race_category_name_err"></small></h5>
                                                @error('race_category_name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-12">
                                            <div class="form-group mt-2">
                                                <input type="checkbox" id="show_as_home" class="checkbox m-1" name="show_as_home" value="1"<?php if(old('show_as_home', $show_as_home)==1){ echo 'checked';};?> autocomplete="off" style="transform: scale(1.5);" />
                                                <label for="show_as_home">Show as home</label>
                                                <h5><small class="text-danger" id="show_as_home_err"></small></h5>
                                                @error('show_as_home')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12" id="races_logo_section" style="display: none;">
                                            <div class="form-group">
                                                <label for="races_logo">Races Logo <span style="color:red;">*</span>
                                                    <span style="color: #949090">(Allowed JPEG, JPG or PNG. Max file size of 2 MB)</span>  
                                                </label>
                                               
                                                <input type="file" id="races_logo" class="form-control"
                                                    placeholder="Logo Name" name="races_logo"
                                                    style="text-transform: capitalize; display: block; width: 100%;"
                                                    accept="image/jpeg, image/png"
                                                    autocomplete="off"  onchange="previewImage(this); validateSize(this);" />
                                                    <span class="error" id="races_logo_err" style="color:red;"></span>
                                                @error('races_logo')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- <div class="col-md-1">
                                            <span><br/></span>
                                            @if (!empty($logo))
                                                <a href="{{ asset('uploads/type_images/' . $logo) }}" target="_blank">
                                                    <img src="{{ asset('uploads/type_images/' . $logo) }}" alt="Current Image"
                                                    style="width: 50px;">
                                                </a>
                                                <input type="hidden" name="hidden_logo" value="{{ old('logo', $logo) }}"
                                                    accept="image/jpeg, image/png">
                                            @endif
                                        </div> --}}

                                        <div class="col-sm-2 mt-2">
                                            <span><br /></span>
                                            <!-- Image preview section -->
                                            <div id="imagePreview">

                                                <?php 
                                                    if(!empty($logo)){ ?>
                                                    <a href="{{ asset('uploads/type_images/' . $logo) }}" target="_blank">
                                                        <img id="preview" src="{{ asset('uploads/type_images/' . $logo) }}" alt="Current Image"
                                                        style="width: 50px;">
                                                    </a>
                                                    <input type="hidden" name="hidden_logo" value="{{ old('logo', $logo) }}"
                                                        accept="image/jpeg, image/png">
                                                <?php } else { ?>
                                                    <img id="preview" class="preview-image" src="#" alt="Image Preview" style="display:none; width: 50px;">
                                                <?php } ?>
                                            </div>    

                                        </div>

                                        

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/type') }}" type="reset"
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
    $(document).ready(function() {
        // Show/Hide races logo section based on checkbox status on page load
        toggleRacesLogo();

        // Show/Hide races logo section when checkbox is clicked
        $('#show_as_home').change(function() {
            toggleRacesLogo();
        });

        function toggleRacesLogo() {
            if ($('#show_as_home').is(':checked')) {
                $('#races_logo_section').show();
                $('#current_logo_section').show();
            } else {
                $('#races_logo_section').hide();
                $('#current_logo_section').hide();
            }
        }
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
    function validateSize(input) {
        var isValid = true;
      const fileSize = input.files[0].size / 1024 / 1024; // in 2 MB
      var races_logo = $('#races_logo').val().trim();
      if(fileSize > 2) {
        //  alert('File size exceeds 2 MB');
         if (races_logo !== "") {
            // alert("here");
            $('#races_logo').parent().addClass('has-error');
            $('#races_logo_err').html('The image must be 2MB or below.');
            $('#races_logo').focus();
            $('#races_logo').keyup(function() {
                $('#races_logo').parent().removeClass('has-error');
                $('#races_logo_err').html('');
            });
            isValid = false;
        }

        return isValid;
      }else{
        
      }
   }
</script>

