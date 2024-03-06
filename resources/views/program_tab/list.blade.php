@extends('layout.index')
@section('title', 'Program Tab List')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-8 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Program Tab List</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-4 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">program</a>
                        </li>
                        <li class="breadcrumb-item active">program Tab List
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>       
  </section>
    <section>
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
                        <form class="dt_adv_search" action="{{ url('program_tabs') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_progrm_tab">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-8">
                                        <div class="row">
                                            
                                            <div class="col-md-3 col-12">   
                                                <div class="form-group">
                                                    <label class="form-label" for="validationTooltip01"> Program Name </label>
                                                    <select class=" form-control form-select " id="program_id"  name='program_name'>
                                                        <option value=''>-- Select Program --</option>
                                                        
                                                        <?php 
                                                        foreach ($master_program as $val)
                                                        
                                                        {
                                                            $selected = '';
                                                            if(old('program_name',$program_name) == $val->id){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>><?php echo $val->program_name; ?></option>
                                                            <?php 
                                                        }
                                                        ?>
                                
                                                    </select>
                                                    
                                                </div>
                                            </div>
                                            

                                            <div class="col-md-4 col-12">   
                                                <div class="form-group m-2">
                                                    <button type="submit" class="btn btn-primary">Search</button>
                                                    @if ($brand_name || $program_name)
                                                        <a title="Clear" href="{{ url('program_tabs/clear_search') }}" type="button"
                                                            class="btn btn-outline-primary ">
                                                            <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <a href="{{ url('/program_tabs/add') }}" class="btn btn-outline-primary float-right">
                                            <i data-feather="plus"></i><span>Add Program Tab</span></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        
                                        <th class="text-left">Program Name</th>
                                        <th class="text-left">Tab Name</th>
                                        <th class="text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                
                                    <?php 
                                    if (!empty($program_tab_array)){
                                        $i = $Offset;?>
                                        <?php foreach ($program_tab_array as $val){
                                         
                                                $i++;?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                
                                                <td class="text-left">{{ $val->program_name }}</td>
                                                <td class="text-left">{{ $val->tab_title }}({{ $val->tab_name }})</td>
                                           
                                                <td>
                                                    <a data-toggle="modal" id="smallbtn" data-target="#smallModal"
                                                        href="javascript:void(0);" onClick="programform({{ $val->id }},{{ $val->program_id }},{{ $val->tab_id }})"  title="add" data-bs-toggle="modal" data-bs-target="#exampleModallaptop1" >
                                                    <i class="btn btn-success btn-sm " >Add View Program Form</i>
                                                    </a>
                                                    <a href="{{ url('/program_tabs/edit', $val->id) }}"><i
                                                            class="fa fa-edit btn btn-primary btn-sm " title="edit"></i></a>
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                        onclick="delprogram_tab({{ $val->id }})" title="delete"></i>
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
                                    {{ $Paginator }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Bordered table end -->
        </div>
       

    </section>
    <div class="modal fade" id="program_form_details_modal" tabindex="-1" role="dialog" aria-labelledby="business_customer_details_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">
            <div class="col-xl-12">
              <div class="card social-profile mb-0">
  
                <div class="card-body" id="program_form_details_body">
                   
                </div>         
                
              </div>
            </div>  
          </div>
        </div>
    </div>

    
    

@endsection
<script>
    function delprogram_tab(id) {
        // alert(id);
        var url = '<?php echo url('/program_tabs/remove/'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove this Program Tab');
        if (bConfirm) {
            window.location.href = url;
        } else {
            return false;
        }
    }

 
    function programform(program_tab_id,program_id,tab_id){
        // alert(program_tab_id);
        
        var url;
        $.ajax({
                url: "<?php echo url('/program_tabs/program_form_ajx') ?>/"+program_tab_id,
            //  alert(url);
            beforeSend: function() {
                $('#loader').show();
            },
            data:{
                    'program_tab_id':program_tab_id,
                    'program_id':program_id,
                    'tab_id':tab_id,
                },
            // return the result
            success: function(result) {
                // console.log(result);
                
                $('#program_form_details_body').html(result);
                $('#program_form_details_modal').modal("show");
            },
            complete: function() {
                $('#loader').hide();
            },
            error: function(jqXHR, testStatus, error) {
                console.log(error);
                alert("Page " + url + " cannot open. Error:" + error);
                $('#loader').hide();
            },
            timeout: 8000
        })
    }

</script> 

