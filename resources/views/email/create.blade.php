
@extends('layout.index')
@if (!empty($id))
    @section('title', ' Emails ')
@else
    @section('title', ' Emails ')
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
                                        <li class="breadcrumb-item">Home</li>
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
                                        <div  class="col-md-6 col-12">
                                            <?php 
                                            //    $Email_Type = array(1=>'All ',2=>'Email',3=>'Upload CSV' ); 
                                            $Email_Type = array(1=>'Select Filter ',2=>'Manual Emails',3=>'Upload CSV' ); 
                                            ?>
                                            <label for="form-control">Email Type <span
                                                style="color:red;">*</span></label>
                                            <select id="email_type" name="email_type" class="form-control select2 form-control" onchange="hideshow_event_receiver(event.target.value)">
                                                <option value="">Select Type</option>
                                                <?php 
                                                    foreach ($Email_Type as  $key => $value)
                                                    {
                                                        $selected = '';
                                                        if(old('email_type') == $key){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                        <?php 
                                                    }
                                                ?>
                                            </select>
                                            @error('email_type')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div  class="col-md-6 col-12">
                                        </div>

                                        <div  class="col-md-6 col-12 receiver mt-2"  style="display:none;">
                                            <?php 
                                            //    $Receivers = ['All User','All Organizer','All Registration','All Participant'];  
                                            $Receivers = ['All Organizer','All Registration','All Participant'];      
                                            ?>
                                            <label for="form-control">Receivers <span
                                                style="color:red;">*</span></label>
                                            <select id="receiver" name="receiver" class="form-control select2 form-control"  onchange="hideshow_event(event.target.value)" >
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
                                        <div  class="col-md-6 col-12 Organizer" style="display:none;">
                                        </div>

                                        <div class="col-md-6 col-12 event mt-2" style="display:none;">
                                            <div class="form-group ">
                                                <label for="event">Event
                                                     <span style="color:red;">*</span>
                                                </label>
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

                                       
                                        <div class="col-md-6 col-12 email mt-2" style="display:none;">
                                            <div class="form-group ">
                                                <label for="email">Email Address <span
                                                        style="color:red;">*</span></label>
                                                 <textarea name="email" id="email" value="{{ old('email') }}" class="form-control" cols="1" rows="1"></textarea>   
                                                <h5><small class="text-danger" id="email_err"></small></h5>
                                                @error('email')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-6 col-12 email" style="display:none;">
                                        </div>

                                        <div class="col-md-6 col-12 email_file mt-2" style="display:none;">
                                            <div class="form-group ">
                                                <label for="email_file"> Email Data Import:<span
                                                        style="color:red;">*</span></label>
                                                    <input type="file" id="email_file" name="email_file" class="form-control">
                                                    <input type="hidden" name="email_id">                  
                                                <h5><small class="text-danger" id="email_file_err"></small></h5>
                                                @error('email_file')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 email_file mt-2" style="display:none;">
                                        </div>
                                        {{-- {{ old('subject', $subject) }} --}}
                                        <div class="col-md-6 col-12 mt-2">
                                            <div class="form-group">
                                                <label for="subject">Subject <span
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
                                                           class="custom-control-input" value="now_date" onchange="hideshow(event.target.value)" 
                                                           {{ old('date') == 'now_date' ? 'checked' : '' }} checked/>
                                                    <label class="custom-control-label" for="customRadio1">Email Send Now</label>
                                                </div>
                                                <div class="custom-control custom-radio mt-0">
                                                    <input type="radio" id="customRadio2" name="date"
                                                           class="custom-control-input" value="shedule_date" onchange="hideshow(event.target.value)"
                                                           {{ old('date') == 'shedule_date' ? 'checked' : '' }} />
                                                    <label class="custom-control-label" for="customRadio2">Email Schedule Later</label>
                                                </div>
                                            </div>
                                            <h5><small class="text-danger" id="gender_err"></small></h5>
                                            @error('date')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 col-12">
                                            <div class="form-group email_date" style="{{ old('date') == 'shedule_date' ? '' : 'display:none;' }}">
                                                <label for="date">Date <span style="color:red;">*</span></label>
                                                <input type="datetime-local" id="date" class="form-control"
                                                       placeholder="Start Date" name="shedulingdate" value="{{ old('shedulingdate') }}"
                                                       autocomplete="off" />
                                                <h5><small class="text-danger" id="date_err"></small></h5>
                                                @error('shedulingdate')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                         
                                       
                                        <div class="col-md-12 col-12 mt-2">
                                            <div class="form-group">
                                                <label for="message">Message <span
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
        .create(document.querySelector('#message'), {
            ckfinder: {
                uploadUrl: '{{ route('ckeditor.upload').'?_token='.csrf_token() }}'
            }
        })
        .catch(error => {
            console.error(error);
        });
    </script>
    <script>
        
        function hideshow(value){
            // alert(value);
            if(value == 'shedule_date'){
                $('.email_date').show();
                $('.email_date').next(".select2-container").show();
            }else{
                $('.email_date').hide();
                $('.email_date').next(".select2-container").hide();
            }
        }
        function hideshow_event(value){
         
            if(value == 'All Organizer'){
                $('.event').hide();
                $('.event').next(".select2-container").hide();
                $('.Organizer').show();
                $('.Organizer').next(".select2-container").hide();
            }else{
                $('.event').show();
                $('.event').next(".select2-container").show();
                $('.Organizer').hide();
                $('.Organizer').next(".select2-container").hide();
            }

            if((value == 'All Registration') || (value == 'All Participant') ){
                $('.event').show();
                $('.event').next(".select2-container").show();
            }else{
                $('.event').hide();
                $('.event').next(".select2-container").hide();
            }
           
        }

        function hideshow_event_receiver(value){
            //1=>All   
            if(value == '1'){
                $('.event').hide();
                $('.event').next(".select2-container").hide();
                $('.receiver').show();
                $('.receiver').next(".select2-container").show();
            }else{
                $('.receiver').hide();
                $('.receiver').next(".select2-container").hide();
            }
             //2=>Email   
            if(value == '2'){
                $('.email').show();
                $('.email').next(".select2-container").show();
               
            }else{
                $('.email').hide();
                $('.email').next(".select2-container").hide();
            }
             //3=>csv upload   
            if(value == '3'){
                $('.email_file').show();
                $('.email_file').next(".select2-container").show();
            }else{
                $('.email_file').hide();
                $('.email_file').next(".select2-container").hide();
            }
        }
        $(document).ready(function() {
  
            var selectedDateOption = $('input[name="date"]:checked').val();
            hideshow(selectedDateOption);
          
            var emailType = $('#email_type').val();
            hideshow_event_receiver(emailType);

            var oldReceiverValue = $('#receiver').val();
            hideshow_event(oldReceiverValue);

            
        });
                
    </script>
@endsection


