<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
@extends('layout.index')
@section('title', 'Testimonial Create')
<!-- Dashboard Ecommerce start -->
<style>
    * {
        font-size: 15px;
    }

    .form-group label {
        margin-top: 5px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
    }

    .error-message {
        color: red;
        font-size: 14px;
        margin-top: 5px;
    }

    .btn-submit {
        margin-top: 20px;
    }
</style>
@section('content')
<section>
    <div class="content-body">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12 d-flex">
                        <h2 class="content-header-title float-start mb-0">Add Testimonial</h2>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <div class="mb-1 breadcrumb-right">
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb" style="justify-content: flex-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Testimonial</a></li>
                            <li class="breadcrumb-item active">Add Testimonial</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($message = Session::get('error'))
    <div class="demo-spacing-0 mb-1">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="alert-body">
                {{ $message }}
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
                            <form method="post" name="transfer" id="transfer" novalidate="novalidate"
                                enctype="multipart/form-data">
                                <input type="hidden" name="form_type" value="add_edit_testimonial">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label for="user_id">Testimonial Name <span style="color:red">*</span></label>
                                         <input type="text" id="testimonial_name" class="form-control"
                                            placeholder="Testimonial Name" name="testimonial_name" autocomplete="off"
                                            value="{{ old('testimonial_name', $testimonial_name) }}" />
                                        <span class="error-message">@error('testimonial_name'){{ $message
                                            }}@enderror</span>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="subtitle">Subtitle <span style="color:red;">*</span></label>
                                        <input type="text" id="subtitle" class="form-control"
                                            placeholder="Subtitle" name="subtitle" autocomplete="off"
                                            value="{{ old('subtitle', $subtitle) }}" />
                                        <span class="error-message">@error('subtitle'){{ $message }}@enderror</span>
                                    </div>


                                  <!--    <div class="col-sm-6">
                                            <div class="form-group mb-5">
                                            <label for="testimonial_img" class="form-label">Image <span style="color:red">*</span></label>
                                                @if (!empty($testimonial_img))
                                                    <img src="{{ asset('uploads/testimonial_images/' . $testimonial_img) }}" alt="Current Image" style="width: 50px;">
                                                    <input type="hidden" name="hidden_testimonial_img" value="{{ old('testimonial_img', $testimonial_img) }}" accept="image/jpeg, image/png">
                                                @endif
                                                <input type="file" name="testimonial_img" id="testimonial_img" accept="image/jpeg, image/png" class="form-control">
                                                <span class="error-message">@error('testimonial_img'){{ $message }}@enderror</span>
                                            </div>  -->

                                    <!-- <div class="col-6">
                                            <div class="form-group mb-5">
                            <label for="testimonial_img" class="form-label">Image <span
                                    style="color:red">*</span></label>

                            @if (!empty($testimonial_img))
                            <img src="{{ asset('uploads/testimonial_images/' . $testimonial_img) }}"
                                alt="Current Image" style="width: 50px;">
                            <input type="hidden" name="hidden_testimonial_img"
                                value="{{ old('testimonial_img', $testimonial_img) }}" accept="image/jpeg, image/png">
                            @endif

                            <input type="file" name="testimonial_img" id="testimonial_img"
                                style="text-transform: capitalize; display: block; width: 100%;"
                                accept="image/jpeg, image/png" class="form-control">
                            <span style="color:red;" id="testimonial_img_err"></span>

                            @error('testimonial_img')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">

<div class="col-12">
    <div class="form-group">
     
        <input type="hidden" name="testimonial_images" id="testimonial_img"
            style="text-transform: capitalize;" style="width:50px;" class="form-control">
    </div>

</div>
</div> -->
                                    <div class="col-6 mt-2">
                                        <label for="description">Description <span style="color:red;">*</span></label>
                                        <input type="text" id="description" class="form-control"
                                            placeholder="Description" name="description" autocomplete="off"
                                            value="{{ old('description', $description) }}" />
                                           
                                        <span class="error-message">@error('description'){{ $message
                                            }}@enderror</span>
                                    </div>

                                    <div class="col-sm-4 mt-2">
                                          
                                        <label for="testimonial_img" class="form-label">Image <span style="color:red">*</span></label>
                                       
                                        <input type="file" name="testimonial_img" id="testimonial_img" accept="image/jpeg, image/png" class="form-control">
                                        <span class="error-message">@error('testimonial_img'){{ $message }}@enderror</span>
                                    </div> 

                                    <div class="col-sm-2 mt-2">
                                        @if (!empty($testimonial_img))
                                            <a href="{{ asset('uploads/testimonial_images/' . $testimonial_img) }}" target="_blank">
                                                <img src="{{ asset('uploads/testimonial_images/' . $testimonial_img) }}" alt="Current Image" style="width: 50px;">
                                            </a>
                                            <input type="hidden" name="hidden_testimonial_img" value="{{ old('testimonial_img', $testimonial_img) }}" accept="image/jpeg, image/png">
                                        @endif
                                    </div>

                               <!--      <div class="col-xs-12 col-md-12">
                                        <label class="col-sm-1 float-left" for="password_confirmation"
                                            style="margin-top:10px">Status <span style="color:red;">*</span></label>
                                        <div class="form-check mt-1 mb-2">
                                            <?php //$activeValue = ''; // Initialize to empty string ?>
                                            <input class="form-check-input active1" type="radio" name="active"
                                                id="active1" value="active" style="cursor: pointer;" <?php //if($active==1) { echo 'checked' ; } ?>>
                                            <label class="form-check-label mr-4" for="active1">Active</label>
                                            <input class="form-check-input active1" type="radio" name="active"
                                                id="active2" value="inactive" style="cursor: pointer;" <?php //if($active==0) { echo 'checked' ; } ?>>
                                            <label class="form-check-label" for="active2">Inactive</label>
                                        </div>
                                        <span class="error-message">@error('active'){{ $message }}@enderror</span>
                                        {{-- <h5><small class="text-danger" id="active_err"></small></h5>
                                        @error('active')
                                        <span class="error" style="color:red;">{{ $message }}</span>
                                        @enderror --}}
                                    </div> -->
                                </div>

                                <div class="col-12 text-center mt-1">
                                    <button type="submit" class="btn btn-primary mr-1"
                                        onClick="return validation()">Submit</button>
                                    <a href="{{ url('/testimonial') }}" type="reset"
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

