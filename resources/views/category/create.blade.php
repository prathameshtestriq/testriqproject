@extends('layout.index')
@if (!empty($aReturn['id']))
    @section('title', 'Edit Category Details')
@else
    @section('title', 'Add Category Details')
@endif
<!-- Dashboard Ecommerce start -->
<style>
    * {
        font-size: 15px;
    }
</style>
@section('content')
    <section>
        <div class="content-body">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12 d-flex">
                            <h2 class="content-header-title float-start mb-0">
                                @if (!empty($aReturn['id']))
                                   Edit Category Details
                                @else
                                   Add Category Details
                                @endif 
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <div class="mb-1 breadcrumb-right">
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb" style="justify-content: flex-end">
                                <li class="breadcrumb-item"><a href="#">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Category</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    @if (!empty($aReturn['id']))
                                        Edit category 
                                    @else
                                         Add category
                                    @endif 
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

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
                                    {{-- <input type="hidden" name="user_id" id="user_id" value="{{ $id }}"> --}}

                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="row">
                                                
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" style="margin-top:20px" for="mobile">Category name <span style="color:red;">*</span></label>
                                                        <input type="text" id="category_name" class="form-control col-sm-8 float-right" name="category_name" placeholder="Category Name" autocomplete="off"
                                                               value="<?php echo isset($aReturn['category_name']) ? $aReturn['category_name'] : (isset($request->category_name) ? $request->category_name : ''); ?>" />
                                                        <h5><small class="text-danger" id="category_name_err"></small></h5>
                                                        <?php
                                                        if ($errors->has('category_name')) {
                                                            echo '<span class="error" style="color:red;">' . $errors->first('category_name') . '</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                
                                                

                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="col-sm-4 float-left" style="margin-top:20px"
                                                            for="mobile">Logo Name <span
                                                                style="color:red;">*</span></label>
                                                        <input type="text" id="category_logo"
                                                            class="form-control col-sm-8 float-right" name="category_logo"
                                                            placeholder="Logo Name" autocomplete="off"
                                                            value="{{ old('category_logo', isset($aReturn['category_logo']) ? $aReturn['category_logo'] : '') }}" />
                                                        <h5><small class="text-danger" id="category_logo_err"></small></h5>
                                                        @error('category_logo')
                                                            <span class="error" style="color:red;">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>


                                                <div class="col-sm-6">
                                                    <div class="row">

                                                        <div class="col-xs-12 col-md-12">
                                                            <label class="col-sm-4 float-left" for="password_confirmation"
                                                                style="margin-top:10px">Status</label>
                                                            <div class="form-check mt-1 mb-2">
                                                                <input class="form-check-input status1" type="radio"
                                                                    name="status" id="status1" value="active"
                                                                    <?php if (isset($aReturn['status']) && $aReturn['status'] == 1) {
                                                                        echo 'checked';
                                                                    } ?>>
                                                                <label class="form-check-label mr-4" for="status1">
                                                                    Active
                                                                </label>
                                                                <input class="form-check-input status1" type="radio"
                                                                    name="status" id="status2" value="inactive"
                                                                    <?php if (isset($aReturn['status']) && $aReturn['status'] == 0) {
                                                                        echo 'checked';
                                                                    } ?>>
                                                                <label class="form-check-label" for="status2">
                                                                    Inactive
                                                                </label>
                                                            </div>

                                                            <h5><small class="text-danger" id="status_err"></small></h5>
                                                            @error('status')
                                                                <span class="error"
                                                                    style="color:red;">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-xs-12 col-md-12">
                                                            <div class="form-group mb-5">
                                                                <label for="event_id" class="col-sm-4 float-left">Event <span style="color:red;">*</span></label>
                                                                <div class="input-group col-sm-8 float-right">
                                                                    <select name="event_id[]" class="form-control form-select select2" multiple style="min-width: 200px;">
                                                                        <option value="">Select Event</option>
                                                                        @foreach ($aReturn['events'] as $eventId => $eventData)
                                                                            <option value="{{ $eventId }}" {{ $eventData['selected'] ? 'selected' : '' }}>
                                                                                {{ $eventData['name'] }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                
                                                                <h5><small class="text-danger" id="event_id_err"></small></h5>
                                                                @error('event_id')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        
                                                        
                                                    </div>
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