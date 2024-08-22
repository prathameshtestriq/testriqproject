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
                                <form class="form" action="" method="post">
                                    <input type="hidden" name="form_type" value="add_edit_event">
                                {{ csrf_field() }}

                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="name">Event Name<span style="color:red;">*</span></label>
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
                                                <label for="start_time">Start Date<span style="color:red;">*</span></label>
                                                <input type="date" id="start_time" class="form-control"
                                                    placeholder="Start Date" name="start_time"
                                                    value="{{ old('start_time', $start_time ? \Carbon\Carbon::parse($start_time)->format('Y-m-d\TH:i') : '') }}"
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="start_time_err"></small></h5>
                                                @error('start_time')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="end_time">End Date<span style="color:red;">*</span></label>
                                                <input type="date" id="end_time" class="form-control"
                                                    placeholder="End Date" name="end_time"
                                                    value="{{ old('end_time', $end_time ? \Carbon\Carbon::parse($end_time)->format('Y-m-d\TH:i') : '') }}" 
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="end_time_err"></small></h5>
                                                @error('end_time')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="country">Country <span style="color:red;">*</span></label>
                                                <select id="country" name="country" class="select2 form-control">
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
                                                    <h5><small class="text-danger" id="country_id_err"></small></h5>
                                                @error('country_id')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="city">City <span style="color:red;">*</span></label>
                                                <select id="city" name="city" class="select2 form-control">
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
                                                    <h5><small class="text-danger" id="city_id_err"></small></h5>
                                                @error('city_id')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
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
                                                <label for="event_description">Event Description <span style="color:red;">*</span></label>
                                                <input type="text" id="event_description" class="form-control" placeholder="Event Description"
                                                    name="event_description"    value="{{ old('event_description', $event_description) }}"  autocomplete="off" />
                                                <h5><small class="text-danger" id="event_description_err"></small></h5>
                                                @error('event_description')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="event_keywords">Event Keywords <span style="color:red;">*</span></label>
                                                <input type="text" id="event_keywords" class="form-control" placeholder="Event Keywords"
                                                    name="event_keywords"    value="{{ old('event_keywords', $event_keywords) }}"  autocomplete="off" />
                                                <h5><small class="text-danger" id="event_keywords_err"></small></h5>
                                                @error('event_keywords')
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

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="state">State <span style="color:red;">*</span></label>
                                                <select id="state" name="state" class="select2 form-control">
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
                                                    <h5><small class="text-danger" id="state_err"></small></h5>
                                                @error('state')
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

                                        <div class="col-12 text-center mt-1">
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
