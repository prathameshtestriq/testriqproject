@if ($message = Session::get('success'))
<div class="demo-spacing-0 mb-1">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="alert-body">
            <i class="fa fa-check-circle" style="font-size:16px;" aria-hidden="true"></i>
            {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">

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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">

        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
@endif
<div class="participant_details_modal">
    <div class="row">
        <h3>PARTICIPANT QUESTIONS</h3><br>
       <div class="table">
            <?php 
            $jsonString =$attendance_booking_details[0]->attendee_details;
            $dataString = json_decode($jsonString, true);
            $dataArray = json_decode($dataString, true);

       
            //  dd($dataArray);  ?>
            <form class="form" action="{{ url('participants_event/'.$event_id.'/edit/'.$attendance_id) }}" method="post" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="edit_question">
                <input type="hidden" id="event_id" name="event_id" value="{{  $event_id  }}" autocomplete="off" />
                <input type="hidden" id="attendance_id" name="attendance_id" value="{{  $attendance_id  }}" autocomplete="off" />
                <input type="hidden" id="dataArray" name="dataArray" value="{{   $jsonString   }}" autocomplete="off" />
                {{ csrf_field() }}
                <?php if (json_last_error() === JSON_ERROR_NONE) {    
                    // Iterate through the array to get question labels and answers
                    $country_sel_id = '';
                    $state_sel_id = '';
                    $city_sel_id = '';
                    foreach ($dataArray as $item) { 
                        if($item['question_form_type'] == 'countries'){
                            $country_sel_id = !empty($item['ActualValue']) ? $item['ActualValue'] : 0;
                        }
                        if($item['question_form_type'] == 'states'){
                            $state_sel_id = !empty($item['ActualValue']) ? $item['ActualValue'] : 0;
                        }
                        if($item['question_form_type'] == 'cities'){
                            $city_sel_id = !empty($item['ActualValue']) ? $item['ActualValue'] : 0;
                              
                        }

                        ?>
                    
                            <div class="row">
                                
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="question_label">{{$item['question_label']}}<span
                                            style="color:red;">*</span> :</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <?php
                                            //if($item['question_form_type'] == 'text' || $item['question_form_type'] == 'email'){
                                            if($item['question_form_type'] == 'text' ){
                                        ?>
                                            <input type="text" id="question_answer" class="form-control"
                                                placeholder=" Question Answer" name="text[{{$item['question_label']}}]"
                                                value="{{ old('question_answer', $item['ActualValue']) }}"  autocomplete="off" />
                                            <h5><small class="text-danger" id="text_err"></small></h5>
                                            {{-- @error("text[{{$item['question_label']}}]")
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror --}}
                                        {{-- <?php 
                                            }else if($item['question_form_type'] == 'date'){ 
                                        ?>

                                            <input type="date" id="date" class="form-control"
                                                placeholder=" Question Answer" name="date"
                                                value="{{ old('date', $item['ActualValue']) }}"  autocomplete="off" />
                                            <h5><small class="text-danger" id="question_answer_err"></small></h5> --}}

                                        <?php 
                                            }else if($item['question_form_type'] == 'radio'){ 
                                                if($item['question_label'] == 'Gender'){
                                                    // $dataString = json_decode($jsonString, true);
                                                    //  $dataArray = json_decode($dataString, true);
                                                    // dd($item['ActualValue']);

                                        ?>
                                                <div class="demo-inline-spacing">
                                                    <div class="custom-control custom-radio mt-0">
                                                        <input type="radio" id="customRadio1" name="radio[{{$item['question_label']}}]"
                                                            class="custom-control-input" value="1" <?php if(old('gender',$item['ActualValue'])  == 1) echo 'checked' ?> />
                                                        <label class="custom-control-label" for="customRadio1">Male</label>
                                                    </div>
                                                    
                                                    <div class="custom-control custom-radio mt-0">
                                                        <input type="radio" id="customRadio2" name="radio[{{$item['question_label']}}]"
                                                            class="custom-control-input" value="2"  <?php if(old('gender',$item['ActualValue'])  == 2) echo 'checked' ?> />
                                                        <label class="custom-control-label" for="customRadio2">Female</label>
                                                    </div>
                                                   
                                                    <div class="custom-control custom-radio mt-0">
                                                        <input type="radio" id="customRadio3" name="radio[{{$item['question_label']}}]"
                                                            class="custom-control-input" value="3" <?php if(old('gender',$item['ActualValue'])  == 3) echo 'checked' ?>  />
                                                        <label class="custom-control-label" for="customRadio3">Other</label>
                                                    </div>
                                                
                                                </div>  
                                            <?php 
                                          
                                            }else if($item['question_label'] == 'Do you have any Chronic Disease?'){
                                                // dd($item['ActualValue']);
                                           ?>
                                                <div class="demo-inline-spacing">
                                                    <div class="custom-control custom-radio mt-0">
                                                        <input type="radio" id="customRadio4" name="radio[{{$item['question_label']}}]"
                                                            class="custom-control-input" value="1" <?php if(old('chronic_disease',$item['ActualValue'])  == 1) echo 'checked' ?> />
                                                        <label class="custom-control-label" for="customRadio4">Yes</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mt-0">
                                                        <input type="radio" id="customRadio5" name="radio[{{$item['question_label']}}]"
                                                            class="custom-control-input" value="2" <?php if(old('chronic_disease',$item['ActualValue'])  == 2) echo 'checked' ?>  />
                                                        <label class="custom-control-label" for="customRadio5">No</label>
                                                    </div>     
                                                </div>
                                            <?php }   ?>
                                        <?php 
                                        }else if($item['question_form_type'] == 'textarea'){ 
                                        ?>    
                                          <textarea name="textarea" id="textarea" class="form-control" cols="1" rows="1">{{ old('textarea', $item['ActualValue']) }}</textarea>

                                        <?php 
                                        }else if($item['question_form_type'] == 'select'){ 
                                            // dd($item['question_label']);
                                            if($item['question_label'] == 'Blood Group' ){
                                        ?>  
                                        <?php  
                                            $bloodjson =  $item['question_form_option']; 
                                            $bloodArray = json_decode($bloodjson, true);  
                                        ?>
                                             <select name="select[{{$item['question_label']}}]" class="select2 form-control">
                                                <option value="">All blood</option>
                                                
                                                <?php 
                                                foreach ($bloodArray as $value)
                                                {  
                                                    $selected = '';
                                                    if(old('select', $item['ActualValue']) == $value['id']){   
                                                        $selected = 'selected';
                                                    }
                                                    ?>
                                                    <option value="<?php echo htmlspecialchars($value['id']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($value['label']); ?></option>
                                                    <?php 
                                                }
                                                ?>
                                            </select>
                                            <?php }else if($item['question_label'] == 'T-shirt'){?>
                                                <?php  
                                                $Tshirtjson =  $item['question_form_option']; 
                                                $TshirtArray = json_decode($Tshirtjson, true);  
                                            ?>
                                                 <select name="select[{{$item['question_label']}}]" class="select2 form-control">
                                                    <option value="">All T-shirt</option>
                                                    
                                                    <?php 
                                                    foreach ($TshirtArray as $value)
                                                    {  
                                                        $selected = '';
                                                        if(old('select', $item['ActualValue']) == $value['id']){   
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo htmlspecialchars($value['id']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($value['label']); ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                                                </select>
                                            <?php } ?>
                                        {{-- <?php 
                                        }else if($item['question_form_type'] == 'file'){
                                        ?>   --}}
                                                {{-- <input type="file" id="file" class="form-control"
                                                placeholder="img" name="file"
                                                autocomplete="off" /> --}}
                                        <?php
                                        }else if($item['question_form_type'] == 'countries'){
                                        ?>   
                                            <select id="countries" name="countries" class="select2 form-control">
                                                <option value="">All countries</option>
                                                <?php  
                                                foreach ($countries as $value)
                                                {  
                                                    $selected = '';
                                                    if(old('countries',$item['ActualValue']) == $value->id){
                                                        $selected = 'selected';
                                                    }
                                                    ?>
                                                    <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->name; ?></option>
                                                    <?php 
                                                }
                                                ?>
                                            </select>
                                        <?php 
                                            }else if($item['question_form_type'] == 'states'){
                                        ?> 
                                                    <select id="states" name="states" class="select2 form-control">
                                                    <option value="">All state</option>
                                                    </select>
                                                    
                                        <?php 
                                            }else if($item['question_form_type'] == 'cities'){
                                        ?> 
                                                <select id="cities" name="cities" class="select2 form-control">
                                                <option value="">All City</option>
                                                </select>
                                                       
                                        {{-- <?php }if($item['question_form_type'] == 'mobile'){ ?>
                                            <input type="number" id="mobile" class="form-control"
                                            placeholder="mobile" name="mobile[{{$item['question_label']}}]"
                                            value="{{ old('mobile', $item['ActualValue']) }}"  autocomplete="off" /> --}}
                                        <?php } ?>   
                                    </div>     
                                </div>
                            </div>   
                        
                <?php  }
                } else { ?>
                    <h1 colspan="17" style="text-align:center; color:red;">No Record Found</h1>
                    {{-- echo "Error decoding JSON: " . json_last_error_msg(); --}}
                <?php  } ?>
                <div class="col-12 text-center mt-1">
                    <input type="submit" class="btn btn-primary mr-1" onClick="return check_validation()" value="Submit">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal" onclick="popupclose()">Close</button>  
                </div>
            </form>

		</div>   
       </div>
    </div>
</div> 

<script>
    function popupclose(){
            $('#participant_details_modal').modal("hide");
        }
</script>
<script>
    
    $(document).ready(function() {
        // var country_id = document.getElementById('countries').value;
        // var state_id = document.getElementById('states').value;
        var country_id = <?php echo $country_sel_id; ?> 
        var state_id = <?php echo $state_sel_id; ?> 
        //   console.log(country_id);
        $('#countries').change(function() {
        
            var countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    url: '/get_states/' + countryId,
                    type: 'GET',
                    success: function(data) {
                        console.log(data);
                        var stateDropdown = $('#states');
                        stateDropdown.empty();
                        stateDropdown.append('<option value="">All State</option>');
                        $.each(data.states, function(index, state) {
                            stateDropdown.append('<option value="' + state.id + '">' + state.name + '</option>');
                        });
                    }
                });
            } else {
                $('#state').empty().append('<option value="">All state</option>');
                $('#cities').empty().append('<option value="">All City</option>');
            }
        });

        if (country_id) {
            $.ajax({
                url: '/get_states/' + country_id,
                type: 'GET',
                success: function(data) {
                    // console.log(data);
                    var stateDropdown = $('#states');
                    var oldStateId = state_id;
                    stateDropdown.empty();
                    stateDropdown.append('<option value="">All State</option>');
                    $.each(data.states, function(index, state) {
                        stateDropdown.append('<option value="' + state.id + '">' + state.name + '</option>');
                    });
                    if (oldStateId) {
                        stateDropdown.val(oldStateId);
                    }
                }
            });
        } else {
            $('#state').empty().append('<option value="">All state</option>');
            $('#cities').empty().append('<option value="">All City</option>');
        }


    });
    $(document).ready(function() {
        
        var state_id = <?php echo $state_sel_id; ?> 
        var city_id = <?php echo $city_sel_id; ?> 
        
        $('#states').change(function() {
            var stateId = $(this).val();
            // alert("herrete");
            if (stateId) {
                $.ajax({
                    url: '/get_cities/' + stateId,
                    type: 'GET',
                    success: function(data) {
                        console.log(data);
                        var cityDropdown = $('#cities');
                        cityDropdown.empty();
                        cityDropdown.append('<option value="">All City</option>');
                        $.each(data.cities, function(index, city) {
                            cityDropdown.append('<option value="' + city.id + '">' + city.name + '</option>');
                        });
                       
                    }
                });
            } else {
                $('#cities').empty().append('<option value="">All City</option>');
            }
        });
        
        if (state_id) {
            $.ajax({
                url: '/get_cities/' + state_id,
                type: 'GET',
                success: function(data) {
                    var cityDropdown = $('#cities');
                    var oldCityId = city_id;
                    cityDropdown.empty();
                    cityDropdown.append('<option value="">All City</option>');
                    $.each(data.cities, function(index, city) {
                        cityDropdown.append('<option value="' + city.id + '">' + city.name + '</option>');
                    });
                    if (oldCityId) {
                        cityDropdown.val(oldCityId);
                    }
                }
            });
        } else {
            $('#cities').empty().append('<option value="">All City</option>');
        }
        
    });
    </script>

