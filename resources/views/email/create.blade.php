{{-- <?php

if (!empty($edit_data)) {
    $id = $edit_data['id'];
    $banner_name = $edit_data['banner_name'];
    $banner_url = $edit_data['banner_url'];
    $banner_image = $edit_data['banner_image'];
    $start_time = $edit_data['start_time'];
    $end_time = $edit_data['end_time'];
    $country = $edit_data['country'];
    $state = $edit_data['state'];
    $city = $edit_data['city'];
    $active = $edit_data['active'];
} else {
    $id = '';
    $banner_name = '';
    $banner_url = '';
    $banner_image = '';
    $start_time = '';
    $end_time = '';
    $country = '';
    $state = '';
    $city = '';
    $active = '';
}

?> --}}
@extends('layout.index')
@if (!empty($id))
    @section('title', 'Edit Email Details')
@else
    @section('title', 'Add Email Details')
@endif

@section('title', 'Email Create')
<!-- Dashboard Ecommerce start -->
@section('content')
    <section>
        <style>
            .ck-editor__editable {
                min-height: 200px; /* Set the minimum height as needed */
            }
        </style>

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
                                                Edit Email Details
                                            @else
                                                Add Email Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                        <li class="breadcrumb-item">Email</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($id))
                                                Edit Email Details
                                            @else
                                                Add Email Details
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
                                    <input type="hidden" name="form_type" value="add_edit_email_sending">
                                    {{ csrf_field() }}
    

                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="event">Event<span
                                                    style="color:red;">*</span></label>
                                                    <?php $event_name=[];
                                                   if(!empty($event_ids))
                                                   { 
                                                       $event_name=explode(",",$event_ids);
                                                   }
                                                    
                                                   ?>
                                                <select id="event" name="event[]" class="select2 form-control"  <?php if($event_ids!=0) echo 'multiple';?>>
                                                    <option value="">All Event</option>
                                                    <?php  
                                                    foreach ($EventsData as $value)
                                                    {  
                                                        $selected ='';
                                                        if(in_array($value->id,$event_name)){
                                                            $selected = 'selected';
                                                        }else{
                                                            $event = old("event");
                                                            
                                                            if(in_array($value->id, (array)$event)) {
                                                                $selected = 'selected';
                                                            }
                                                        }
                                                        
                                                        ?>
                                                       
                                                        <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->name; ?></option>
                                                        
                                                        <?php 
                                                    }
                                                    ?>
                                                </select>
                                                <h5><small class="text-danger" id="event_err"></small></h5>
                                                @error('event')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                     

                                        <div  class="col-md-6 col-12">
                                            <?php 
                                               $Receivers = ['All User','All Organizer','All Registration','All Participant'];    
                                            ?>
                                            <label for="form-control">Receivers<span
                                                style="color:red;">*</span></label>
                                            <select id="receiver" name="receiver" class="form-control select2 form-control">
                                                <option value="">Select Receiver</option>
                                                <?php 
                                                    foreach ($Receivers as  $value)
                                                    {
                                                        $selected = '';
                                                        if(old('receiver') == $value){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                        <?php 
                                                    }
                                                ?>
                                            </select>
                                            @error('receiver')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                        </div>
                                       
                                        {{-- {{ old('subject', $subject) }} --}}
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="subject">Subject<span
                                                        style="color:red;">*</span></label>
                                                <input type="text" id="subject" class="form-control"
                                                    placeholder="Subject Name" name="subject"
                                                    value="{{ old('subject') }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="subject_err"></small></h5>
                                                @error('subject')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-12"><br>
                                            <label for="password_confirmation m-2">Email Send Date :</label> <br/>
                                            <div class="demo-inline-spacing">
                                                <div class="custom-control custom-radio mt-0">
                                                    <input type="radio" id="customRadio1" name="date"
                                                        class="custom-control-input" value="now_date" onchange="hideshow(event.target.value)"/>
                                                    <label class="custom-control-label" for="customRadio1">Email Send Now</label>
                                                </div>
                                                <div class="custom-control custom-radio mt-0">
                                                    <input type="radio" id="customRadio2" name="date"
                                                        class="custom-control-input" value="shedule_date" onchange="hideshow(event.target.value)"/>
                                                    <label class="custom-control-label" for="customRadio2">Email Shedule Later</label>
                                                </div>
                                            </div>
                                            <h5><small class="text-danger" id="gender_err"></small></h5>
                                            @error('date')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- value="{{ old('date', $date ? \Carbon\Carbon::parse($date)->format('Y-m-d\TH:i:s') : '') }}"  --}}
                                        <div class="col-md-6 col-12">
                                            <div class="form-group email_date " style="display:none;">
                                                <label for="date">Date<span style="color:red;">*</span></label>
                                                <input type="datetime-local" id="date" class="form-control"
                                                    placeholder="Start Date" name="shedulingdate" value="{{ old('date') }}"
                                                 
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="date_err"></small></h5>
                                                @error('shedulingdate')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                         
                                       
                                        <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="message">message<span
                                                        style="color:red;">*</span></label>
                                                 <textarea name="message" id="message" value="{{ old('message') }}" class="form-control" cols="30" rows="10"></textarea>   
                                                <h5><small class="text-danger" id="message_err"></small></h5>
                                                @error('message')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                      
                                        
                                       
                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/email_sending') }}" type="reset"
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
    <script src={{ asset('/app-assets/js/scripts/Ckeditor/ckeditor.js') }}></script>
    <script>
        ClassicEditor
        .create(document.querySelector('#message'))

        function hideshow(value){
            // alert(value);
            if(value == 'shedule_date'){
                // alert("here");
                $('.email_date').show();
                $('.email_date').next(".select2-container").show();
            }else{
                // alert("here123456");
                $('.email_date').hide();
                $('.email_date').next(".select2-container").hide();
            }
        }

    </script>
@endsection


