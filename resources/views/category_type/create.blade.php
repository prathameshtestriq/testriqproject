@extends('layout.index')
@if (!empty($id))
    @section('title', ' Category Type')
@else
    @section('title', ' Category Type')
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
                                        @if (!empty($aReturn['id']))
                                            Edit Category Type Details
                                        @else
                                            Add Category Type Details
                                        @endif
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end breadcrumb-wrapper">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mr-1">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('/dashboard') }}" class="text-reset text-decoration-none">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('/category') }}" class="text-reset text-decoration-none">Category Type</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        @if (!empty($aReturn['id']))
                                            Edit Category Type
                                        @else
                                            Add Category Type
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
                    {{ $message }}
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
                            @php
                                $category_logo_name = old('category_logo_name', $aReturn['category_logo'] ?? '');
                            @endphp
                            <form class="form" action="" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="form_type" value="add_edit_category">
                                {{ csrf_field() }}
                              
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="category_type_name">Category Type Name<span style="color:red;">*</span></label>
                                            <input type="text" id="category_type_name" class="form-control"
                                                placeholder="Enter Category Type Name" name="category_type_name"  
                                                value="{{ old('category_type_name', $aReturn['category_name'] ?? '') }}" autocomplete="off" />
                                            <h5><small class="text-danger" id="category_type_name_err"></small></h5>
                                            @error('category_type_name')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="category_logo_name">Category Logo <span style="color:red;">*</span>
                                                <span style="color: #949090">(Allowed JPEG, JPG or PNG. Max file size of 2 MB)</span>  
                                            </label>
                                            <input type="file" id="category_logo_name" class="form-control"
                                                name="category_logo_name" accept="image/jpeg, image/png" onchange="previewImage(this);" />
                                            <span class="error" id="category_logo_name_err" style="color:red;"></span>
                                            @error('category_logo_name')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-12">
                                        <span><br /></span>
                                        <!-- Image preview section -->
                                        <div id="imagePreview">
                                            @if(!empty($category_logo_name))
                                                <a id="previewLink" href="{{ asset('uploads/category/' . $category_logo_name) }}" target="_blank">
                                                    <img id="preview" src="{{ asset('uploads/category/' . $category_logo_name) }}" alt="Current Image" style="width: 50px;">
                                                </a>
                                                <input type="hidden" id="hidden_image" name="category_logo_name" value="{{ $category_logo_name }}">
                                            @else
                                                <img id="preview" src="#" alt="Image Preview" style="display:none; width:50px;">
                                                <input type="hidden" id="hidden_image" name="category_logo_name" value="">
                                            @endif
                                        </div>        
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="event_id">Event</label>
                                            <select name="event_id[]" class="form-control form-select select2" multiple style="min-width: 200px;">
                                                <option value="">Select Event</option>
                                                @foreach ($aReturn['events'] as $eventId => $eventData)
                                                    <option value="{{ $eventId }}" {{ $eventData['selected'] ? 'selected' : '' }}>
                                                        {{ $eventData['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <h5><small class="text-danger" id="event_id_err"></small></h5>
                                            @error('event_id')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-12"><br>
                                        <label>Status :</label> <br/>
                                        <div class="demo-inline-spacing">
                                            <div class="custom-control custom-radio mt-0">
                                                <input type="radio" id="customRadio1" name="status"
                                                    class="custom-control-input" value="active" 
                                                    @if(isset($aReturn['status']) && $aReturn['status'] == 1) checked @endif />
                                                <label class="custom-control-label" for="customRadio1">Active</label>
                                            </div>
                                            <div class="custom-control custom-radio mt-0">
                                                <input type="radio" id="customRadio2" name="status"
                                                    class="custom-control-input" value="inactive" 
                                                    @if(isset($aReturn['status']) && $aReturn['status'] == 0) checked @endif />
                                                <label class="custom-control-label" for="customRadio2">Inactive</label>
                                            </div>
                                        </div>
                                        <h5><small class="text-danger" id="status_err"></small></h5>
                                        @error('status')
                                            <span class="error" style="color:red;">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-12 text-center mt-1">
                                        <button type="submit" class="btn btn-primary mr-1" onClick="return validation()">Submit</button>
                                        <a href="{{ url('/category') }}" class="btn btn-outline-secondary">Cancel</a>
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
    let isValid = true;
    let category_name = $('#category_type_name').val().trim();
    let logo_file = $('#category_logo_name').val().trim();

    if (category_name === "") {
        $('#category_type_name_err').html('Please enter category Name.');
        isValid = false;
    } else {
        let category_filter = /^[a-zA-Z ]*$/;
        if (!category_filter.test(category_name)) {
            $('#category_type_name_err').html('The category name must only contain letters.');
            isValid = false;
        } else {
            $('#category_type_name_err').html('');
        }
    }

    @if(empty($category_logo_name))
    if (logo_file === "") {
        $('#category_logo_name_err').html('Please upload logo.');
        isValid = false;
    } else {
        $('#category_logo_name_err').html('');
    }
    @endif

    return isValid;
}


function previewImage(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('preview');
            preview.src = e.target.result;
            preview.style.display = 'block';

            // Update preview link dynamically
            let blobUrl = URL.createObjectURL(input.files[0]);
            let previewLink = document.getElementById('previewLink');
            if (previewLink) {
                previewLink.href = blobUrl;
            } else {
                let link = document.createElement('a');
                link.id = "previewLink";
                link.href = blobUrl;
                link.target = "_blank";
                preview.parentNode.insertBefore(link, preview);
                link.appendChild(preview);
            }

            // Reset hidden input when new image is selected
            let hiddenInput = document.getElementById('hidden_image');
            if (hiddenInput) {
                hiddenInput.value = '';
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
