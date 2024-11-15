@extends('layout.index')
@if (!empty($id))
    @section('title', ' Email Placeholder Management')
@else
    @section('title', ' Email Placeholder Management')
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
                                                Edit Email Placeholder Management Details
                                            @else
                                                Add Email Placeholder Management Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Email Placeholder Management </li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($aReturn['id']))
                                                Edit Email Placeholder Management 
                                            @else
                                                Add Email Placeholder Management
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
                                    <input type="hidden" name="form_type" value="add_edit_email_placeholder"
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

                                        <div class="col-sm-6 col-12">
                                            <label for="form-control">Question <span style="color:red;">*</span></label>
                                            <input type="hidden" id="result" name="question_form_name" value="{{old('question_form_name',$question_form_name)}}"/> 
                                            <select id="question" name="question" class="form-control select2 form-control" oninput="getSelectedOption()">
                                                <option value="">Select Question</option>
                                            </select>
                                            <h5><small class="text-danger" id="question_err"></small></h5>
                                            @error('question')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                            <h5><small class="text-danger" id="question_form_name_err"></small></h5>
                                            @error('question_form_name')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                            
                                        </div>
                                      
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="placeholder_name">Placeholder Name <span style="color:red;">*</span></label>
                                                <input type="text" id="placeholder_name" class="form-control"
                                                    placeholder="Placeholder Name" name="placeholder_name" value="{{ old('placeholder_name', $placeholder_name) }}"   autocomplete="off"  oninput="this.value = this.value.toUpperCase().replace(/\s/g, '');"  />
                                                <h5><small class="text-danger" id="placeholder_name_err"></small></h5>
                                                @error('placeholder_name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                       
                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/email_placeholder_management') }}" type="reset"
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function getSelectedOption() {
       // Get the dropdown element
        var dropdown = document.getElementById('question');
      
        // Get the selected option
        var selectedOption = dropdown.options[dropdown.selectedIndex];
       
        // Get the selected value and text (name)
        var selectedValue = selectedOption.value;
        var selectedName = selectedOption.text;
        
       
        // Combine the selected value and name
        // var resultText = "Selected Value: " + selectedValue + " | Selected Name: " + selectedName;
        var resultText = selectedName
        // Set the combined result text into the input field's value
        document.getElementById('result').value = resultText;

       
    }

</script>
<script>
    $(document).ready(function() {
        var oldQuestionId = "{{ old('question', $question_id) }}"; // Get old value

        $('#event').on('change', function() {
            var eventId = $(this).val();
            var baseUrl = "{{ config('custom.app_url') }}";
            $('#question').empty().append('<option value="">Select Question</option>');

            if (eventId) {
                $.ajax({
                    url: baseUrl + '/get_questions',
                    type: 'GET',
                    data: { EventId: eventId },
                    success: function(data) {
                        $.each(data, function(key, question) {
                            var selected = question.id == oldQuestionId ? 'selected' : '';
                            $('#question').append('<option value="' + question.id + '" ' + selected + '>' + question.question_form_name + '</option>');
                        });
                    },
                    error: function() {
                        $('#question_err').text('Could not load questions.');
                    }
                });
            }
        });

        // Trigger change to load questions if there's an old event
        if ($('#event').val()) {
            $('#event').trigger('change');
        }
    });
</script>




