@extends('layout.index')
@if (!empty($id))
    @section('title', 'Event Certificate  ')
@else
    @section('title', ' Event Certificate  ')
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
                                            @if (!empty($id))
                                                Edit Event Certificate  Details
                                            @else
                                                Add Event Certificate  Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div> 
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Advertisement</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($id))
                                                Edit Event Certificate Details
                                            @else
                                                Add Event Certificate Details
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
                                <form class="form" action="" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="form_type" value="add_edit_event_certificate">

                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Event<span style="color:red;">*</span></label>
                                            </div>
                                            <div class="col-sm-3 col-12 ">
                                                <?php $event_id=!empty($event_certificate_details[0]->event_id)?$event_certificate_details[0]->event_id:''; ?>
                                              
                                                <select id="event_id" name="event_id" class="form-control select2 form-control">
                                                    <option value="">Select  Event</option>
                                                    <?php 
                                                        foreach ($EventsData as $value)
                                                        {
                                                            $selected = '';
                                                            if(old('event_id',$event_id) == $value->id){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo ucfirst($value->name); ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                                @if ($errors->has('event_id'))
                                                <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                style="font-weight: bold; font-size: 13px;">{{ $errors->first('event_id') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="event_id_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>  
                                        </div>    
                                    </div>

                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Certificate Name<span style="color:red;">*</span></label>
                                            </div>
                                            <div class="col-sm-3 col-12 ">
                                                <?php $certificate_name=!empty($event_certificate_details[0]->certificate_name)?$event_certificate_details[0]->certificate_name:''; ?>
                                                <input type="text" id="certificate_name" class="form-control"
                                                    placeholder=" Certificate Name" name="certificate_name"
                                                    value="{{ old('certificate_name', $certificate_name) }}"  autocomplete="off" />
                                                 
                                                @if ($errors->has('certificate_name'))
                                                <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                style="font-weight: bold; font-size: 13px;">{{ $errors->first('certificate_name') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="certificate_name_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>  
                                        </div>    
                                    </div>

                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Image<span style="color:red;">*</span></label>
                                                <input type="hidden" name="image_field" id="" value="image" class="form-control">
                                            </div>
                                            <div class="col-md-5">
                                                <?php $value=!empty($event_certificate_details[0]->image)?$event_certificate_details[0]->image:'';?>
                                                <span style="color: #949090">(Allowed JPEG, JPG or PNG. Max file size of 10 MB)</span>
                                                <input type="file" id="image" name="image" class="form-control mt-2"  onchange="previewImage(this)">
                                                {{-- value={{ $value }} --}}
                                                @if ($errors->has('image'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;">{{ $errors->first('image') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="image_err" style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>

                                            <div class="col-md-2 col-12">
                                                <span><br /></span>
                                                <!-- Image preview section -->
                                                <div id="imagePreview">
                                                    <?php if(!empty($event_certificate_details[0]->image)){ ?>
                                                        <a href="{{ asset('uploads/Event_certificate/' . $event_certificate_details[0]->image) }}" target="_blank">
                                                            <img id="preview" src="{{ asset('uploads/Event_certificate/' . $event_certificate_details[0]->image) }}" alt="Current Image" style="width:100px;height:50px">
                                                        </a>
                                                        <input type="hidden" name="hidden_image" value="{{ old('img', $value) }}" accept="image/jpeg, image/png">
                                                    <?php } else { ?>
                                                        <img id="preview" class="preview-image" src="#" alt="Image Preview" style="display:none; width: 100px;">
                                                    <?php } ?>
                                                </div>    
                                            </div>

                                           
                                        </div>
                                        
                                    </div>

                                    {{-- name --}}
                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Name <span style="color:red;">*</span></label>
                                                <input type="hidden" name="name_field" id="" value="Name" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                {{-- <?php //dd($event_certificate_details); ?> --}}
                                                <?php $xvalue1=!empty($event_certificate_details[1]->x_coordinate)?$event_certificate_details[1]->x_coordinate:'';?>
                                                <label class="form-label">X Coordinate</label>
                                                <input type="text" id="name_x_coordinates" name="name_x_coordinate" class="form-control" value="{{ old('name_x_coordinate',$xvalue1) }}">
                                                @if ($errors->has('name_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('name_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="name_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue1=!empty($event_certificate_details[1]->y_coordinate)?$event_certificate_details[1]->y_coordinate:'';?>
                                                <input type="text" id="name_y_coordinate" name="name_y_coordinate" class="form-control"  value="{{ old('name_y_coordinate',$yvalue1) }}">
                                                @if ($errors->has('name_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('name_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="name_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Name Size</label>
                                                <?php $sizevalue1=!empty($event_certificate_details[1]->text_size)?$event_certificate_details[1]->text_size:'';?>
                                                <input type="text" id="name_size" name="name_size" class="form-control" value="{{ old('name_size',$sizevalue1)}}">
                                                @if ($errors->has('name_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('name_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="name_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Name Color</label>
                                                <?php $colorvalue1=!empty($event_certificate_details[1]->text_color)?$event_certificate_details[1]->text_color:'';?>
                                                <input type="text" id="name_color" name="name_color" class="form-control" value="{{ old('name_color',$colorvalue1) }}">
                                                @if ($errors->has('name_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('name_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="name_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>

                                    {{-- timing --}}
                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Timing</label>
                                                <input type="hidden" name="timing_field" id="" value="Timing" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">X Coordinate</label>
                                                <?php $xvalue2=!empty($event_certificate_details[2]->x_coordinate)?$event_certificate_details[2]->x_coordinate:'';?>
                                                <input type="text" id="timing_x_coordinate" name="timing_x_coordinate" class="form-control" value="{{ old('timing_x_coordinate',$xvalue2) }}">
                                                @if ($errors->has('timing_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('timing_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="timing_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue2=!empty($event_certificate_details[2]->y_coordinate)?$event_certificate_details[2]->y_coordinate:'';?>
                                                <input type="text" id="timing_y_coordinate" name="timing_y_coordinate" class="form-control" value="{{ old('timing_y_coordinate',$yvalue2)  }}">
                                                @if ($errors->has('timing_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('timing_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="timing_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Timing Size</label>
                                                <?php $sizevalue2=!empty($event_certificate_details[2]->text_size)?$event_certificate_details[2]->text_size:'';?>
                                                <input type="text" id="timing_size" name="timing_size" class="form-control" value="{{ old('timing_size',$sizevalue2) }}">
                                                @if ($errors->has('timing_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('timing_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="timing_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Timing Color</label>
                                                <?php $colorvalue2=!empty($event_certificate_details[2]->text_color)?$event_certificate_details[2]->text_color:'';?>
                                                <input type="text" id="timing_color" name="timing_color" class="form-control" value="{{ old('timing_color',$colorvalue2) }}">
                                                @if ($errors->has('timing_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('timing_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="timing_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>

                                    {{-- days --}}
                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Days</label>
                                                <input type="hidden" name="days_field" id="" value="Days" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">X Coordinate</label>
                                                <?php $xvalue3=!empty($event_certificate_details[3]->x_coordinate)?$event_certificate_details[3]->x_coordinate:'';?>
                                                <input type="text" id="days_x_coordinate" name="days_x_coordinate" class="form-control" value="{{ old('days_x_coordinate',$xvalue3)}}">
                                                @if ($errors->has('days_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('days_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="days_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue3=!empty($event_certificate_details[3]->y_coordinate)?$event_certificate_details[3]->y_coordinate:'';?>
                                                <input type="text" id="days_y_coordinate" name="days_y_coordinate" class="form-control" value="{{ old('days_y_coordinate',$yvalue3) }}">
                                                @if ($errors->has('days_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('days_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="days_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Days Size</label>
                                                <?php $sizevalue3=!empty($event_certificate_details[3]->text_size)?$event_certificate_details[3]->text_size:'';?>
                                                <input type="text" id="days_size" name="days_size" class="form-control" value="{{ old('days_size',$sizevalue3) }}">
                                                @if ($errors->has('days_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('days_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="days_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Days Color</label>
                                                <?php $colorvalue3=!empty($event_certificate_details[3]->text_color)?$event_certificate_details[3]->text_color:'';?>
                                                <input type="text" id="days_color" name="days_color" class="form-control" value="{{ old('days_color',$colorvalue3) }}">
                                                @if ($errors->has('days_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('days_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="days_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>

                                    {{-- distance --}}
                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Distance</label>
                                                <input type="hidden" name="distance_field" id="" value="Distance" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">X Coordinate</label>
                                                <?php $xvalue4=!empty($event_certificate_details[4]->x_coordinate)?$event_certificate_details[4]->x_coordinate:'';?>
                                                <input type="text" id="distance_x_coordinate" name="distance_x_coordinate" class="form-control" value="{{old('distance_x_coordinate',$xvalue4)}}">
                                                @if ($errors->has('distance_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('distance_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="distance_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue4=!empty($event_certificate_details[4]->y_coordinate)?$event_certificate_details[4]->y_coordinate:'';?>
                                                <input type="text" id="distance_y_coordinate" name="distance_y_coordinate" class="form-control" value="{{ old('distance_y_coordinate',$yvalue4) }}">
                                                @if ($errors->has('distance_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('distance_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="distance_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Distance Size</label>
                                                <?php $sizevalue4=!empty($event_certificate_details[4]->text_size)?$event_certificate_details[4]->text_size:'';?>
                                                <input type="text" id="distance_size" name="distance_size" class="form-control" value="{{ old('distance_size',$sizevalue4) }}">
                                                @if ($errors->has('distance_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('distance_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="distance_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <?php $colorvalue4=!empty($event_certificate_details[4]->text_color)?$event_certificate_details[4]->text_color:'';?>
                                                <label class="form-label">Distance Color</label>
                                                <input type="text" id="distance_color" name="distance_color" class="form-control" value="{{ old('distance_color',$colorvalue4) }}">
                                                @if ($errors->has('distance_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('distance_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="distance_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>


                                    {{-- image --}}
                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Image</label>
                                                <input type="hidden" name="image_field1" id="" value="Image" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">X Coordinate</label>
                                                <?php $xvalue4=!empty($event_certificate_details[5]->x_coordinate)?$event_certificate_details[5]->x_coordinate:'';?>
                                                <input type="text" id="image_x_coordinate" name="image_x_coordinate" class="form-control" value="{{old('image_x_coordinate',$xvalue4)}}">
                                                @if ($errors->has('image_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('image_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="image_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue4=!empty($event_certificate_details[5]->y_coordinate)?$event_certificate_details[5]->y_coordinate:'';?>
                                                <input type="text" id="image_y_coordinate" name="image_y_coordinate" class="form-control" value="{{ old('image_y_coordinate',$yvalue4) }}">
                                                @if ($errors->has('image_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('image_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="image_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Image Size</label>
                                                <?php $sizevalue4=!empty($event_certificate_details[5]->text_size)?$event_certificate_details[5]->text_size:'';?>
                                                <input type="text" id="image_size" name="image_size" class="form-control" value="{{ old('image_size',$sizevalue4) }}">
                                                @if ($errors->has('image_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('image_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="image_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <?php $colorvalue4=!empty($event_certificate_details[5]->text_color)?$event_certificate_details[5]->text_color:'';?>
                                                <label class="form-label">Image Color</label>
                                                <input type="text" id="image_color" name="image_color" class="form-control" value="{{ old('image_color',$colorvalue4) }}">
                                                @if ($errors->has('image_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('image_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="image_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>

                                    <div class="col-12 text-center mt-1 mb-1">
                                        <button type="submit" name="command" value="Save" class="btn btn-primary" onClick="return event_certificate()">
                                            <span class="align-middle d-sm-inline-block d-none" >Save</span>
                                        </button>
                                        <a href="{{ url('/event_certificate') }}" type="reset"
                                        class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>
   
    <script>
        function previewImage(input) {
            var file = input.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById('preview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }
      
        	
	</script>
@endsection



