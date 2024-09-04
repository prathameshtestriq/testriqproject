@extends('layout.index')
@section('title', 'Remittance Management ')


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
                                        <h2 class="content-header-title float-left mb-0">Remittance Management</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Remittance </li>
                                        <li class="breadcrumb-item active" aria-current="page">Remittance List</li>
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


        <div class="content-body">
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card">
                        <form class="dt_adv_search" action="" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_remittance_management">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <label for="form-control">Remittance Name</label>
                                                <input type="text" id="remittance_name" class="form-control"
                                                    placeholder="Remittance Name" name="remittance_name"
                                                    value="{{old('remittance_name',$search_remittance_name)}}" autocomplete="off" />
                                            </div>
                                            
                                            <div class="col-sm-2 ">
                                                <label for="form-control">Start Remittance Date</label>
                                                <input type="date" id="start_remittance_date" class="form-control"
                                                    placeholder="Start Date" name="start_remittance_date" value="{{ old('start_remittance_date', $search_start_remittance_date ? \Carbon\Carbon::parse($search_start_remittance_date)->format('Y-m-d') : '') }}"   
                                                    autocomplete="off" />
                                            </div>
                                            
                                            <div class="col-sm-2">
                                                <label for="form-control">End Remittance Date</label>
                                                <input type="date" id="end_remittance_date" class="form-control"
                                                    placeholder="End Date" name="end_remittance_date" value="{{ old('end_remittance_date', $search_end_remittance_date ? \Carbon\Carbon::parse($search_end_remittance_date)->format('Y-m-d') : '') }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-2 col-12">
                                                <?php 
                                                   $remittance_status = array(0=>'Inactive',1=>'Active' );    
                                                ?> 
                                                <label for="form-control"> Status</label>
                                                <select id="remittance_status" name="remittance_status" class="form-control select2 form-control">
                                                    <option value="">Select  Status</option>
                                                    <?php 
                                                        foreach ($remittance_status as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if(old('remittance_status',$search_remittance_status) == $key){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-2 col-12">
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

                                            <div class="col-sm-2 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_remittance_name) || !empty($search_start_remittance_date) || !empty($search_end_remittance_date) || ($search_remittance_status != '')||!empty($search_event_id))
                                                    <a title="Clear" href="{{ url('/remittance_management/clear_search') }}"
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

                        <form method="post" action="{{route('remittance_management.import_remittance_management') }}" enctype="multipart/form-data">
                            @csrf 
                            <div class="row">
                                <div class="col-sm-2 ">
                                    <div class="form-group p-2">
                                       Remittance Data Import <span style="color:red;">*</span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group mr-3">
                                        <input type="file" id="rem_file" name="rem_file" class="form-control">
                                        <input type="hidden" name="remittance_id">
                                        <h5><small class="text-danger" id="rem_file_err"></small></h5>
                                        @error('rem_file')
                                            <span class="error" style="color:red;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group m-0">
                                       <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>

                                <div class="col-sm-5 float-right ">
                                    <div class="float-right">
                                        <a href="{{ url('remittance_management/sample_excel_export') }}" class="btn btn-danger text-white float-right mr-1 " title="Download">Sample Excel Download </a>
                                    </div>
                                    @if (!empty($Remittance)) 
                                        <div class="float-right">
                                            <a href="{{ url('remittance_management/export_remittance_management') }}" class="btn btn-danger text-white float-right mr-1 ml-2 " title="Download">Download </a>
                                        </div>
                                    @endif
                                    <div class="float-right ml-3">
                                    <a href="{{ url('remittance_management/add') }}"
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
                                        <th class="text-left">Remittance Name</th>
                                        <th class="text-left">Remittance Date</th>
                                        <th class="text-left">Event Name</th>
                                        <th class="text-center">Gross Amount</th>
                                        <th class="text-center">Service Charge</th>
                                        <th class="text-center">SGST</th>
                                        <th class="text-center">CGST</th>
                                        <th class="text-center">IGST</th>
                                        <th class="text-center">Deductions</th>
                                        <th class="text-center">TDS</th>
                                        <th class="text-center">Amount Remitted</th>
                                        <th class="text-left">Bank Reference</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (!empty($Remittance)){
                                        $i = $Offset;?>
                                    <?php foreach ($Remittance as $val){
                                      
                                                $i++;
                                    ?>
                                        <tr>
                                            <td class="text-center">{{ $i }}</td>
                                            <td class="text-left">{{ ucfirst($val->remittance_name) }}</td>
                                            <td class="text-left">{{  date('d-m-Y',$val->remittance_date) }}</td>
                                            <td class="text-left">{{ ucfirst($val->event_name) }}</td>
                                            <td class="text-center">{{ number_format($val->gross_amount, 2)  }}</td>
                                            <td class="text-center">{{ number_format($val->service_charge,2) }}</td>
                                            <td class="text-center">{{ number_format($val->Sgst,2) }}</td>
                                            <td class="text-center">{{ number_format($val->Cgst,2) }}</td>
                                            <td class="text-center">{{ number_format($val->Igst,2) }}</td>
                                            <td class="text-center">{{ number_format($val->deductions,2) }}</td>
                                            <td class="text-center">{{ number_format($val->Tds,2) }}</td>
                                            <td class="text-center">{{ number_format($val->amount_remitted,2) }}</td>
                                            <td class="text-left">{{ ucfirst($val->bank_reference) }}</td>
                                            <td class="text-center">
                                                <div class="custom-control custom-switch custom-switch-success">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="{{ $val->id }}" {{ $val->active ? 'checked' : '' }}
                                                        onclick="change_status(event.target, {{ $val->id }});" />
                                                    <label class="custom-control-label" for="{{ $val->id }}">
                                                        <span class="switch-icon-left"></span>
                                                        <span class="switch-icon-right"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                {{-- {{ url('/category/add_edit', $category->id) }} --}}
                                                <a href="{{ url('remittance_management/edit', $val->id ) }}">
                                                    <i class="fa fa-edit btn btn-primary btn-sm" title="Edit"></i>
                                                </a>
                                                {{-- onclick="delCategory({{ $category->id }})" --}}
                                                <i class="fa fa-trash-o btn btn-danger btn-sm" onclick="delremittance({{ $val->id }})"
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
   function delremittance(id) {
        // alert(id);
        var url = '<?php echo url('remittance_management/delete'); ?>';
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
                url: "<?php echo url('remittance_management/change_status'); ?>",
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
