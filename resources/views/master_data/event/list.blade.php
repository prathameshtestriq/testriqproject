@extends('layout.index')
@section('title', 'Event List')

@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Event List</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Events</a></li>
                        <li class="breadcrumb-item active">Event List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
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
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <input type="text" id="name" class="form-control" placeholder="Event Name"
                                                name="name" value="{{ $search_name }}" autocomplete="off" />
                                        </div>

                                        <div class="col-sm-6">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                            @if ($search_name)
                                            <a title="Clear" href="{{ url('event/clear_search') }}" type="button"
                                                class="btn btn-outline-primary">
                                                <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <a href="{{  url('/event/add') }}" class="btn btn-outline-primary float-right">
                                        <i data-feather="plus"></i><span>Add Event</span></a>
                                </div>
                            </div>
                        </div>

                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Sr No.</th>
                                    <th>Event Name</th>
                                    <th>Event Start Time</th>
                                    <th>Event End Time</th>
                                    <th>City</th>
                                    <!-- <th>State</th>
                                    <th style="text-align: center;">Country</th> -->
                                    <th style="text-align: center;">Active</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($event_array))
                                @foreach ($event_array as $key => $event)
                                <tr>
                                    <td style="text-align: center;">{{ $key + 1 }}</td>
                                    <td>{{ ucfirst($event->name) }}</td>
                                    <td>{{ date('Y-m-d H:i:s', $event->start_time) }}</td>
                                    <td>{{ date('Y-m-d H:i:s', $event->end_time) }}</td>
                                    <td>{{ ucfirst($event->city) }}</td>
                                    <!-- <td>{{ ucfirst($event->state) }}</td>
                                    <td style="text-align: center;">{{ strtoupper($event->country) }}</td> -->
                                    <td class="text-center">

                                        <div class="custom-control custom-switch custom-switch-success">
                                            <input type="checkbox" class="custom-control-input" id="{{ $event->id }}" {{
            $event->active ? 'checked' : '' }}
                                            onclick="change_status(event.target, {{ $event->id }});" />

                                            <label class="custom-control-label" style="cursor: pointer;" for="{{ $event->id }}">

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
                                                class="fa fa-edit btn btn-primary btn-sm" title="edit"></i></a>
                                        <i class="fa fa-trash-o btn btn-danger btn-sm"
                                            onclick="remove_event({{$event->id }})" title="delete"></i>
                                    </td>
                                </tr>

                                @endforeach
                                @else
                                <tr>
                                    <td colspan="9" style="text-align: center;">No record found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            {{ $Paginator }}
                        </div>
                    </div>
                </div>
</section>

<div class="flex-grow-1"></div>
<script>

    function remove_event(iId) {
        //alert(iId);
        var url = '<?php echo url('/event/remove_event') ?>';

        url = url + '/' + iId;
        // alert(url);
        Confirmation = confirm('Are you sure you want to remove this event');
        if (Confirmation) {

            window.location.href = url;

        }
    }
    // alert('here');

    function change_status(_this, id) {
        var active = $(_this).prop('checked') == true ? 1 : 0;

        if (confirm("Are you sure you change this status?")) {
            $.ajax({
                url: "<?php echo url('event/change_status') ?>",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    active: active
                },
                success: function (result) {
                    console.log(result);
                    if (result == 1) {
                        alert('Status changed successfully');
                    } else {
                        alert('Some error occurr ed');
                    }
                },
                error: function () {
                    alert('Some error occurred');
                }
            });
        } else {
            $(_this).prop("checked", !active);
        }
    }




</script>
@endsection