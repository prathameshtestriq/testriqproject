@extends('layout.index')
@if (!empty($id))
    @section('title', 'Edit Category Details')
@else
    @section('title', 'Add Category Details')
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
                                                Edit Category Details
                                            @else
                                                Add Category Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                        <li class="breadcrumb-item">Category</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($aReturn['id']))
                                                Edit category 
                                            @else
                                                Add category
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
                                <form class="form" action="" method="post">
                                    <input type="hidden" name="form_type" value="add_edit_category"
                                        enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                  
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="category_name">Category type name<span style="color:red;">*</span></label>
                                                <input type="text" id="category_name" class="form-control"
                                                    placeholder="Category Type Name" name="category_name"  value="<?php echo isset($aReturn['category_name']) ? $aReturn['category_name'] : (isset($request->category_name) ? $request->category_name : ''); ?>" autocomplete="off" />
                                                <h5><small class="text-danger" id="category_name_err"></small></h5>
                                                @error('category_name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="lastname">Logo Name <span style="color:red;">*</span></label>
                                                <input type="text" id="category_logo" class="form-control"
                                                    placeholder="Logo Name" name="category_logo"  value="{{ old('category_logo', isset($aReturn['category_logo']) ? $aReturn['category_logo'] : '') }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="category_logo_err"></small></h5>
                                                @error('category_logo')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
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
                                            <label for="password_confirmation m-2">Status :</label> <br/>
                                            <div class="demo-inline-spacing">
                                                <div class="custom-control custom-radio mt-0">
                                                    <input type="radio" id="customRadio1" name="status"
                                                        class="custom-control-input" value="active"   <?php if (isset($aReturn['status']) && $aReturn['status'] == 1) {
                                                            echo 'checked';
                                                        } ?> />
                                                    <label class="custom-control-label" for="customRadio1">Active</label>
                                                </div>
                                                <div class="custom-control custom-radio mt-0">
                                                    <input type="radio" id="customRadio2" name="status"
                                                        class="custom-control-input" value="inactive"   <?php if (isset($aReturn['status']) && $aReturn['status'] == 0) {
                                                            echo 'checked';
                                                        } ?> />
                                                    <label class="custom-control-label" for="customRadio2">Inactive</label>
                                                </div>
                                            </div>
                                            <h5><small class="text-danger" id="status_err"></small></h5>
                                            @error('status')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/category') }}" type="reset"
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

        var category_name = $('#category_name').val().trim();
        var logo_name = $('#logo_name').val().trim();

        if (category_name === "") {
            $('#category_name').parent().addClass('has-error');
            $('#category_name_err').html('Please enter category Name.');
            $('#category_name').focus();
            $('#category_name').keyup(function() {
                $('#category_name').parent().removeClass('has-error');
                $('#category_name_err').html('');
            });
            isValid = false;
        } else {
            var category_filter = /^[a-zA-Z]*$/; // Regex pattern to allow letters
            if (!category_filter.test(category_name)) {
                $('#category_name').parent().addClass('has-error');
                $('#category_name_err').html('The category name must only contain letters.');
                $('#category_name').focus();
                isValid = false;
            } else {
                $('#category_name').parent().removeClass('has-error');
                $('#category_name_err').html('');
            }
        }

        if (logo_name === "") {
            $('#logo_name').parent().addClass('has-error');
            $('#logo_name_err').html('Please enter logo Name.');
            $('#logo_name').focus();
            $('#logo_name').keyup(function() {
                $('#logo_name').parent().removeClass('has-error');
                $('#logo_name_err').html('');
            });
            isValid = false;
        }

        return isValid;
    
    }
</script>
