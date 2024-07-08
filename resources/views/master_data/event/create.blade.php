@extends('layout.index')
@if (!empty($id))
@section('title', 'Edit Event Details')
@else
@section('title', 'Add Event Details')
@endif

<?php //dd($Category_array)?>
<style>
    * {
        font-size: 15px;
    }
</style>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
@section('content')
<section>
  
    <div class="content-body">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12 d-flex">
                        <h2 class="content-header-title float-start mb-0">
                            @if (!empty($id))
                            Edit Event Details
                            @else
                            Add Event Details
                            @endif
                        </h2>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <div class="mb-1 breadcrumb-right">
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb" style="justify-content: flex-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Event</a></li>
                            <li class="breadcrumb-item active">
                                @if (!empty($id))
                                Edit Event Details
                                @else
                                Add Event Details
                                @endif
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
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
                            <form method="post" name="transfer" id="transfer" novalidate="novalidate"
                                enctype="multipart/form-data">
                                <input type="hidden" name="form_type" value="add_edit_event">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name">Event Name <span style="color:red;">*</span></label>
                                            <input type="text" id="name" class="form-control" name="name"
                                                placeholder="Event Name" autocomplete="off"
                                                value="{{ old('name', $name) }}" />
                                            <small class="text-danger">{{ $errors->first('name') }}</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="address">Event Address <span style="color:red;">*</span></label>
                                            <input type="text" id="address" class="form-control" name="address"
                                                placeholder="Event Address" autocomplete="off"
                                                value="{{ old('address', $address) }}" />
                                            <small class="text-danger">{{ $errors->first('address') }}</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="start_time">Start Date <span style="color:red;">*</span></label>
                                            <input type="datetime-local" id="start_time" class="form-control"
                                                name="start_time" autocomplete="off"
                                                value="{{ old('start_time', $start_time ? \Carbon\Carbon::parse($start_time)->format('Y-m-d\TH:i') : '') }}" />
                                            <small class="text-danger">{{ $errors->first('start_time') }}</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="end_time">End Date <span style="color:red;">*</span></label>
                                            <input type="datetime-local" id="end_time" class="form-control"
                                                name="end_time" autocomplete="off"
                                                value="{{ old('end_time', $end_time ? \Carbon\Carbon::parse($end_time)->format('Y-m-d\TH:i') : '') }}" />
                                            <small class="text-danger">{{ $errors->first('end_time') }}</small>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="country">Country <span style="color:red">*</span></label>
                                            <select name="country" id="country" class="form-control">
                                                <option value="">Select Country</option>
                                                @foreach($countries_array as $res)
                                                @php
                                                $selected = '';
                                                if (old('country', $country) == $res->id) {
                                                $selected = 'selected';
                                                }
                                                @endphp
                                                <option value="{{ $res->id }}" {{$selected}}>{{ $res->name }}</option>
                                                @endforeach
                                            </select>
                                            <span style="color:red;" id="country_err">
                                            </span>
                                            @error('country')
                                            <span class="error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="city">city <span style="color:red;">*</span></label>
                                            <select class="form-control" name="city" id="city">
                                                <option value="">Select city</option>
                                                @foreach ($cities_array as $res)
                                                @php
                                                $selected = '';
                                                if (old('city', $city) == $res->id) {
                                                $selected = 'selected';
                                                }
                                                @endphp

                                                <option value="{{ $res->id }}" {{$selected}}>{{ $res->name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger">{{ $errors->first('state') }}</small>
                                        </div>
                                        <!-- <div class="form-group mb-3">
                                            <label for="status" class="col-sm-2 float-left">Status</label>
                                            <div class="form-check mt-1 mb-2">
                                                <input class="form-check-input active1" type="radio" name="active"
                                                    style="cursor: pointer;" id="active1" value="active" {{ $active==1
                                                    ? 'checked' : '' }}>
                                                <label class="form-check-label mr-4" for="active1">Active</label>
                                                <input class="form-check-input active1" type="radio" name="active"
                                                    style="cursor: pointer;" id="active2" value="inactive" {{ $active==0
                                                    ? 'checked' : '' }}>
                                                <label class="form-check-label" for="active2">Inactive</label>
                                            </div>
                                            <h5><small class="text-danger" id="active_err"></small></h5>
                                            @error('active')
                                            <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div> -->
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="event_url">Event URL <span style="color:red;">*</span></label>
                                            <input type="text" id="event_url" class="form-control" name="event_url"
                                                placeholder="Event URL" autocomplete="off"
                                                value="{{ old('event_url', $event_url) }}" />
                                            <small class="text-danger">{{ $errors->first('event_url') }}</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="description">Event Description <span
                                                    style="color:red;">*</span></label>
                                            <input type="text" id="event_description" class="form-control" name="event_description"
                                                placeholder="Event Description" autocomplete="off"
                                                value="{{ old('event_description', $event_description) }}" />
                                            <small class="text-danger">{{ $errors->first('event_description') }}</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="event_keywords">Event Keywords <span
                                                    style="color:red;">*</span></label>
                                            <input type="text" id="event_keywords" class="form-control"
                                                name="event_keywords" placeholder="Event Keywords" autocomplete="off"
                                                value="{{ old('event_keywords', $event_keywords) }}" />
                                            <small class="text-danger">{{ $errors->first('event_keywords') }}</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="timezones">Timezone <span style="color:red">*</span></label>
                                            <select name="time_zone" id="time_zone" class="form-control">
                                                <option value="">-- Select Timezone --</option>
                                                @foreach($timezones_array as $res)
                                                <option value="{{ $res->id }}" {{ old('time_zone', $time_zone)==$res->id
                                                    ? 'selected' : '' }}>
                                                    {{ $res->area }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger">{{ $errors->first('time_zone') }}</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="state">State <span style="color:red;">*</span></label>
                                            <select class="form-control" name="state" id="state">
                                                <option value="">Select State</option>
                                                @foreach ($states_array as $res)
                                                @php
                                                $selected = '';
                                                if (old('state', $state) == $res->id) {
                                                $selected = 'selected';
                                                }
                                                @endphp
                                                <option value="{{ $res->id }}" {{ $selected }}>
                                                    {{ $res->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger">{{ $errors->first('state') }}</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="categorySelect" class="col-sm-4 float-left">Select Category
                                                <span style="color:red;">*</span></label>
                                            <!-- <div class="input-group col-sm-8 float-right"> -->

                                            <select class="form-control form-select select2" multiple
                                                style="min-width: 400px;" id="categorySelect" name="category_id[]">
                                                @foreach($Category as $category)
                                                <option value="{{ $category->category_id }}" {{ $category->selected ?
                                                    'selected' : '' }}>
                                                    {{ $category->category_name }}</option>
                                                @endforeach
                                            </select>
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                    <!-- Submit Button -->
                                    <div class="col-12 text-center mt-1">
                                        <button type="submit" class="btn btn-primary mr-1"
                                            onClick="return validation()">Submit</button>
                                        <a href="{{ url('/event') }}" type="reset"
                                            class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        $(document).ready(function() {
        $('#country').change(function() {
            var countryId = $(this).val();
            // console.log("Country Id: " + countryId);
            if (countryId) {
                $.ajax({
                    url: "{{url('get-states')}}?country_id=" + countryId,
                    type: 'GET',
                    success: function (res) {
                        // console.log("Response from get-states:");
                        // console.log(res);
                        $('#state').empty();
                        $('#state').append('<option value="">Select</option>');
                        $.each(res, function (key, value) {
                            $('#state').append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                $('#state').empty();
                $('#city').empty();
            }
        });

        $('#state').change(function() {
            var stateId = $(this).val();
            // console.log("State Id: " + stateId);
            if (stateId) {
                $.ajax({
                    url: "{{url('get-cities')}}?state_id=" + stateId,
                    type: 'GET',
                    success: function (res) {
                        // console.log("Response from get-cities:");
                        // console.log(res);
                        $('#city').empty();
                        $('#city').append('<option value="">Select</option>');
                        $.each(res, function (key, value) {
                            $('#city').append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                $('#city').empty();
            }
        });
    });
    </script>

</section>
@endsection