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
        <div class="row">
            <div class="col-md-8">
                <form class="dt_adv_search" action="{{ url('event') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_type" value="search_event">
                    <div class="input-group mb-3">
                        <input type="text" id="name" class="form-control" placeholder="Search by Event Name" name="name"
                            value="{{ $search_name }}" autocomplete="off" />
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if ($search_name)
                        <a title="Clear" href="{{ url('event/clear_search') }}" type="button"
                            class="btn btn-outline-primary">
                            <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                        </a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <a href="{{ url('/event/add') }}" class="btn btn-outline-primary float-end">
                    <i data-feather="plus"></i><span>Add Event</span>
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="text-align: center;">Sr No.</th>
                        <th>Event Name</th>
                        <th>Event Start Time</th>
                        <th>Event End Time</th>
                        <th>City</th>
                        <th>State</th>
                        <th style="text-align: center;">Country</th>
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
                        <td>{{ $event->start_time }}</td>
                        <td>{{ $event->end_time }}</td>
                        <td>{{ ucfirst($event->city) }}</td>
                        <td>{{ ucfirst($event->state) }}</td>
                        <td style="text-align: center;">{{ strtoupper($event->country) }}</td>
                        <td style="text-align: center;">{{ $event->active }}</td>
                        <td style="text-align: center;">
                            <a href="{{ url('event/edit/{id}') }}" title="Edit" class=""><i class="fa fa-edit"
                                    style="color: green; font-size:20px;"></i></a> |
                            <a class="cursor-pointer" title="Delete"><i class="fa fa-trash"
                                    onClick="remove_event({{ $event->id }})"
                                    style="color: red; font-size:20px;"></i></a>
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

@endsection