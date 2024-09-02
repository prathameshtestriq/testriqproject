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
                                            Edit Organiser Master  
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
                                            Edit Organiser Master      
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
                                    <input type="hidden" name="form_type" value="edit_organiser_master"
                                        enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                  
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="organiser_name">Oraniser Name<span style="color:red;">*</span></label>
                                                <input type="text" id="organiser_name" class="form-control"
                                                    placeholder="Enter Oraniser Name" name="organiser_name"   autocomplete="off" />
                                                <h5><small class="text-danger" id="organiser_name_err"></small></h5>
                                                @error('organiser_name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="category_logo_name">Category Logo Name <span style="color:red;">*</span></label>
                                                <input type="text" id="category_logo_name" class="form-control"
                                                    placeholder="Enter Category Logo Name" name="category_logo_name"  value="{{ old('category_logo', isset($aReturn['category_logo']) ? $aReturn['category_logo'] : '') }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="category_logo_name_err"></small></h5>
                                                @error('category_logo_name')
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
                                        </div> --}}

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
 

