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
                                            <div class="col-sm-2">
                                                <label for="form-control">Event Name</label>
                                                <input type="text" id="name" class="form-control"
                                                    placeholder="Event Name" name="name" value="{{ $search_event_name }}"
                                                    autocomplete="off" />
                                            </div>
                                          
                                            <div class="col-sm-2 ">
                                                <label for="form-control">Start Booking Date</label>
                                                <input type="date" id="event_start_date" class="form-control"
                                                    placeholder="Start Date" name="event_start_date"   value="{{ old('start_date', $search_event_start_date ? \Carbon\Carbon::parse($search_event_start_date)->format('Y-m-d') : '') }}"   
                                                    autocomplete="off" />
                                            </div>
                                            
                                            <div class="col-sm-2">
                                                <label for="form-control">End Booking Date</label>
                                                <input type="date" id="event_end_date" class="form-control"
                                                    placeholder="End Date" name="event_end_date" value="{{ old('end_date', $search_event_end_date ? \Carbon\Carbon::parse($search_event_end_date)->format('Y-m-d') : '') }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-2">
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

                                            <div class="col-sm-2 ">
                                                <label for="form-control">State</label>
                                                <select id="state" name="event_state" class="select2 form-control">
                                                    <option value="">All state</option>
                                                </select>  
                                            
                                            </div>
    
                                            <div class="col-sm-2">
                                                <label for="form-control">City</label>
                                                <select id="city" name="event_city" class="select2 form-control">
                                                    <option value="">All City</option>
                                                </select>  
                                            </div>

                                            <div class="col-sm-2 col-12">
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

                                            <div class="col-sm-2 col-12">
                                                <label for="form-control"> Organizer</label>
                                                <select id="organizer" name="organizer" class="form-control select2 form-control">
                                                    <option value="">Select  Organizer</option>
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
 
                                            <div class="col-sm-2 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_event_name)|| !empty($search_event_start_date) || !empty($search_event_end_date) || ($search_event_status != '')|| !empty($search_organizer)|| !empty($search_event_country)|| !empty($search_event_state)|| !empty($search_event_city))
                                                    <a title="Clear" href="{{ url('event/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
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

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr> 
                                        <th style="text-align: center;">Sr No.</th>
                                        <th>Event Name</th>
                                        <th>Event Start Date</th>
                                        <th>Event End Date</th>
                                        <th>Country</th>
                                        <th>State</th>
                                        <th>City</th>
                                        <th style="text-align: center;">View</th>
                                        <th style="text-align: center;">Status</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($event_array))
                                        @foreach ($event_array as $key => $event)
                                            <tr>
                                                <td style="text-align: center;">{{ $key + 1 }}</td>
                                                <td>{{ ucfirst($event->name) }}</td>
                                                <td><?php echo !empty($event->start_time) ? date('d-m-Y', $event->start_time) : ''; ?></td>
                                                <td><?php echo !empty($event->end_time) ? date('d-m-Y', $event->end_time) : ''; ?></td>
                                                <td>{{ ucfirst($event->country) }}</td>
                                                <td>{{ ucfirst($event->state) }}</td>
                                                <td>{{ ucfirst($event->city) }}</td>
                                                <td style="text-align: center;">
                                                    <a href={{ url('participants_event', $event->id) }}> 
                                                        <i class="fa fa-eye btn btn-success btn-sm "  title="Participants event"></i>
                                                    </a>
                                                    <a href={{ url('/registration_successful', $event->id) }}> 
                                                        <i class="fa fa-eye btn btn-success btn-sm "  title="Registration successful"></i>
                                                    </a>
                                                </td>
                                                <!-- <td>{{ ucfirst($event->state) }}</td>
                                                <td style="text-align: center;">{{ strtoupper($event->country) }}</td> -->
                                                <td class="text-center">

                                                    <div class="custom-control custom-switch custom-switch-success">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="{{ $event->id }}"
                                                            {{ $event->active ? 'checked' : '' }}
                                                            onclick="change_status(event.target, {{ $event->id }});" />

                                                        <label class="custom-control-label" style="cursor: pointer;"
                                                            for="{{ $event->id }}">

                                                            <span class="switch-icon-left"></span>
                                                            <span class="switch-icon-right"></span>
                                                        </label>
                                                    </div>
                                                </td>


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
                                                            title="edit"></i></a>
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                        onclick="remove_event({{ $event->id }})" title="delete"></i>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="9" style="text-align: center; color:red;">No record found</td>
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
                        console.log(result);
                        if (result == 1) {
                            alert('Status changed successfully');
                        } else {
                            alert('Some error occurr ed');
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



        $(document).ready(function() {
        var CountryId = '<?php echo old('event_country', $search_event_country); ?>';
        var StateId = '<?php echo old('event_state', $search_event_state); ?>';
        var CityId = '<?php echo old('event_city', $search_event_city); ?>';

        // Fetch states based on the selected country
        if (CountryId !== '') {
            $.ajax({
                url: '/get_states', // Replace with your URL to fetch states
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
                            url: '/get_cities', // Replace with your URL to fetch cities
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
                url: '/get_states',
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
                url: '/get_cities',
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
@endsection
