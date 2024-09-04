
<?php
 if(!empty($edit_data)){
     $id        = $edit_data->id;
     $firstname = $edit_data->firstname;
     $lastname  = $edit_data->lastname;
     $mobile    = $edit_data->mobile;
     $email     = $edit_data->email;
     $is_active = $edit_data->is_active;
     $type      = $edit_data->type;
     $country   = $edit_data->country;
     $state     = $edit_data->state;
     $city      = $edit_data->city;
     $dob       = $edit_data->dob;
     $gender    = $edit_data->gender;
     $role      = $edit_data->role;
 }else{
    
     $id        = '';
     $firstname = '';
     $lastname  = '';
     $mobile    = '';
     $email     = '';
     $is_active = '';
     $type      = '';
     $country   = '';
     $state     = '';
     $city      = '';
     $dob       = '';
     $gender    = '';
     $role      = '';
 }

?>

@extends('layout.index')
@if (!empty($id))
    @section('title', ' User ')
@else
    @section('title', ' User ')
@endif

@section('title', 'User Create')
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
                                            Edit User Details
                                        @else
                                             Add User Details
                                        @endif</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</a></li>
                                        <li class="breadcrumb-item">User</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($id))
                                                Edit User
                                            @else
                                                Add User
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
                                    <input type="hidden" name="form_type" value="add_edit_user">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="user_id" id="user_id" value="{{ $id }}">

                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="firstname">First Name <span style="color:red;">*</span></label>
                                                <input type="text" id="firstname" class="form-control"
                                                    placeholder="Enter First Name" name="firstname" value="{{ old('firstname', $firstname) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="firstname_err"></small></h5>
                                                @error('firstname')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="lastname">Last Name <span style="color:red;">*</span></label>
                                                <input type="text" id="lastname" class="form-control"
                                                    placeholder="Enter Last Name" name="lastname" value="{{ old('lastname',$lastname) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="lastname_err"></small></h5>
                                                @error('lastname')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="email">Email <span style="color:red;">*</span></label>
                                                <input type="text" id="email" class="form-control" name="email"
                                                    placeholder="Enter Email" autocomplete="off" value="{{ old('email',$email) }}" />
                                                <h5><small class="text-danger" id="email_err"></small></h5>
                                                @error('email')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="contact_number">Contact Number <span style="color:red;">*</span></label>
                                                <input type="text" id="contact_number" class="form-control" name="contact_number"
                                                    placeholder="Enter Contact Number" autocomplete="off" value="{{ old('contact_number', $mobile) }}"
                                                    inputmode="numeric" pattern="\d*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="12" />
                                                <h5><small class="text-danger" id="email_err"></small></h5>
                                                @error('contact_number')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                       
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="dob">Date of Birth <span style="color:red;">*</span></label>
                                                <input type="date" id="dob" class="form-control"
                                                    placeholder="Enter Start Date" name="dob"
                                                    value="{{ old('dob', $dob ? \Carbon\Carbon::parse($dob)->format('Y-m-d') : '') }}" 
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="dob_err"></small></h5>
                                                @error('dob')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-12"><br>
                                            <label for="password_confirmation m-2">Gender <span style="color:red;">*</span></label> <br/>
                                            <div class="demo-inline-spacing">
                                                <div class="custom-control custom-radio mt-0">
                                                    <input type="radio" id="customRadio1" name="gender"
                                                        class="custom-control-input" value="1" {{ old('gender', $gender ?? '') == 1 ? 'checked' : '' }} />
                                                    <label class="custom-control-label" for="customRadio1">Male</label>
                                                </div>
                                                <div class="custom-control custom-radio mt-0">
                                                    <input type="radio" id="customRadio2" name="gender"
                                                        class="custom-control-input" value="2"  {{ old('gender', $gender ?? '') == 2 ? 'checked' : '' }} />
                                                    <label class="custom-control-label" for="customRadio2">Female</label>
                                                </div>
                                                <div class="custom-control custom-radio mt-0">
                                                    <input type="radio" id="customRadio2" name="gender"
                                                        class="custom-control-input" value="3" {{ old('gender', $gender ?? '') == 3 ? 'checked' : '' }} />
                                                    <label class="custom-control-label" for="customRadio2">Other</label>
                                                </div>
                                            </div>
                                            <h5><small class="text-danger" id="gender_err"></small></h5>
                                            @error('gender')
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="type">Type <span style="color:red;">*</span></label>
                                                <select id="type" name="type" class="select2 form-control">
                                                    <option value="">Select Type</option>

                                                    <option value="1" {{ old('type', $type ?? '') == 1 ? 'selected' : '' }}>Superadmin</option>
                                                    <option value="2" {{ old('type', $type ?? '') == 2 ? 'selected' : '' }}>Organizer/Admin</option>
                                                    <option value="3" {{ old('type', $type ?? '') == 3 ? 'selected' : '' }}>User</option>
                                                </select>
                                                    <h5><small class="text-danger" id="type_err"></small></h5>
                                                @error('type')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="role">Role</label>
                                                <select id="role" name="role" class="select2 form-control">
                                                    <option value="">Select Role</option>
                                                    <?php  
                                                    foreach ($role_details as $res)
                                                    {  
                                                        $selected = '';
                                                        if(old('role', $role) == $res->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $res->id; ?>" <?php echo $selected; ?>><?php echo $res->name; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                                                </select>
                                                   
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-12">
                                            <label for="country">Country: <span style="color:red;">*</span></label>
                                            <select id="country" name="country" class="select2 form-control">
                                                <option value="">All country</option>
                                                <?php  
                                                foreach ($countries as $value)
                                                {  
                                                    $selected = '';
                                                    if(old('country', $country) == $value->id){
                                                        $selected = 'selected';
                                                    }
                                                    ?>
                                                    <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo ucfirst($value->name); ?></option>
                                                    <?php 
                                                }
                                                ?>
                                            </select>
                                            <h5><small class="text-danger" id="country_err"></small></h5>
                                                @error('country')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                        </div>
                                        <div class="col-sm-6 col-12">
                                            <label for="state">State: <span style="color:red;">*</span></label>
                                            <select id="state" name="state" class="select2 form-control">
                                                <option value="" class="placeholder">All state</option>
                                            </select>  
                                            <h5><small class="text-danger" id="state_err"></small></h5>
                                                @error('state')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                        </div>

                                        <div class="col-sm-6 col-12">
                                            <label for="city">City: <span style="color:red;">*</span></label>
                                            <select id="city" name="city" class="select2 form-control">
                                                <option value="">All City</option>
                                            </select>  
                                            <h5><small class="text-danger" id="city_err"></small></h5>
                                                @error('city')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="password">Password <span style="color:red;">*</span></label>
                                                <i class="fa fa-eye" id="togglePassword" style="cursor:pointer; position: absolute; top: 35px; right: 30px;"></i>
                                                <input type="password" id="password" class="form-control" name="password" placeholder="Enter Password" autocomplete="off" />
                                                
                                                <h5><small class="text-danger" id="password_err"></small></h5>
                                                @error('password')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="password_confirmation">Confirm Password <span style="color:red;">*</span> </label>
                                                <i class="fa fa-eye" id="toggleConfirmPassword" style="cursor:pointer; position: absolute; top: 35px; right: 30px;"></i>
                                                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="Enter Confirm Password" />
                                                <h5><small class="text-danger" id="password_confirmation_err"></small>
                                                </h5>
                                                @error('password_confirmation')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                       
                                   

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/users') }}" type="reset"
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
    $(document).ready(function () {
        $('#togglePassword').on('click', function () {
            const passwordField = $('#password');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).toggleClass('fa-eye-slash'); // Toggle the icon
        });

        $('#toggleConfirmPassword').on('click', function () {
            const confirmPasswordField = $('#password_confirmation');
            const type = confirmPasswordField.attr('type') === 'password' ? 'text' : 'password';
            confirmPasswordField.attr('type', type);
            $(this).toggleClass('fa-eye-slash'); // Toggle the icon
        });
    });
</script>
<script>
     $(document).ready(function() {
        var CountryId = '<?php echo old('country', $country); ?>';
        var StateId = '<?php echo old('state', $state); ?>';
        var CityId = '<?php echo old('city', $city); ?>';
      
        // alert(CountryId);
         //console.log("CountryId "+CountryId);
        // Fetch states based on the selected country
        if (CountryId !== '') {
            // alert("here");
            $.ajax({
                url: '/get_states', // Replace with your URL to fetch states
                type: 'GET',
                data: { country_id: CountryId },
                success: function(states) {
                    $('#state').empty().append('<option value="">Select State</option>');
                    $.each(states, function(key, value) {
                        $('#state').append('<option value="'+ value.id +'" '+ (StateId == value.id ? 'selected' : '') +'>'
                            + value.name +'</option>');
                    });

                    // Fetch cities based on the selected state
                    if (StateId !== '') {
                        $.ajax({
                            url: '/get_cities', // Replace with your URL to fetch cities
                            type: 'GET',
                            data: { state_id: StateId },
                            success: function(cities) {
                                $('#city').empty().append('<option value="">Select City</option>');
                                $.each(cities, function(key, value) {
                                    $('#city').append('<option value="'+ value.id +'" '+ (CityId == value.id ? 'selected' : '') +'>'
                                        + value.name +'</option>');
                                });
                            }
                        });
                    }
                }
            });
        }

        // Handle country change
        $('#country').change(function() {
            var countryId = $(this).val();
            $.ajax({
                url: '/get_states',
                type: 'GET',
                data: { country_id: countryId },
                success: function(states) {
                    $('#state').empty().append('<option value="">Select State</option>');
                    $.each(states, function(key, value) {
                        $('#state').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                    $('#city').empty().append('<option value="">Select City</option>'); // Clear cities
                }
            });
        });

        // Handle state change
        $('#state').change(function() {
            var stateId = $(this).val();
            $.ajax({
                url: '/get_cities',
                type: 'GET',
                data: { state_id: stateId },
                success: function(cities) {
                    $('#city').empty().append('<option value="">Select City</option>');
                    $.each(cities, function(key, value) {
                        $('#city').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                }
            });
        });
    });

</script>

