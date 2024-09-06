@extends('layout.index')
@if (!empty($id))
    @section('title', ' Marketing ')
@else
    @section('title', ' Marketing ')
@endif

@section('title', 'Marketing Create')
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
                                                Edit Marketing Details
                                            @else
                                                Add Marketing Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Marketing</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($aReturn['id']))
                                                Edit Marketing 
                                            @else
                                                Add Marketing
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
                                    <input type="hidden" name="form_type" value="add_edit_marketing"
                                        enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                  
                                    <div class="row">
                                        <div class="col-sm-6 col-12">
                                            <label for="form-control"> Event <span style="color:red;">*</span></label>
                                            <select id="event" name="event" class="form-control select2 form-control">
                                                <option value="">Select  Event</option>
                                                <?php 
                                                    foreach ($EventsData as $value)
                                                    {
                                                        $selected = '';
                                                        if(old('event',$event_id) == $value->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo ucfirst($value->name); ?></option>
                                                        <?php 
                                                    }
                                                ?>
                                            </select>
                                            <h5><small class="text-danger" id="event_err"></small></h5>
                                                @error('event')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="campaign_name">Campaign Name <span style="color:red;">*</span></label>
                                                <input type="text" id="campaign_name" class="form-control"
                                                    placeholder="Campaign Name" name="campaign_name"  value="{{ old('campaign_name', $campaign_name) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="campaign_name_err"></small></h5>
                                                @error('campaign_name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="campaign_type">Campaign Type <span style="color:red;">*</span></label>
                                                <?php 
                                                $Campaign_Types  = array('Email', 'Whatsapp', 'SMS', 'Social Media(Text)', 'Ad Campaign (Text)' );
                                                ?>
                                                <select id="campaign_type" name="campaign_type" class="select2 form-control">
                                                    <option value="">Select Campaign Type</option>
                                                    <?php 
                                                    foreach ($Campaign_Types as $key => $value)
                                                    {
                                                        // old('position',$position)
                                                        $selected = '';
                                                        if(old('campaign_type',$campaign_type ) == $value){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo ucfirst($value); ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                                                </select>
                                                    <h5><small class="text-danger" id="campaign_type_err"></small></h5>
                                                @error('campaign_type')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="count">Count <span style="color:red;">*</span></label>
                                                <input type="number" id="count" class="form-control"
                                                    placeholder="Count" name="count"  value="{{ old('count', $count) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="count_err"></small></h5>
                                                @error('count')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="start_date">Campaign Start Date <span style="color:red;">*</span></label>
                                                <input type="date" id="start_date" class="form-control"
                                                    placeholder="Start Date" name="start_date"
                                                    value="{{ old('start_date', $start_date ? \Carbon\Carbon::parse($start_date)->format('Y-m-d') : '') }}" 
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="start_date_err"></small></h5>
                                                @error('start_date')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="end_date">Campaign End Date <span style="color:red;">*</span></label>
                                                <input type="date" id="end_date" class="form-control"
                                                    placeholder="End Date" name="end_date"
                                                    value="{{ old('end_date', $end_date ? \Carbon\Carbon::parse($end_date)->format('Y-m-d') : '') }}"  
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="end_date_err"></small></h5>
                                                @error('end_date')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/marketing') }}" type="reset"
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


