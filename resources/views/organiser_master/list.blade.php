@extends('layout.index')
@section('title', 'Organiser Master')

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
                                        <h2 class="content-header-title float-left mb-0">Organiser Master</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Organiser</li>
                                        <li class="breadcrumb-item active" aria-current="page">Organiser List</li>
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
            <!-- Bordered table start -->
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card ">
                        <form class="dt_adv_search" action="{{ url('/organiser_master') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_user">
                            <div class="card-header w-100 m-0"> 
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="form-control">Organiser Name</label>
                                                <input type="text" id="organiser_name" class="form-control"
                                                    placeholder="Organiser Name" name="organiser_name" value="{{ $search_organiser_name }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-3 col-12">
                                                <label for="form-control"> User Name</label>
                                                <select id="organiser_user_name" name="organiser_user_name" class="form-control select2 form-control">
                                                    <option value="">Select  user name</option>
                                                    <?php 
                                                        foreach ($UserDetails as $value)
                                                        {
                                                            $selected = '';
                                                            if(old('organiser_user_name',$search_organiser_user_name) == $value->id){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->name; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3">
                                                <label for="form-control">GST Number</label>
                                                <input type="text" id="gst_number" class="form-control"
                                                    placeholder="GST Number" name="gst_number" value="{{ $search_gst_number }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-2 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if ((!empty($search_organiser_name)) || (!empty($search_gst_number)) || !empty($search_organiser_user_name))
                                                    <a title="Clear" href="{{ url('/organiser_master/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif
                                            </div>

                                            <div class="col-sm-1 mt-2 float-right">
                                                <a href="{{ url('organiser_master/add') }}" class="btn btn-outline-primary float-right pr-2">
                                                    <i data-feather="plus"></i><span>Add</span></a>
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
                                        <th class="text-left">Organiser Name</th>
                                        <th class="text-left">User Name</th>                                    
                                        <th class="text-left">Email ID</th>
                                        <th class="text-left">Contact Number</th>
                                        <th class="text-left">Gst Number</th>
                                        <th class="text-left">Logo Image</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                    
                                    <?php 
                                    if (!empty($OrganiserDetails)){
                                        $i = $Offset;
                                        // $i = 0;
                                        ?>
                                        <?php foreach ($OrganiserDetails as $val){
                                                $i++;?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                               
                                                <td class="text-left">{{ ucfirst($val->name) }}</td>
                                                <td class="text-left">{{ ucfirst($val->user_name) }}</td>
                                                <td class="text-left">{{ ucfirst($val->email) }}</td>
                                                <td class="text-left">{{ $val->mobile }}</td>
                                                <td class="text-left">{{ $val->gst_number }}</td>
                                                <td class="t-center text-center">
                                                    @php
                                                        $imagePath = public_path('uploads/organiser/logo_image/' . $val->logo_image);
                                                    @endphp
                                                    @if (file_exists($imagePath) && !empty($val->logo_image))
                                                        <a target="_blank" title="View Image"
                                                            href="{{ asset('uploads/organiser/logo_image/' . $val->logo_image) }}">
                                                            <img style="width:50px;" src="{{ asset('uploads/organiser/logo_image/' . $val->logo_image) }}" alt="Logo Image">
                                                        </a>
                                                    @else
                                                      <?php   echo ' '; ?>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ url('organiser_master/edit', $val->id) }}"><i
                                                        class="fa fa-edit btn btn-primary btn-sm" title="edit"></i></a>
                                                        <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                        onclick="delorganiser({{ $val->id }})" title="delete"></i>
                                                </td>
                                               
                                            </tr>
                                      <?php }
                                    }else{?>
                                        <tr>
                                            <td colspan="16" style="text-align:center; color:red;">No Record Found</td>
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

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src={{ asset('/app-assets/js/scripts/jquerycdn.js') }}></script>
<script>
     function delorganiser(id) {
        // alert(id);
        var url = '<?php echo url('organiser_master/delete'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove organiser ?');
        if (bConfirm) {
            window.location.href = url;
        } else {
            return false;
        }
     }
</script>
