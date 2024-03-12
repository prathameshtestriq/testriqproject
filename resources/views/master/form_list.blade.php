@extends('layout.index')
@section('title', 'Users List')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Master Question List</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-5 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Master Question</a>
                        </li>
                        <li class="breadcrumb-item active">Master Question List
                        </li>
                    </ol>
                </div>
            </div>
        </div>
		<div class="col-md-1">
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
                        <form class="dt_adv_search" action="{{ url('master_questions') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_question">
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-9">
                                        <div class="row">
                                            
                                            <div class="col-sm-3">
                                                <label class="form-label" for="validationCustom04">Form Name:</label>
                                                <?php 
                                                    //   dd($search_name);
                                                    ?>
                                                <select class="form-select form-control" name='name'>
                                                    <option value=''>Select Form Name</option>
                                                    <?php 
                                                        foreach ($master_forms as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if($search_name == $value->id){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                    <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->form_name; ?></option>
                                                    <?php 
                                                        }
                                                        ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="form-label" for="validationCustom04">Form Type:</label>
                                                <?php 
                                                    //   dd($master_types);
                                                    ?>
                                                <select class="form-select form-control" name='type'>
                                                    <option value=''>Select Form Type</option>
                                                    <?php 
                                                        foreach ($master_types as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if($search_type == $value->type_name){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                    <option value="<?php echo $value->type_name; ?>" <?php echo $selected; ?>><?php echo $value->type_name; ?></option>
                                                    <?php 
                                                        }
                                                        ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="form-label" for="validationCustom04">Activity:</label>

                                                <input type="text" id="activity" class="form-control"
                                                    placeholder="Activity" name="activity" value="{{ $search_activity }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-4 mt-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if ($search_name || $search_activity || $search_type) 
                                                    <a title="Clear" href="{{ url('master_questions/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary ">
                                                        <i data-feather="rotate-ccw" ></i> Clear Search
                                                    </a>
                                                @endif
                                            </div>

                                            
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <a href="{{ url('master_questions/add_edit') }}" class="btn btn-outline-primary float-right">
                                            <i data-feather="plus"></i><span>Add Master Question</span></a>
                                    </div>
                                </div>
                            </div>
                        </form>
						
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-left">Form Name</th>
                                        <th class="text-left">Name Key</th>
                                        <th class="text-left">Description</th>
                                        <th class="text-left">Type</th>
                                        <th class="text-left">Action</th>
                                        

                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                
                                    <?php 
                                    if (!empty($master_que)){
                                       $i =0;?>
                                        <?php foreach ($master_que as $val){
                                                // dd($Offset);
                                                $Offset++;
                                                $i++;?>
                                            <tr>
                                                <td class="text-center">{{ $Offset }}</td>
                                                <td class="text-left">{{ $val->form_name }} </td>
                                                <td class="text-left">{{ $val->name_key }} </td>
                                                <td class="text-left">{{ $val->name_description }}</td>
                                                <td class="text-left">{{ $val->type }}</td>
                                                <td>
                                                    <div class="d-inline-flex">
                                                        <a href="{{ url('master_questions/add_edit', $val->id) }}" style="margin-right: 4px;"><i
                                                            class="fa fa-edit btn btn-primary btn-sm " title="edit"></i></a>
    
                                                     <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                            onclick="delquestion({{ $val->id }})" title="delete"></i>                                          
                                                   
                                                    </div> 
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

@endsection
<script>
    function delquestion(id) {
        // alert(id);
        var url = '<?php echo url('master_questions/delete'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove this question');
        if (bConfirm) {
            window.location.href = url;
        } else {
            return false;
        }
    }
 
 </script>

       
    
    


