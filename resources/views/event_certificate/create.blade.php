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
                                                <label class="form-label mt-2">Certificate Name<span style="color:red;">*</span></label>
                                            </div>
                                            <div class="col-sm-3 col-12 ">
                                                <?php $certificate_name=!empty($event_certificate_details[0]->certificate_name)?$event_certificate_details[0]->certificate_name:'';
                                                 ?>
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
                                                <label class="form-label mt-2">Certificate Image<span style="color:red;">*</span></label>
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

                                    {{-- firstname --}}
                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">First Name <span style="color:red;">*</span></label>
                                                <input type="hidden" name="firstname_field" id="firstname_field" value="FirstName" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <?php $xvalue1=!empty($event_certificate_details[0]->x_coordinate)?$event_certificate_details[0]->x_coordinate:'';?>
                                                <label class="form-label">X Coordinate</label>
                                                <input type="text" id="firstname_x_coordinates" name="firstname_x_coordinate" class="form-control" value="{{ old('firstname_x_coordinate',$xvalue1) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('firstname_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('firstname_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="firstname_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue1=!empty($event_certificate_details[0]->y_coordinate)?$event_certificate_details[0]->y_coordinate:'';?>
                                                <input type="text" id="firstname_y_coordinate" name="firstname_y_coordinate" class="form-control"  value="{{ old('firstname_y_coordinate',$yvalue1) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('firstname_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('firstname_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="firstname_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">FirstName Size</label>
                                                <?php $sizevalue1=!empty($event_certificate_details[0]->text_size)?$event_certificate_details[0]->text_size:'';?>
                                                <input type="text" id="firstname_size" name="firstname_size" class="form-control" value="{{ old('firstname_size',$sizevalue1)}}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('firstname_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('firstname_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="firstname_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">FirstName Color</label>
                                                <?php $colorvalue1=!empty($event_certificate_details[0]->text_color)?$event_certificate_details[0]->text_color:'';?>
                                                <input type="color" id="firstname_color" name="firstname_color" class="form-control" value="{{ old('firstname_color',$colorvalue1) }}" style="width: 115px" >
                                                @if ($errors->has('firstname_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('firstname_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="firstname_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>

                                    {{-- lastname --}}
                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Last Name</label>
                                                <input type="hidden" name="lastname_field" id="lastname_field" value="LastName" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">X Coordinate</label>
                                                <?php $xvalue2=!empty($event_certificate_details[1]->x_coordinate)?$event_certificate_details[1]->x_coordinate:'';?>
                                                <input type="text" id="lastname_x_coordinate" name="lastname_x_coordinate" class="form-control" value="{{ old('lastname_x_coordinate',$xvalue2) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('lastname_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('lastname_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="lastname_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue2=!empty($event_certificate_details[1]->y_coordinate)?$event_certificate_details[1]->y_coordinate:'';?>
                                                <input type="text" id="lastname_y_coordinate" name="lastname_y_coordinate" class="form-control" value="{{ old('lastname_y_coordinate',$yvalue2)  }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('lastname_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('lastname_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="lastname_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Lastname Size</label>
                                                <?php $sizevalue2=!empty($event_certificate_details[1]->text_size)?$event_certificate_details[1]->text_size:'';?>
                                                <input type="text" id="lastname_size" name="lastname_size" class="form-control" value="{{ old('lastname_size',$sizevalue2) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('lastname_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('lastname_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="lastname_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Lastname Color</label>
                                                <?php $colorvalue2=!empty($event_certificate_details[1]->text_color)?$event_certificate_details[1]->text_color:'';?>
                                                <input type="color" id="lastname_color" name="lastname_color" class="form-control" value="{{ old('lastname_color',$colorvalue2) }}" style="width: 115px">
                                                @if ($errors->has('lastname_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('lastname_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="lastname_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>

                                    {{-- Option1 --}}
                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">option 1</label>
                                                <input type="hidden" name="option1_field" id="option1_field" value="Option1" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">X Coordinate</label>
                                                <?php $xvalue3=!empty($event_certificate_details[2]->x_coordinate)?$event_certificate_details[2]->x_coordinate:'';?>
                                                <input type="text" id="option1_x_coordinate" name="option1_x_coordinate" class="form-control" value="{{ old('option1_x_coordinate',$xvalue3)}}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" >
                                                @if ($errors->has('option1_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option1_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option1_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue3=!empty($event_certificate_details[2]->y_coordinate)?$event_certificate_details[2]->y_coordinate:'';?>
                                                <input type="text" id="option1_y_coordinate" name="option1_y_coordinate" class="form-control" value="{{ old('option1_y_coordinate',$yvalue3) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option1_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option1_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option1_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Option1 Size</label>
                                                <?php $sizevalue3=!empty($event_certificate_details[2]->text_size)?$event_certificate_details[2]->text_size:'';?>
                                                <input type="text" id="option1_size" name="option1_size" class="form-control" value="{{ old('option1_size',$sizevalue3) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option1_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option1_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option1_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Option1 Color</label>
                                                <?php $colorvalue3=!empty($event_certificate_details[2]->text_color)?$event_certificate_details[2]->text_color:'';?>
                                                <input type="color" id="option1_color" name="option1_color" class="form-control" value="{{ old('option1_color',$colorvalue3) }}" style="width: 115px">
                                                @if ($errors->has('option1_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option1_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option1_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>

                                    {{-- option2 --}}
                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Option2</label>
                                                <input type="hidden" name="option2_field" id="option2_field" value="Option2" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">X Coordinate</label>
                                                <?php $xvalue4=!empty($event_certificate_details[3]->x_coordinate)?$event_certificate_details[3]->x_coordinate:'';?>
                                                <input type="text" id="option2_x_coordinate" name="option2_x_coordinate" class="form-control" value="{{old('option2_x_coordinate',$xvalue4)}}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option2_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option2_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option2_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue4=!empty($event_certificate_details[3]->y_coordinate)?$event_certificate_details[3]->y_coordinate:'';?>
                                                <input type="text" id="option2_y_coordinate" name="option2_y_coordinate" class="form-control" value="{{ old('option2_y_coordinate',$yvalue4) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option2_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option2_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option2_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Option2 Size</label>
                                                <?php $sizevalue4=!empty($event_certificate_details[3]->text_size)?$event_certificate_details[3]->text_size:'';?>
                                                <input type="text" id="option2_size" name="option2_size" class="form-control" value="{{ old('option2_size',$sizevalue4) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option2_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option2_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option2_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <?php $colorvalue4=!empty($event_certificate_details[3]->text_color)?$event_certificate_details[3]->text_color:'';?>
                                                <label class="form-label">Option2 Color</label>
                                                <input type="color" id="option2_color" name="option2_color" class="form-control" value="{{ old('option2_color',$colorvalue4) }}" style="width: 115px">
                                                @if ($errors->has('option2_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option2_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option2_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>


                                    {{-- option3 --}}
                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Option 3</label>
                                                <input type="hidden" name="option3_field" id="option3_field" value="Option3" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">X Coordinate</label>
                                                <?php $xvalue4=!empty($event_certificate_details[4]->x_coordinate)?$event_certificate_details[4]->x_coordinate:'';?>
                                                <input type="text" id="option3_x_coordinate" name="option3_x_coordinate" class="form-control" value="{{old('option3_x_coordinate',$xvalue4)}}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option3_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option3_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option3_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue4=!empty($event_certificate_details[4]->y_coordinate)?$event_certificate_details[4]->y_coordinate:'';?>
                                                <input type="text" id="option3_y_coordinate" name="option3_y_coordinate" class="form-control" value="{{ old('option3_y_coordinate',$yvalue4) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option3_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option3_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option3_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Option3 Size</label>
                                                <?php $sizevalue4=!empty($event_certificate_details[4]->text_size)?$event_certificate_details[4]->text_size:'';?>
                                                <input type="text" id="option3_size" name="option3_size" class="form-control" value="{{ old('option3_size',$sizevalue4) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option3_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option3_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option3_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <?php $colorvalue4=!empty($event_certificate_details[4]->text_color)?$event_certificate_details[4]->text_color:'';?>
                                                <label class="form-label">Option3 Color</label>
                                                <input type="color" id="option3_color" name="option3_color" class="form-control" value="{{ old('option3_color',$colorvalue4) }}" style="width: 115px">
                                                @if ($errors->has('option3_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option3_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option3_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>

                                     {{-- option4 --}}
                                     <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Option 4</label>
                                                <input type="hidden" name="option4_field" id="option4_field" value="Option4" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">X Coordinate</label>
                                                <?php $xvalue2=!empty($event_certificate_details[5]->x_coordinate)?$event_certificate_details[5]->x_coordinate:'';?>
                                                <input type="text" id="option4_x_coordinate" name="option4_x_coordinate" class="form-control" value="{{ old('option4_x_coordinate',$xvalue2) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option4_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option4_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option4_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue2=!empty($event_certificate_details[5]->y_coordinate)?$event_certificate_details[5]->y_coordinate:'';?>
                                                <input type="text" id="option4_y_coordinate" name="option4_y_coordinate" class="form-control" value="{{ old('option4_y_coordinate',$yvalue2)  }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option4_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option4_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option4_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Option4 Size</label>
                                                <?php $sizevalue2=!empty($event_certificate_details[5]->text_size)?$event_certificate_details[5]->text_size:'';?>
                                                <input type="text" id="option4_size" name="option4_size" class="form-control" value="{{ old('option4_size',$sizevalue2) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option4_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option4_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option4_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Option4 Color</label>
                                                <?php $colorvalue2=!empty($event_certificate_details[5]->text_color)?$event_certificate_details[5]->text_color:'';?>
                                                <input type="color" id="option4_color" name="option4_color" class="form-control" value="{{ old('option4_color',$colorvalue2) }}" style="width: 115px">
                                                @if ($errors->has('option4_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option4_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option4_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>

                                    {{-- option5 --}}
                                    <div class="col-md-12 col-12 pl-0">
                                        <div class="row mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label mt-2">Option 5</label>
                                                <input type="hidden" name="option5_field" id="option5_field" value="Option5" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">X Coordinate</label>
                                                <?php $xvalue2=!empty($event_certificate_details[6]->x_coordinate)?$event_certificate_details[6]->x_coordinate:'';?>
                                                <input type="text" id="option5_x_coordinate" name="option5_x_coordinate" class="form-control" value="{{ old('option5_x_coordinate',$xvalue2) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option5_x_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option5_x_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option5_x_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Y Coordinate</label>
                                                <?php $yvalue2=!empty($event_certificate_details[6]->y_coordinate)?$event_certificate_details[6]->y_coordinate:'';?>
                                                <input type="text" id="option5_y_coordinate" name="option5_y_coordinate" class="form-control" value="{{ old('option5_y_coordinate',$yvalue2)  }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option5_y_coordinate'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option5_y_coordinate') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option5_y_coordinate_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Option5 Size</label>
                                                <?php $sizevalue2=!empty($event_certificate_details[6]->text_size)?$event_certificate_details[6]->text_size:'';?>
                                                <input type="text" id="option5_size" name="option5_size" class="form-control" value="{{ old('option5_size',$sizevalue2) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                @if ($errors->has('option5_size'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option5_size') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option5_size_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Option5 Color</label>
                                                <?php $colorvalue2=!empty($event_certificate_details[6]->text_color)?$event_certificate_details[6]->text_color:'';?>
                                                <input type="color" id="option5_color" name="option5_color" class="form-control" value="{{ old('option5_color',$colorvalue2) }}" style="width: 115px">
                                                @if ($errors->has('option5_color'))
                                                    <span class="text-danger" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;">{{ $errors->first('option5_color') }}</span>
                                                @endif
                                                <h5><small class="text-danger" id="option5_color_err" style="font-weight: bold; font-size: 13px;"
                                                    style="font-weight: bold; font-size: 13px;"></small></h5>
                                            </div>
                                        </div>	
                                    </div>

                                    <div class="col-12 text-center mt-1 mb-1">
                                        <button type="submit" name="command" value="Save" class="btn btn-primary" onClick="return event_certificate()">
                                            <span class="align-middle d-sm-inline-block d-none" >Save</span>
                                        </button>
                                        <!-- Preview Button -->
                                        <button type="button" name="preview" class="btn btn-primary" data-bs-target="#previewModal" onclick="previewdata();">
                                            Preview
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

    <script>
        // document.querySelector('[data-bs-target="#previewModal"]').addEventListener('click', function (event) {
        function previewdata(){ 
            const certificate_name = document.getElementById('certificate_name').value;
            const certificate_image = document.getElementById('image').files[0];
            const firstname_field = document.getElementById('firstname_field').value;
            const xCoordinate = document.getElementById('firstname_x_coordinates').value;
            const yCoordinate = document.getElementById('firstname_y_coordinate').value;
            const firstnamesize = document.getElementById('firstname_size').value;
            const firstnamecolor = document.getElementById('firstname_color').value;   
            const formData = new FormData();

            // Collect data from form inputs
            formData.append('certificate_name', document.getElementById('certificate_name').value);
            formData.append('certificate_image', document.getElementById('image').files[0]);
            formData.append('firstname_field', document.getElementById('firstname_field').value);
            formData.append('firstname_x_coordinate', document.getElementById('firstname_x_coordinates').value);
            formData.append('firstname_y_coordinate', document.getElementById('firstname_y_coordinate').value);
            formData.append('firstname_size', document.getElementById('firstname_size').value);
            formData.append('firstname_color', document.getElementById('firstname_color').value);
            formData.append('lastname_field', document.getElementById('lastname_field').value);
            formData.append('lastname_x_coordinate', document.getElementById('lastname_x_coordinate').value);
            formData.append('lastname_y_coordinate', document.getElementById('lastname_y_coordinate').value);
            formData.append('lastname_size', document.getElementById('lastname_size').value);
            formData.append('lastname_color', document.getElementById('lastname_color').value);

            // Add dynamic options
            for (let i = 1; i <= 5; i++) {
                formData.append(`option${i}_field`, document.getElementById(`option${i}_field`).value);
                formData.append(`option${i}_x_coordinate`, document.getElementById(`option${i}_x_coordinate`).value);
                formData.append(`option${i}_y_coordinate`, document.getElementById(`option${i}_y_coordinate`).value);
                formData.append(`option${i}_size`, document.getElementById(`option${i}_size`).value);
                formData.append(`option${i}_color`, document.getElementById(`option${i}_color`).value);
            }

            // Add CSRF token and emailCertificateId
            formData.append('_token', "{{ csrf_token() }}");
            // Debugging: Check the values in the browser console
            var emailCertificateId = "<?php echo !empty($event_certificate_details[0]->email_certificate_id) ? $event_certificate_details[0]->email_certificate_id : ''; ?>";
            var id = "<?php echo !empty($id) ? $id : '0'; ?>";

            // console.log("Email Certificate ID: " + emailCertificateId);
            // console.log("ID: " + id);
            var update_image = "<?php echo !empty($event_certificate_details[0]->image) ? $event_certificate_details[0]->image : "0"; ?>";

            formData.append('emailCertificateId', emailCertificateId);
            formData.append('Id', id);
            formData.append('update_image', update_image);

            let valid = true;
       
            // Check if either of the coordinates is empty
            // || certificate_image === undefined
            if (xCoordinate === '' || yCoordinate === ''|| firstnamesize === '' || firstnamecolor === '' || certificate_name === '' || ((certificate_image === undefined) && (update_image == "0"))) {
                
                // Show error messages if coordinates are empty
                if (certificate_name === '') {
                    document.getElementById('certificate_name_err').innerText = 'Please Enter Certificate Name.';
                    document.getElementById('certificate_name').parentElement.classList.add('has-error');
                }
                // if(empty(update_image)){
                //     document.getElementById('image_err').innerText = 'Please Enter Certificate Image .';
                //     document.getElementById('image').parentElement.classList.add('has-error');
                // }else (certificate_image === undefined ) {
                //     document.getElementById('image_err').innerText = 'Please Enter Certificate Image .';
                //     document.getElementById('image').parentElement.classList.add('has-error');
                // }

                if((certificate_image === undefined) && (update_image === "0")) {
                    document.getElementById('image_err').innerText = 'Please Enter Certificate Image .';
                    document.getElementById('image').parentElement.classList.add('has-error');
                }

                if (xCoordinate === '') {
                    document.getElementById('firstname_x_coordinate_err').innerText = 'Please Enter X Coordinate.';
                    document.getElementById('firstname_x_coordinates').parentElement.classList.add('has-error');
                }
                if (yCoordinate === '') {
                    document.getElementById('firstname_y_coordinate_err').innerText = 'Please Enter Y Coordinate.';
                    document.getElementById('firstname_y_coordinate').parentElement.classList.add('has-error');
                }
                if (firstnamesize === '') {
                    document.getElementById('firstname_size_err').innerText = 'Please Enter First Name size.';
                    document.getElementById('firstname_size').parentElement.classList.add('has-error');
                }
                if (firstnamecolor === '') {
                    document.getElementById('firstname_color_err').innerText = 'Please Enter First Name Color.';
                    document.getElementById('firstname_color').parentElement.classList.add('has-error');
                }
                valid = false;
            } else {
                 // If fields are valid, validate file
                if (certificate_image && update_image) {
                    const fileSize = certificate_image.size;
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                    // File size validation
                    if (fileSize > 10 * 1024 * 1024) {
                        document.getElementById('image_err').innerText = 'File size must be below 10 MB.';
                        document.getElementById('image').parentElement.classList.add('has-error');
                        valid = false;
                    }

                    // File type validation
                    if (!allowedTypes.includes(certificate_image.type)) {
                        document.getElementById('image_err').innerText = 'Only JPG, JPEG, and PNG files are allowed.';
                        document.getElementById('image').parentElement.classList.add('has-error');
                        valid = false;
                    }
                }

                if (valid) {
                    // Remove error messages if coordinates are valid
                    document.getElementById('firstname_x_coordinate_err').innerText = '';
                    document.getElementById('firstname_y_coordinate_err').innerText = '';
                    document.getElementById('firstname_size_err').innerText = '';
                    document.getElementById('firstname_color_err').innerText = '';
                    document.getElementById('certificate_name_err').innerText = '';
                    document.getElementById('image_err').innerText = '';
                    document.getElementById('firstname_x_coordinates').parentElement.classList.remove('has-error');
                    document.getElementById('firstname_y_coordinate').parentElement.classList.remove('has-error');
                    document.getElementById('firstname_size').parentElement.classList.remove('has-error');
                    document.getElementById('firstname_color').parentElement.classList.remove('has-error');
                    document.getElementById('certificate_name').parentElement.classList.remove('has-error');
                    document.getElementById('image').parentElement.classList.remove('has-error');
                }    
            }
        
            // If coordinates are valid, show the modal
            if (valid) {
                $.ajax({
                    url: "<?php echo url('event_certificate/preview_data') ?>",
                    type: 'POST',
                    processData: false, // Necessary for FormData
                    contentType: false, // Necessary for FormData
                    data: formData,
                    success: function (result) {
                        
                        if (result == 1) {
                            alert('Data submitted successfully');
                        } else {
                            alert('Some error occurred');
                        }
                    },
                    error: function () {
                        alert('Some error occurred');
                    }
                });
            }
        }

       
        
     
    </script>
@endsection



