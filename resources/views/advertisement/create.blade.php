<?php
// dd($edit_data);
if (!empty($edit_data)) {
    $id = $edit_data['id'];
    $name = $edit_data['name'];
    $img = $edit_data['img'];
    $url = $edit_data['url'];
    $position = $edit_data['position'];
    $start_time = $edit_data['start_time'];
    $end_time = $edit_data['end_time'];
} else {
    $id = '';
    $name = '';
    $img = '';
    $url = '';
    $position = '';
    $start_time = '';
    $end_time = '';
}

?>
@extends('layout.index')
@if (!empty($id))
    @section('title', 'Advertisement ')
@else
    @section('title', ' Advertisement ')
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
                                                Edit Advertisement Details
                                            @else
                                                Add Advertisement Details
                                            @endif
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                        <li class="breadcrumb-item">Advertisement</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if (!empty($id))
                                                Edit Advertisement Details
                                            @else
                                                Add Advertisement Details
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
                                    <input type="hidden" name="form_type" value="add_edit_ad">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="name">Name<span
                                                        style="color:red;">*</span></label>
                                                <input type="text" id="name" class="form-control"
                                                    placeholder=" Name" name="name"
                                                    value="{{ old('name', $name) }}"  autocomplete="off" />
                                                <h5><small class="text-danger" id="name_err"></small></h5>
                                                @error('name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="url">Url <span style="color:red;">*</span></label>
                                                <input type="text" id="url" class="form-control"
                                                    placeholder="Url" name="url"
                                                    value="{{ old('url', $url) }}"
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="url_err"></small></h5>
                                                @error('url')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="position">Position<span style="color:red;">*</span></label>
                                                <?php 
                                                $Positions = array('top', 'bottom', 'middle' );
                                                ?>
                                                <select id="position" name="position" class="select2 form-control">
                                                    <option value="">Select Position</option>
                                                    <?php 
                                                    foreach ($Positions as $key => $value)
                                                    {
                                                        // old('position',$position)
                                                        $selected = '';
                                                        if(old('position', $position) == $value){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                                                </select>
                                                    <h5><small class="text-danger" id="position_err"></small></h5>
                                                @error('position')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="img">Image <span style="color:red;">*</span></label>
                                                <p style="color:red;">Allowed JPEG, JPG or PNG. Max file size of 2 MB</p>
                                                <input type="file" id="img" class="form-control"
                                                    placeholder="img" name="img"
                                                    style="text-transform: capitalize; display: block; width: 100%;"
                                                    accept="image/jpeg, image/png" 
                                                    autocomplete="off" />
                                                  
                                                <h5><small class="text-danger" id="image_err"></small></h5>
                                                @error('img')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-2 mt-2">
                                            <span><br /></span>
                                            @if (!empty($img))
                                                <a href="{{ asset('uploads/images/' . $img) }}" target="_blank">
                                                  <img src="{{ asset('uploads/images/' . $img) }}" alt="Current Image" style="width: 50px;">
                                                </a>
                                                <input type="hidden" name="hidden_image" value="{{ old('img', $img) }}" accept="image/jpeg, image/png">
                                            @endif

                                        </div>
                                          
                                        
 
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="start_time">Start Date<span style="color:red;">*</span></label>
                                                <input type="datetime-local" id="start_time" class="form-control"
                                                    placeholder="Start Date" name="start_time"
                                                    value="{{ old('start_time', $start_time ? \Carbon\Carbon::parse($start_time)->format('Y-m-d\TH:i:s') : '') }}" 
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="start_time_err"></small></h5>
                                                @error('start_time')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="end_time">End Date<span style="color:red;">*</span></label>
                                                <input type="datetime-local" id="end_time" class="form-control"
                                                    placeholder="End Date" name="end_time"
                                                    value="{{ old('end_time', $end_time ? \Carbon\Carbon::parse($end_time)->format('Y-m-d\TH:i') : '') }}"  
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="end_time_err"></small></h5>
                                                @error('end_time')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                  

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/advertisement') }}" type="reset"
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

<script type="text/javascript">
    function validation() {

      
    }
</script>