<script>
    function validation() {
        var isValid = true;


        $('.error').html('');

        // Check vehicle brand
        // var vehicle_brand = document.getElementById('vehicle_brand');
        // var ptrn = /^[A-Za-z\s]*$/;
        // console.log(vehicle_brand.value.match(ptrn))
        // if (vehicle_brand.value.match(ptrn) === null) {
        //     $('#vehicle_brand_err').html('Please enter letters only');
        //     isValid = false;
        // }

        // // Check vehicle brand
        // if ($('#vehicle_brand').val() == "") {
        //     $('#vehicle_brand_err').html('Please enter Vehicle brand.');
        //     $('#vehicle_brand').focus();
        //     $('#vehicle_brand').keyup(function () {
        //         $('#vehicle_brand').parent().removeClass('has-error');
        //         $('#vehicle_brand_err').html('');
        //     });
        //     isValid = false;
        // }

        // // Check vehicle number
        // var ptrn_vehicle_number = /^[A-Z]{2}[ -][0-9]{1,2}(?: [A-Z])?(?: [A-Z]*)? [0-9]{4}$/;
        // if ($('#vehicle_number').val() == "") {
        //     $('#vehicle_number_err').html('Please enter vehicle number.');
        //     $('#vehicle_number').focus();
        //     $('#vehicle_number').keyup(function () {
        //         $('#vehicle_number').parent().removeClass('has-error');
        //         $('#vehicle_number_err').html('');
        //     });
        //     isValid = false;
        // } else if (!ptrn_vehicle_number.test($('#vehicle_number').val())) {
        //     $('#vehicle_number_err').html('Please enter a valid vehicle number');
        //     isValid = false;
        // }
        // var vehicle_driver_name = document.getElementById('vehicle_driver_name');
        // var driverNamePtrn = /^[A-Za-z\s]*$/;
        // console.log(vehicle_driver_name.value.match(driverNamePtrn))
        // if (vehicle_driver_name.value.match(driverNamePtrn) === null) {
        //     $('#vehicle_driver_name_err').html('Please enter letters only');
        //     isValid = false;
        // }


        // // Check vehicle driver name
        // if ($('#vehicle_driver_name').val() == "") {
        //     $('#vehicle_driver_name_err').html('Please enter vehicle driver name.');
        //     $('#vehicle_driver_name').focus();
        //     $('#vehicle_driver_name').keyup(function () {
        //         $('#vehicle_driver_name').parent().removeClass('has-error');
        //         $('#vehicle_driver_name_err').html('');
        //     });
        //     isValid = false;
        // }

        // // Check driver contact number
        // var contactNumberRegex = /^[0-9]{10}$/;
        // if ($('#driver_contact').val() == "") {
        //     $('#driver_contact_err').html('Please enter driver contact number.');
        //     $('#driver_contact').focus();
        //     $('#driver_contact').keyup(function () {
        //         $('#driver_contact').parent().removeClass('has-error');
        //         $('#driver_contact_err').html('');
        //     });
        //     isValid = false;
        // } else if (!contactNumberRegex.test($('#driver_contact').val())) {
        //     $('#driver_contact_err').html('Please enter a valid 10-digit contact number.');
        //     isValid = false;
        // }

        // // Check vehicle type
        // if ($('#vehicle_type').val() == "") {
        //     $('#vehicle_type_err').html('Please select the vehicle type.');
        //     $('#vehicle_type').focus();
        //     $('#vehicle_type').change(function () {
        //         $('#vehicle_type').parent().removeClass('has-error');
        //         $('#vehicle_type_err').html('');
        //     });
        //     isValid = false;
        // }

        // Check vehicle photo
        var testimonial_img = $('#testimonial_img').prop('files')[0];
        var existingImage = $('input[name="hidden_testimonial_img"]').val();
        if (!testimonial_img && !existingImage) {
         //  alert('here');
            $('#testimonial_img_err').html('Please select a image.');
            isValid = false;
        } else if (testimonial_img) {
            var maxSize = 2 * 1024 * 1024; // 2MB in bytes
            if (testimonial_img.size > maxSize) {
                $('#testimonial_img_err').html('Please select a file smaller than 2MB.');
                $('#testimonial_img').val('');
                isValid = false;
            }

            var allowedTypes = ['jpg', 'jpeg', 'png'];
            var fileType = testimonial_img.name.split('.').pop().toLowerCase();
            if ($.inArray(fileType, allowedTypes) === -1) {
                $('#testimonial_img_err').html('Please select a valid file type (jpg, jpeg, png).');
                $('#testimonial_img').val('');
                isValid = false;
            }
        }

        return isValid;
    }


</script>