@extends('layout.index')
@section('title', 'Testimonial ')

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
                                    <h2 class="content-header-title float-left mb-0">Testimonial </h2>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end breadcrumb-wrapper">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mr-1">
                                    <li class="breadcrumb-item">Home</li>
                                    <li class="breadcrumb-item">Testimonial</li>
                                    <li class="breadcrumb-item active" aria-current="page">Testimonial List</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     <!-- Bordered table end -->
    </div>    

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

    <div class="alert alert-success p-1" id="success-alert" style="display: none;">
        <i class="fa fa-check-circle" style="font-size:16px;" aria-hidden="true"></i>
        <span id="success-message"></span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    
    <div class="alert alert-danger p-1" id="error-alert" style="display: none;">
        <i class="fa fa-exclamation-triangle" style="font-size:16px;" aria-hidden="true"></i>
        <span id="error-message"></span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="content-body">
        <!-- Bordered table start -->
        <div class="row" id="table-bordered">
            <div class="col-12">
                <div class="card ">
                    <form class="dt_adv_search" action="{{ url('testimonial') }}" method="POST">
                        @csrf
                        <input type="hidden" name="form_type" value="search_testimonial">
                        <div class="card-header w-100 m-0">
                            <div class="row w-100">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label for="form-control"> Testimonial Name</label>
                                            <input type="text" id="user_id" class="form-control" placeholder="Search Testimonial Name"
                                                name="user_id" value="{{ $search_name }}" autocomplete="off" />
                                        </div>

                                        <div class="col-sm-3">
                                            <label for="form-control"> Subtitle </label>
                                            <input type="text" id="subtitle" class="form-control" placeholder="Search Subtitle "
                                                name="subtitle" value="{{ $search_subtitle }}" autocomplete="off" />
                                        </div>

                                        <div class="col-sm-3 "> 
                                            <?php 
                                               $testimonial_status = array(0=>'Inactive',1=>'Active' );    
                                            ?>
                                            <label for="form-control"> Status</label>
                                            <select id="testimonial_status" name="testimonial_status" class="form-control select2 form-control">
                                                <option value="">Select  Status</option>
                                                <?php 
                                                    foreach ($testimonial_status as $key => $value)
                                                    {
                                                        $selected = '';
                                                        if(old('testimonial_status',$search_testimonial_status) == $key){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                        <?php 
                                                    }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-sm-2 mt-2">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                            @if (!empty($search_name) || ($search_testimonial_status != '') || !empty($search_subtitle))
                                            <a title="Clear" href="{{ url('testimonial/clear_search') }}" type="button"
                                                class="btn btn-outline-primary">
                                                <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                            </a>
                                            @endif
                                        </div>
                                        <div class="col-sm-1 mt-2">
                                            <a href="{{ url('/testimonial/add') }}" class="btn btn-outline-primary float-right pr-2">
                                                <i data-feather="plus"></i><span>Add </span></a>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Sr.No</th>
                                    <th class="text-left">Testimonial Name</th>
                                    <th class="text-left">Subtitle</th>
                                    <!-- <th class="text-center">Image</th> -->
                                    <th style="text-align: center;">Active</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">

                                <?php 
                                    if(!empty($testimonial_array)){
                                        $i = $Offset;?>
                                <?php foreach ($testimonial_array as $val){
                                                $i++;?>
                                <tr>
                                    <td class="text-center">{{ $i }}</td>
                                    <td class="text-left">
                                        <?php echo ucfirst($val->name); ?>
                                    </td>
                                    <td  class="text-left">{{ ucfirst($val->subtitle) }}</td>
                                  
                                    <!-- <td class="t-center text-center"><a target="_blank"
                                href="{{ asset('uploads/testimonial_images/' . $val->testimonial_img) }}"><img
                                    style="width:50px ;"
                                    src="{{ asset('uploads/testimonial_images/' . $val->testimonial_img) }}"></a>
                        </td> -->
                                    <td class="text-center">

                                        <div class="custom-control custom-switch custom-switch-success">
                                            <input type="checkbox" class="custom-control-input" id="{{ $val->id }}" {{
                                                $val->active ? 'checked' : '' }}
                                            onclick="change_status(event.target, {{ $val->id }});" />

                                            <label class="custom-control-label" style="cursor: pointer;"
                                                for="{{ $val->id }}">

                                                <span class="switch-icon-left"></span>
                                                <span class="switch-icon-right"></span>
                                            </label>
                                        </div>
                                    </td>



                                    <td>
                                        <a href="{{ route('edit_testimonial', $val->id) }}"><i
                                                class="fa fa-edit btn btn-primary btn-sm" title="Edit"></i></a>
                                        <i class="fa fa-trash-o btn btn-danger btn-sm"
                                            onclick="remove_testimonial({{ $val->id }})" title="Delete"></i>
                                    </td>
                                </tr>
                                <?php }
                                    }else{?>
                                <tr>
                                    <td colspan="8" style="text-align:center; color:red;">No Record Found</td>
                                </tr>
                                <?php }?>
                            </tbody>
                        </table>
                        <div class="card-body">
                            <div class="d-flex justify-content-end">
                                {{ $Paginator->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Bordered table end -->
    </div>


</section>

@endsection
<script>
    function remove_testimonial(iId) {
        // alert(iId);
        var url = '<?php echo url('/testimonial/remove_testimonial') ?>';

        url = url + '/' + iId;
        // alert(url);
        Confirmation = confirm('Are you sure you want to remove this record ?');
        if (Confirmation) {

            window.location.href = url;

        }
    }

    function change_status(_this, id) {
        var active = $(_this).prop('checked') == true ? 1 : 0;

        if (confirm("Are you sure you change this status?")) {
            $.ajax({
                url: "<?php echo url('testimonial/change_status') ?>",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    active: active
                },
                success: function(result) {
                    if (result.sucess == 'true') {
                        // console.log(result);
                        // alert(result.message); 
                        $("#success-message").text(result.message); // Update success message
                        $("#success-alert").show(); // Show the success alert
                        // Optionally hide the alert after a few seconds
                        setTimeout(function() {
                            $("#success-alert").fadeOut();
                        }, 2000); // Adjust time (2000 = 2 seconds)

                    }else{
                        alert('Some error occured');
                        if(status)
                            $(_this).prop("checked" , false)
                        else
                            $(_this).prop("checked" , true)
                            return false;
                    }
                },
                error: function () {
                    alert('Some error occurred');
                }
            });
        } else {
            $(_this).prop("checked", !active);
        }
    }
  
</script>
