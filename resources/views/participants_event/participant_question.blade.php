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
                // dd($dataArray); 
            ?>
            <form class="form" action="{{ url('participants_event/'.$event_id.'/edit/'.$attendance_id) }}" method="post" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="edit_question">
                <input type="hidden" id="event_id" name="event_id" value="{{  $event_id  }}" autocomplete="off" />
                <input type="hidden" id="attendance_id" name="attendance_id" value="{{  $attendance_id  }}" autocomplete="off" />
                <input type="hidden" id="dataArray" name="dataArray" value="{{   $jsonString   }}" autocomplete="off" />
                {{ csrf_field() }}
                <?php if (json_last_error() === JSON_ERROR_NONE) {    
                    // Iterate through the array to get question labels and answer
                    $country_sel_id = '';
                    $state_sel_id = '';
                    $city_sel_id = '';
                    foreach ($dataArray as $item) { 
                        if (!array_key_exists('child_question_ids', $item)) {
                            continue; // Skip this iteration if the key is missing
                        }
                        if(($item['child_question_ids'] == null) || ($item['child_question_ids'] == '')){
                            $ActualValue = !empty($item['ActualValue']) ?  $item['ActualValue'] : ''; 
                            if($item['question_form_type'] == 'countries'){
                                $country_sel_id = !empty( $ActualValue) ?   $ActualValue : 0;
                            }
                            if($item['question_form_type'] == 'states'){
                                $state_sel_id = !empty( $ActualValue) ?   $ActualValue : 0;
                            }
                            if($item['question_form_type'] == 'cities'){
                                $city_sel_id = !empty( $ActualValue) ?   $ActualValue : 0;   
                            }
                            
                            ?>
                            
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        {{-- label name --}}
                                        <div class="form-group">
                                            {{-- @if(!in_array($item['question_label'], ['Date of Birth','DOB','Upload ID Proof','Enter your Personal Best Timing', 'Email Address','Amount','Other Amount','Enter amount to donate','Personal Best Distance']))
                                                <label for="question_label">
                                                    {{$item['question_label']}}<span style="color:red;">*</span> :
                                                </label>
                                            @endif --}}
                                            @if(!in_array($item['question_form_type'], ['date','time','amount','email','file']))
                                                <label for="question_label">
                                                    {{$item['question_label']}}<span style="color:red;"></span> :
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <?php if(($item['question_form_type'] == 'text') || $item['question_form_type'] == 'mobile' ){ ?>
                                                <input type="text" id="question_answer" class="form-control"
                                                    placeholder=" Question Answer" name="text[{{$item['question_label']}}]"
                                                    value="{{ old('question_answer',  $ActualValue) }}"  autocomplete="off" />
                                                <h5><small class="text-danger" id="text_err"></small></h5>
                                            <?php } else if($item['question_form_type'] == 'checkbox'){ ?>
                                                <?php  
                                                    $optionsJson = $item['question_form_option']; 
                                                    $optionsArray = json_decode($optionsJson, true); 
                                                    // dd($optionsJson);
                                                    $actualValue = $item['ActualValue'] ?? ''; // Retrieve the ActualValue
                                                    $checkedValues = explode(',', $actualValue); 
                                                ?>
                                                    <div class="form-check">
                                                    @foreach($optionsArray as $index => $option)
                                                        <input 
                                                            type="checkbox" 
                                                            id="checkbox_{{$item['question_label']}}_{{$index}}" 
                                                            name="checkbox[{{$item['question_label']}}][]" 
                                                            class="form-check-input"
                                                            value="{{ $option['id'] }}" 
                                                            {{ in_array($option['id'], old('checkbox['.$item['question_label'].']', $checkedValues)) ? 'checked' : '' }} />
                                                        
                                                        <label class="form-check-label" for="checkbox_{{$item['question_label']}}_{{$index}}">
                                                            {{ $option['label'] }}
                                                        </label>
                                                    @endforeach
                                                    <h5><small class="text-danger" id="checkbox_err"></small></h5>
                                                </div>
                                            <?php }else if($item['question_form_type'] == 'radio'){ ?>     
                                                <?php  
                                                    $optionsJson = $item['question_form_option']; 
                                                    $optionsArray = json_decode($optionsJson, true); 
                                                ?>
                                                <div class="demo-inline-spacing">
                                                    @foreach ($optionsArray as $index => $value)
                                                        <div class="custom-control custom-radio mt-0">
                                                            <input 
                                                                type="radio" 
                                                                id="customRadio{{$item['id']}}{{$index}}" 
                                                                name="radio[{{$item['question_label']}}]"
                                                                class="custom-control-input" 
                                                                value="{{ $value['id'] }}" 
                                                                {{ old('radio.'.$item['question_label'], $item['ActualValue']) == $value['id'] ? 'checked' : '' }} 
                                                            />
                                                            <label class="custom-control-label" for="customRadio{{$item['id']}}{{$index}}">{{ $value['label'] }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            <?php }else if($item['question_form_type'] == 'textarea'){ ?>
                                                <textarea name="textarea[{{$item['question_label']}}]" id="address" class="form-control" cols="1" rows="1" placeholder="Enter Address">{{ old('address.' . $item['question_label'], $ActualValue) }}</textarea>
                                                <h5><small class="text-danger" id="address_err"></small></h5>
                                            <?php }else if($item['question_form_type'] == 'select'){ ?>
                                                <?php  
                                                    $optionsJson = $item['question_form_option']; 
                                                    $optionsArray = json_decode($optionsJson, true); 
                                                    //dd(  $optionsArray);
                                                ?>
                                                <select name="select[{{$item['question_label']}}]" class="select2 form-control">
                                                    <option value="">Select {{ $item['question_label'] }}</option>
                                                    @foreach ($optionsArray as $value)
                                                        <option value="{{ htmlspecialchars($value['id']) }}" {{ old('select',  $ActualValue) == $value['id'] ? 'selected' : '' }}>
                                                            {{ htmlspecialchars($value['label']) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            <?php }else if($item['question_form_type'] == 'countries'){ ?>   
                                                <select id="countries" name="countries" class="select2 form-control">
                                                    <option value="">All countries</option>
                                                    <?php  
                                                    foreach ($countries as $value)
                                                    {  
                                                        $selected = '';
                                                        if(old('countries', $ActualValue) == $value->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->name; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                                                </select>
                                            <?php }else if($item['question_form_type'] == 'states'){ ?> 
                                                <select id="states" name="states" class="select2 form-control">
                                                <option value="">All state</option>
                                                </select>   
                                            <?php }else if($item['question_form_type'] == 'cities'){ ?> 
                                                <select id="cities" name="cities" class="select2 form-control">
                                                <option value="">All City</option>
                                                </select>            
                                            <?php } ?>   
                                        </div>     
                                    </div>
                                </div>   
                            
                        <?php }
                    }
                } else { ?>
                    <h1 colspan="17" style="text-align:center; color:red;">No Record Found</h1>
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
        var country_id = <?php echo $country_sel_id; ?>; 
        var state_id = <?php echo $state_sel_id; ?> ;
        var baseUrl = "{{ config('custom.app_url') }}";
       
        //console.log(country_id);
        if (country_id) {
            $.ajax({
                url:  baseUrl +'/get_states/' + country_id,
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


        $('#countries').change(function() {
        
            var countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    url:  baseUrl +'/get_states/' + countryId,
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
    });
    $(document).ready(function() {
        
        var state_id = <?php echo $state_sel_id; ?> 
        var city_id = <?php echo $city_sel_id; ?> 
        var baseUrl = "{{ config('custom.app_url') }}";
       
        //console.log(state_id);
        $('#states').change(function() {
            var stateId = $(this).val();
            // alert("herrete");
            if (stateId) {
                $.ajax({
                    url:  baseUrl +'/get_cities/' + stateId,
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
                url:  baseUrl +'/get_cities/' + state_id,
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

