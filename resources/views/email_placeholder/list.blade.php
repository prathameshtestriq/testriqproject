@extends('layout.index')
@section('title', 'Email Placeholder Management')


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
                                        <h2 class="content-header-title float-left mb-0">Email Placeholder Management</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Email Placeholder Management </li>
                                        <li class="breadcrumb-item active" aria-current="page">Email Placeholder Management List</li>
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
                        {!! $message !!}
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
                        {!! $message !!}
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
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card">
                        <form class="dt_adv_search" action="" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_email_placeholder">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            
                                            <div class="col-sm-3 p-1">
                                                <label for="form-control">Name</label>
                                                <input type="text" id="search_Name" class="form-control"
                                                    placeholder="Search Name" name="search_Name" value="{{old('search_Name',$search_placeholder_name)}}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-3 col-12 p-1">
                                                <label for="form-control"> Events</label>
                                                <select id="event" name="event" class="form-control select2 form-control">
                                                    <option value="">Select  Event</option>
                                                    <?php 
                                                        foreach ($EventsData as $value)
                                                        {
                                                            $selected = '';
                                                            if(old('event',$search_event_id) == $value->id){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo ucfirst($value->name); ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>


                                            <div class="col-sm-3 p-1 col-12">
                                                <?php 
                                                   $placeholder_status = array(0=>'Inactive',1=>'Active' );    
                                                ?> 
                                                <label for="form-control"> Status</label>
                                                <select id="placeholder_status" name="placeholder_status" class="form-control select2 form-control">
                                                    <option value="">Select  Status</option>
                                                    <?php 
                                                        foreach ($placeholder_status as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if(old('placeholder_status',$search_placeholder_status) == $key){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3 mt-2 p-1">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_placeholder_name) || ($search_placeholder_status != '') || !empty($search_event_id))
                                                    <a title="Clear" href="{{ url('/email_placeholder_management/clear_search') }}"
                                                        type="button" class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="row px-2">
                            <div class="col-sm-8 float-right">
                                <h2 class="content-header-title float-left mb-0">Email Placeholder Management details</h2>
                            </div>
                            <div class="col-sm-4 d-flex justify-content-end float-right">
                                <div class="d-flex justify-content-end">
                                    <!-- Add button -->
                                    <a href="{{ url('email_placeholder_management/add') }}" 
                                       class="btn btn-outline-primary pr-2 mr-1"> <!-- Added 'mr-3' for spacing -->
                                        <i data-feather="plus"></i><span> Add </span>
                                    </a> 
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-left">Event Name</th>
                                        <th class="text-left">Question</th>
                                        <th class="text-left" >Placeholder Name</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" style="width: 150px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (!empty($Email_placeholder)){
                                        $i = $Offset;?>
                                    <?php foreach ($Email_placeholder as $val){
                                      
                                                $i++;
                                    ?>
                                        <tr>
                                            <td class="text-center">{{ $i }}</td>
                                            <td class="text-left">{{ ucfirst($val->event_name) }}</td>
                                            <td class="text-left">{{  $val->question_form_name }}</td>
                                            <td class="text-left">{{ ucfirst($val->placeholder_name) }}</td>
                                            <td class="text-center">
                                                <div class="custom-control custom-switch custom-switch-success">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="{{ $val->id }}" {{ $val->status ? 'checked' : '' }}
                                                        onclick="change_status(event.target, {{ $val->id }});" />
                                                    <label class="custom-control-label" for="{{ $val->id }}">
                                                        <span class="switch-icon-left"></span>
                                                        <span class="switch-icon-right"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                {{-- {{ url('/category/add_edit', $category->id) }} --}}
                                                <a href="{{ url('email_placeholder_management/edit', $val->id ) }}">
                                                    <i class="fa fa-edit btn btn-primary btn-sm" title="Edit"></i>
                                                </a>
                                                {{-- onclick="delCategory({{ $category->id }})" --}}
                                                <i class="fa fa-trash-o btn btn-danger btn-sm" onclick="delemailplaceholder({{ $val->id }})"
                                                  title="Delete"></i>
                                            </td>
                                        </tr>
                                        <?php }
                                    }else{?>
                                        <tr>
                                            <td colspan="17" class="text-center" style="color: red">No Record Found</td>
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
        </div>
    </section>
@endsection

<script>
   function delemailplaceholder(id) {
        // alert(id);
        var url = '<?php echo url('email_placeholder_management/delete'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove this record ?');
        if (bConfirm) {
            window.location.href = url;
        } else {
            return false;
        }
    }


    function change_status(_this, id) {
        //  alert(id)
        // ;
        var status = $(_this).prop('checked') == true ? 1 : 0;
        // alert(status);

        if (confirm("Are you sure want to change this status?")) {
            let _token = $('meta[name="csrf-token"]').attr('content');
            //alert(_token);
            $.ajax({
                url: "<?php echo url('email_placeholder_management/change_status'); ?>",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
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
                error: function() {
                    alert('Some error occured');
                    if (status)
                        $(_this).prop("checked", false)
                    else
                        $(_this).prop("checked", true)
                    return false;
                }
            });
        } else {
            if (status)
                $(_this).prop("checked", false)
            else
                $(_this).prop("checked", true)
            return false;
        }
    }
</script>


