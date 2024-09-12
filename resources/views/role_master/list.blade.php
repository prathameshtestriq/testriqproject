@extends('layout.index')
@section('title', 'Role Master ')


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
                                        <h2 class="content-header-title float-left mb-0">Role Master </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Role Master</li>
                                        <li class="breadcrumb-item active" aria-current="page">Role Master List</li>
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


        <div class="content-body">
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card">
                        <form class="dt_adv_search" action="" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_role_master">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="form-control">Role Name</label>
                                                <input type="text" id="role_name" class="form-control"
                                                    placeholder="Role Name" name="role_name"
                                                    value="{{old('role_name',$search_role_name)}}" autocomplete="off" />
                                            </div>
                                            
                                            <div class="col-sm-3 col-12">
                                                <?php 
                                                   $Role_status = array(0=>'Inactive',1=>'Active' );    
                                                ?> 
                                                <label for="form-control"> Status</label>
                                                <select id="role_status" name="role_status" class="form-control select2 form-control">
                                                    <option value="">Select  Status</option>
                                                    <?php 
                                                        foreach ($Role_status as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if(old('role_status',$search_role_status) == $key){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_role_name) || ($search_role_status != ''))
                                                    <a title="Clear" href="{{ url('/role_master/clear_search') }}"
                                                        type="button" class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                            </div>

                                            <div class="col-sm-3 mt-2">
                                                <a href="{{ url('role_master/add') }}"
                                                    class="btn btn-outline-primary float-right pr-2">
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
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-left">Role Name</th>
                                        <th class="text-center">Role Access</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (!empty($role_master)){
                                        $i = $Offset;?>
                                    <?php foreach ($role_master as $val){
                                      
                                                $i++;
                                    ?>
                                        <tr>
                                            <td class="text-center">{{ $i }}</td>
                                            <td class="text-left">{{ ucfirst($val->name) }}</td>
                                             <td class="text-center"><a href="{{ url('role_access', $val->id ) }}"><i class="fa fa-eye" style="cursor:pointer;" ></i></a></td>
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
                                                <a href="{{ url('role_master/edit', $val->id ) }}">
                                                    <i class="fa fa-edit btn btn-primary btn-sm" title="Edit"></i>
                                                </a>
                                           
                                                <i class="fa fa-trash-o btn btn-danger btn-sm" onclick="delrolemaster({{ $val->id }})"
                                                  title="Delete"></i>
                                            </td>
                                        </tr>
                                        <?php }
                                    }else{?>
                                        <tr>
                                            <td colspan="13" class="text-center" style="color: red">No Record Found</td>
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
   function delrolemaster(id) {
        // alert(id);
        var url = '<?php echo url('role_master/delete'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove or delete role master details ?');
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
                url: "<?php echo url('role_master/change_status'); ?>",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function(result) {
                    if (result == 1) {
                        console.log(result);
                        alert('Status changed successfully')
                        //location.reload(); 
                    } else {
                        alert('Some error occured');
                        if (status)
                            $(_this).prop("checked", false)
                        else
                            $(_this).prop("checked", true)
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
