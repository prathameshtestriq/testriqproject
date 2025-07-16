@extends('layout.index')
@section('title', 'Event ')

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
                                        <h2 class="content-header-title float-left mb-0">Event </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Events</li>
                                        <li class="breadcrumb-item active" aria-current="page">Event List</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bordered table end -->
        </div>

        @if ($message = Session::get('success'))
            <div class="demo-spacing-0 mb-1">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-body">
                        <i class="fa fa-check-circle" style="font-size:16px;" aria-hidden="true"></i>
                        {{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @elseif ($message = Session::get('error'))
            <div class="demo-spacing-0 mb-1">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                        {{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif

        <div class="alert alert-success p-1" id="success-alert" style="display: none;">
            <i class="fa fa-check-circle" style="font-size:16px;" aria-hidden="true"></i>
            <span id="success-message"></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="alert alert-danger p-1" id="error-alert" style="display: none;">
            <i class="fa fa-exclamation-triangle" style="font-size:16px;" aria-hidden="true"></i>
            <span id="error-message"></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="content-body">
            <!-- Bordered table start -->
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card ">
                        <form class="dt_adv_search" action="{{ url('event') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_event">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="form-control"> Event</label>
                                                <select id="name" name="name" class="form-control select2 form-control">
                                                    <option value="">Select  Event</option>
                                                    <?php 
                                                        foreach ($EventsData as $value)
                                                        {
                                                            $selected = '';
                                                            if(old('name', $search_event_name) == $value->id){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo ucfirst($value->name); ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                          
                                            <div class="col-sm-3 ">
                                                <label for="form-control">Start Booking Date</label>
                                                <input type="date" id="event_start_date" class="form-control"
                                                    placeholder="Start Date" name="event_start_date"   value="{{ old('start_date', $search_event_start_date ? \Carbon\Carbon::parse($search_event_start_date)->format('Y-m-d') : '') }}"   
                                                    autocomplete="off" onkeydown="return false;" onchange="setEndDateMin()" />
                                            </div>
                                            
                                            <div class="col-sm-3">
                                                <label for="form-control">End Booking Date</label>
                                                <input type="date" id="event_end_date" class="form-control"
                                                    placeholder="End Date" name="event_end_date" value="{{ old('end_date', $search_event_end_date ? \Carbon\Carbon::parse($search_event_end_date)->format('Y-m-d') : '') }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-3">
                                                <label for="form-control">Country</label>
                                                <select id="country" name="event_country" class="select2 form-control">
                                                    <option value="">All country</option>
                                                    <?php  
                                                    foreach ($countries as $value)
                                                    {  
                                                        $selected = '';
                                                        if(old('event_country', $search_event_country) == $value->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo ucfirst($value->name); ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3 mt-2">
                                                <label for="form-control">State</label>
                                                <select id="state" name="event_state" class="select2 form-control">
                                                    <option value="">All state</option>
                                                </select>  
                                            
                                            </div>
    
                                            <div class="col-sm-3 mt-2">
                                                <label for="form-control">City</label>
                                                <select id="city" name="event_city" class="select2 form-control">
                                                    <option value="">All City</option>
                                                </select>  
                                            </div>

                                            <div class="col-sm-3 col-12 mt-2">
                                                <?php 
                                                   $event_status = array(0=>'Inactive',1=>'Active' );    
                                                ?> 
                                                <label for="form-control"> Status</label>
                                                <select id="event_status" name="event_status" class="form-control select2 form-control">
                                                    <option value="">Select  Status</option>
                                                    <?php 
                                                        foreach ($event_status as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if(old('event_status',$search_event_status) == $key){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3 col-12 mt-2">
                                                <label for="form-control"> Organiser</label>
                                                <select id="organizer" name="organizer" class="form-control select2 form-control">
                                                    <option value="">Select  Organiser</option>
                                                    <?php 
                                                        foreach ($organizer as  $value)
                                                        {
                                                            
                                                            $selected = '';
                                                            if(old('organizer',$search_organizer) == $value->id){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->name; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 col-12 mt-2">
                                                <?php 
                                                   $event_info_status = array(1=>'Public',2=>'Private',3=>'Draft' );    
                                                ?> 
                                                <label for="form-control"> Event Info Status</label>
                                                <select id="event_info_status" name="event_info_status" class="form-control select2 form-control">
                                                    <option value="">Select  Status</option>
                                                    <?php 
                                                        foreach ($event_info_status as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if(old('event_info_status',$search_event_info_status) == $key){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                        
                                            <div class="col-sm-3 mt-4">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_event_name)|| !empty($search_event_start_date) || !empty($search_event_end_date) || ($search_event_status != '')|| !empty($search_organizer)|| !empty($search_event_country)|| !empty($search_event_state)|| !empty($search_event_city) || !empty($search_event_info_status))
                                                    <a title="Clear" href="{{ url('event/clear_search') }}" type="button"
                                                    class="btn btn-outline-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw me-25"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg> Clear Search
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-sm-4 mt-2">
                                        <a href="{{ url('/event/add') }}" class="btn btn-outline-primary float-right">
                                            <i data-feather="plus"></i><span>Add Event</span></a>
                                    </div> --}}
                                </div>
                            </div>

                        </form>
                        <div class="row px-2">
                            <div class="col-sm-8 float-right">
                                <h2 class="content-header-title float-left mb-0">Event details</h2>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mt-2">
                                <thead>
                                    <tr> 
                                        <th style="text-align: center;">Sr No.</th>
                                        <th>Event Id</th>
                                        <th>Event Name</th>
                                        <th>Event Start Date</th>
                                        <th>Event End Date</th>
                                        <th>Country</th>
                                        <th>State</th>
                                        <th>City</th>
                                        <th>Event Image</th>
                                        <th>Allow Guest Login</th>
                                        <th>Event Status</th>
                                        <th>Verify Status</th>
                                        <th style="text-align: center;">Status</th>
                                        <th style="text-align: center; width: 150px">View</th>
                                        <th style="text-align: center;width: 150px" >Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($event_array))
                                    <?php $i = $Offset; ?>
                                        @foreach ($event_array as $key => $event)
                                        <?php $i++; ?>
                                            <tr>
                                                <td style="text-align: center;">{{  $i }}</td>
                                                <td>{{ $event->id }}</td>
                                                <td>{{ ucfirst($event->name) }}</td>
                                                <td><?php echo !empty($event->start_time) ? date('d-m-Y', $event->start_time) : ''; ?></td>
                                                <td><?php echo !empty($event->end_time) ? date('d-m-Y', $event->end_time) : ''; ?></td>
                                                <td>{{ ucfirst($event->country) }}</td>
                                                <td>{{ ucfirst($event->state) }}</td>
                                                <td>{{ ucfirst($event->city) }}</td>
                                                <td style="text-align:center;">
                                                    {{-- @php
                                                        $imagePath = public_path('uploads/banner_image/' . $event->banner_image);
                                                    @endphp --}}
                                                   {{-- file_exists($imagePath) && !empty($event->banner_image) --}}
                                                    @if (!empty($event->banner_image))
                                                        <a target="_blank" title="View Image"
                                                            href="{{ asset('uploads/banner_image/' . $event->banner_image) }}">
                                                            <img style="width:50px;" src="{{ asset('uploads/banner_image/' . $event->banner_image) }}" alt="Logo Image">
                                                        </a>
                                                    @else
                                                    <?php   echo '-'; ?>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="custom-control custom-switch custom-switch-success">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="guest-{{ $event->id }}"
                                                            {{ $event->allow_guest_login ? 'checked' : '' }}
                                                            onclick="guest_login_status(event.target, {{ $event->id }});" />

                                                        <label class="custom-control-label" style="cursor: pointer;"
                                                            for="guest-{{ $event->id }}">

                                                            <span class="switch-icon-left"></span>
                                                            <span class="switch-icon-right"></span>
                                                        </label> 
                                                    </div>
                                                </td>
                                                <td style="text-align:center;">
                                                    <?php  
                                                        if($event->event_info_status == 1) {
                                                            echo '<span style="display: inline-block; padding: 5px 10px; border-radius: 15px; background-color: #b8ffc6; color: #07AE28;font-weight: 500;width: 100%;">Public</span>';
                                                        } else if($event->event_info_status == 2) {
                                                            echo '<span style="display: inline-block; padding: 5px 10px; border-radius: 15px; background-color: #c2e4ff; color: #1B6FB6;font-weight: 500;width: 100%;">Private</span>';
                                                        } else if($event->event_info_status == 3) {
                                                            echo '<span style="display: inline-block; padding: 5px 10px; border-radius: 15px; background-color: #ffe6bf; color: #E28A00;font-weight: 500;width: 100%;">Draft</span>';
                                                        }
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="custom-control custom-switch custom-switch-success">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="verify-{{ $event->id }}"
                                                            {{ $event->is_verify ? 'checked' : '' }}
                                                            onclick="change_verify_status(event.target, {{ $event->id }});" />

                                                        <label class="custom-control-label" style="cursor: pointer;"
                                                            for="verify-{{ $event->id }}">
                                                            <span class="switch-icon-left"></span>
                                                            <span class="switch-icon-right"></span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="custom-control custom-switch custom-switch-success">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="status-{{ $event->id }}"
                                                            {{ $event->active ? 'checked' : '' }}
                                                            onclick="change_status(event.target, {{ $event->id }});" />

                                                        <label class="custom-control-label" style="cursor: pointer;"
                                                            for="status-{{ $event->id }}">
                                                            <span class="switch-icon-left"></span>
                                                            <span class="switch-icon-right"></span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td style="text-align: center;">
                                                    <a href={{ url('participants_event', $event->id) }}> 
                                                        <i class="fa fa-users btn btn-success btn-sm "  title="Participants event"></i>
                                                    </a>
                                                    <a href={{ url('/registration_successful', $event->id) }}> 
                                                        <i class="fa fa fa-user btn btn-success btn-sm "  title="Registration successful"></i>
                                                    </a>
                                                </td>
                                                <!-- <td>{{ ucfirst($event->state) }}</td>
                                                <td style="text-align: center;">{{ strtoupper($event->country) }}</td> -->
                                                


                                                <!-- <td style="text-align: center;">
                                                    <a href="{{ url('event/edit', $event->id) }}" title="Edit" class=""><i class="fa fa-edit"
                                                            style="color: green; font-size:20px;"></i></a> |
                                                    <a class="cursor-pointer" title="Delete"><i class="fa fa-trash"
                                                            onClick="remove_event({{ $event->id }})"
                                                            style="color: red; font-size:20px;"></i></a>
                                                </td>  -->

                                                <td style="text-align: center;">
                                                    <a href="{{ url('event/edit', $event->id) }}"><i
                                                            class="fa fa-edit btn btn-primary btn-sm"
                                                            title="Edit"></i></a>
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                        onclick="remove_event({{ $event->id }})" title="Delete"></i>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="16" style="text-align: center; color:red;">No record found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-end">
                                {{ $Paginator->links() }}
                            </div>
                        </div>
                    </div>
    </section>

    <div class="flex-grow-1"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script>
        function remove_event(iId) {
            //alert(iId);
            var url = '<?php echo url('/event/remove_event'); ?>';

            url = url + '/' + iId;
            // alert(url);
            Confirmation = confirm('Are you sure you want to remove this record ?');
            if (Confirmation) {

                window.location.href = url;

            }
        }
        // alert('here');
        function change_verify_status(_this, id) {
            var is_verify = $(_this).prop('checked') == true ? 1 : 0;

            if (confirm("Are you sure you change this status?")) {
                $.ajax({
                    url: "<?php echo url('event/change_verify_status'); ?>",
                    type: 'post',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        is_verify: is_verify
                    },
                    success: function(result) {
                    if (result.sucess == 'true') {
                        // console.log(result);
                        // alert(result.message); 
                        $("#success-message").text(result.message); // Update success message
                        $("#success-alert").show(); // Show the success alert
                        // Optionally hide the alert after a few seconds
                        setTimeout(function() {
                            $("#success-alert").fadeOut();
                        }, 5000); // Adjust time (2000 = 2 seconds)

                    }else{
                        alert('Some error occured');
                        if(status)
                            $(_this).prop("checked" , false)
                        else
                            $(_this).prop("checked" , true)
                            return false;
                    }
                },
                    error: function() {
                        alert('Some error occurred');
                    }
                });
            } else {
                $(_this).prop("checked", !is_verify);
            }
        }


        function change_status(_this, id) {
            var active = $(_this).prop('checked') == true ? 1 : 0;

            if (confirm("Are you sure you change this status?")) {
                $.ajax({
                    url: "<?php echo url('event/change_status'); ?>",
                    type: 'post',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        active: active
                    },
                    success: function(result) {
                    if (result.sucess == 'true') {
                        // console.log(result);
                        // alert(result.message); 
                        $("#success-message").text(result.message); // Update success message
                        $("#success-alert").show(); // Show the success alert
                        // Optionally hide the alert after a few seconds
                        setTimeout(function() {
                            $("#success-alert").fadeOut();
                        }, 5000); // Adjust time (2000 = 2 seconds)

                    }else{
                        alert('Some error occured');
                        if(status)
                            $(_this).prop("checked" , false)
                        else
                            $(_this).prop("checked" , true)
                            return false;
                    }
                },
                    error: function() {
                        alert('Some error occurred');
                    }
                });
            } else {
                $(_this).prop("checked", !active);
            }
        }

       
        function guest_login_status(_this, id) {
            var allow_guest_login = $(_this).prop('checked') == true ? 1 : 0;
            //alert(allow_guest_login);

            if (confirm("Are you sure you want to change this status in guest login?")) {
                $.ajax({
                    url: "<?php echo url('event/guest_login_status'); ?>",
                    type: 'post',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        guest_login: allow_guest_login
                    },
                    success: function(result) {
                    if (result.sucess == 'true') {
                       
                        $("#success-message").text(result.message); // Update success message
                        $("#success-alert").show(); // Show the success alert
                        // Optionally hide the alert after a few seconds
                        setTimeout(function() {
                            $("#success-alert").fadeOut();
                        }, 5000); // Adjust time (2000 = 2 seconds)

                    }else{
                        alert('Some error occured');
                        if(status)
                            $(_this).prop("checked" , false)
                        else
                            $(_this).prop("checked" , true)
                            return false;
                    }
                },
                    error: function() {
                        alert('Some error occurred');
                    }
                });
            } else {
                $(_this).prop("checked", !allow_guest_login);
            }
        }


        $(document).ready(function() {
        var CountryId = '<?php echo old('event_country', $search_event_country); ?>';
        var StateId = '<?php echo old('event_state', $search_event_state); ?>';
        var CityId = '<?php echo old('event_city', $search_event_city); ?>';
        var baseUrl = "{{ config('custom.app_url') }}";
        // Fetch states based on the selected country
        if (CountryId !== '') {
            $.ajax({
                url: baseUrl +'/get_states', // Replace with your URL to fetch states
                type: 'GET',
                data: { country_id: CountryId },
                success: function(states) {
                    $('#state').empty().append('<option value="">Select State</option>');
                    $.each(states, function(key, value) {
                        $('#state').append('<option value="'+ value.id +'" '+ (StateId == value.id ? 'selected' : '') +'>'
                            + value.name +'</option>');
                    });

                    // Fetch cities based on the selected state
                    if (StateId !== '') {
                        $.ajax({
                            url: baseUrl +'/get_cities', // Replace with your URL to fetch cities
                            type: 'GET',
                            data: { state_id: StateId },
                            success: function(cities) {
                                $('#city').empty().append('<option value="">Select City</option>');
                                $.each(cities, function(key, value) {
                                    $('#city').append('<option value="'+ value.id +'" '+ (CityId == value.id ? 'selected' : '') +'>'
                                        + value.name +'</option>');
                                });
                            }
                        });
                    }
                }
            });
        }

        // Handle country change
        $('#country').change(function() {
            var countryId = $(this).val();
            $.ajax({
                url: baseUrl +'/get_states',
                type: 'GET',
                data: { country_id: countryId },
                success: function(states) {
                    $('#state').empty().append('<option value="">Select State</option>');
                    $.each(states, function(key, value) {
                        $('#state').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                    $('#city').empty().append('<option value="">Select City</option>'); // Clear cities
                }
            });
        });

        // Handle state change
        $('#state').change(function() {
            var stateId = $(this).val();
            $.ajax({
                url: baseUrl +'/get_cities',
                type: 'GET',
                data: { state_id: stateId },
                success: function(cities) {
                    $('#city').empty().append('<option value="">Select City</option>');
                    $.each(cities, function(key, value) {
                        $('#city').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                }
            });
        });
    });
    </script>
    <script>
        function setEndDateMin() {
            const startDateInput = document.getElementById('event_start_date');
            const endDateInput = document.getElementById('event_end_date');
            const startDate = startDateInput.value;
    
            if (startDate) {
                endDateInput.setAttribute('min', startDate);
                if (endDateInput.value && endDateInput.value < startDate) {
                    endDateInput.value = '';
                }
            }
        }
    </script>
@endsection
