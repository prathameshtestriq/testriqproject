@extends('layout.index')
@section('title', 'Category Type ')


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
                                        <h2 class="content-header-title float-left mb-0">Category Type </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Category Type</li>
                                        <li class="breadcrumb-item active" aria-current="page">Category Type List</li>
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
                        <form class="dt_adv_search" action="{{ url('category') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_category">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label for="form-control">Category Type Name:</label>
                                                <input type="text" id="category_name" class="form-control"
                                                    placeholder="Category Type Name" name="category_name"
                                                    value="{{ $search_category }}" autocomplete="off" />
                                            </div>
                                            <div class="col-sm-4 ">
                                                <?php 
                                                   $category_status = array(0=>'Inactive',1=>'Active' );    
                                                ?>
                                                <label for="form-control"> Status:</label>
                                                <select id="category_status" name="category_status" class="form-control select2 form-control">
                                                    <option value="">Select  Status</option>
                                                    <?php 
                                                        foreach ($category_status as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if(old('category_status',$search_category_status) == $key){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>


                                            <div class="col-sm-4 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_category) || ($search_category_status != ''))
                                                    <a title="Clear" href="{{ url('category/clear_search') }}"
                                                        type="button" class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mt-2">
                                        <a href="{{ url('/category/add_edit') }}"
                                            class="btn btn-outline-primary float-right pr-2">
                                            <i data-feather="plus"></i><span>Add </span></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-left">Category Type Name</th>
                                        {{-- <th class="text-center">Logo Name</th> --}}
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                               
                                <tbody class="text-center">
                                
                                    <?php 
                                    if (!empty($category_array)){
                                        $i = $Offset;?>
                                        <?php foreach ($category_array as $category){
                                                $i++;?>
                                              <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-left">{{ $category->name }}</td>
                                                {{-- <td class="text-center">{{ $category->logo }}</td> --}}
                                                <td class="text-center">
                                                    <div class="custom-control custom-switch custom-switch-success">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="{{ $category->id }}" {{ $category->active ? 'checked' : '' }}
                                                            onclick="change_status(event.target, {{ $category->id }});" />
                                                        <label class="custom-control-label" for="{{ $category->id }}"></label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ url('/category/add_edit', $category->id) }}">
                                                        <i class="fa fa-edit btn btn-primary btn-sm" title="Edit"></i>
                                                    </a>
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                        onclick="delCategory({{ $category->id }})" title="Delete"></i>
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
        </div>
    </section>
@endsection

<script>
   
    function delCategory(id) {
        var bConfirm = confirm('Are you sure you want to remove this record ?');
        if (bConfirm) {
            var url = '{{ url('category/delete') }}/' + id;
            window.location.href = url;
        }
    }

    function change_status(_this, id) {
        //  alert(id)
        ;
        var status = $(_this).prop('checked') == true ? 1 : 0;
        // alert(status);

        if (confirm("Are you sure want to change this status?")) {
            let _token = $('meta[name="csrf-token"]').attr('content');
            //alert(_token);
            $.ajax({
                url: "<?php echo url('category/change_status'); ?>",
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
