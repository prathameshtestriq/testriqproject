@extends('layout.index')
@section('title', 'Kpi ')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-7 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0"> Kpi</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-5 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Master Kpi</a>
                        </li>
                        <li class="breadcrumb-item active">Add Kpi Question
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
                        <form class="dt_adv_search"  method="POST">
                            @csrf
                            <input type="hidden" name='form_type' value='kpi_questions'>
                            <div class="card-header w-100 m-0">
                                {{-- <div class="row w-100">
                                    <div class="col-sm-12"> --}}
                                        <div class="col-md-6 col-12">
                                            {{-- <?php dd($question_id); ?> --}}
                                            <div class=" form-group  form-select" id="device">
                                              <label class="form-label" for="validationTooltip01">questions <span style="color:red"> *</span></label>
                                              <select class=" form-control form-select select2 " id="questions_id"  name='questions_id[]' multiple>
                                                  <option value='0'>-- Select questions --</option>
                                                  
                                                  <?php 
                                                  foreach ($questions as $val)
                                                  {
                                                      ?>
                                                      <option value="<?php echo $val->id; ?>" ><?php echo $val->form_name. ' ('.$val->name_description.')' ; ?></option>
                                                      <?php 
                                                  }
                                                  ?>
                                              
                                              </select>
                                              @error('questions_id')
                                              <span class="text-danger">{{ $message }}</span>
                                              @enderror
                                              <h5><small class="text-danger" id="question_id_err"></small></h5>
                                          </div></div>
                                        <div class="col-md-6 col-12">

                                             <button type="submit" class="btn btn-primary" name="kpi_submit" onclick="return question_validation()" >Submit</button>
                                             <a href="{{ url('/master_kpi') }}"
                                            class="btn btn-outline-secondary">Cancel</a>  
                                            </div>
                                    {{-- </div>
                                </div> --}}
                            </div>
                        </form>
                        <div class="table-responsive text-center">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr. No</th>
                                        <th>Kpi Name</th>
                                        {{-- <th class="text-left">Active</th> --}}
                                        <th >Actions</th> 
                                    </tr>
                                </thead>
                                <tbody >
                                
                                    <?php 
                                    if (!empty($assign_que)){
                                       $i =0;?>
                                        <?php foreach ($assign_que as $val){
                                          
                                                $i++;?>
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ $val->form_name }}({{ $val->name_description }})</td>
                                                <td>
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                    onclick="delQuestion({{ $val->field_id }},{{ $Kpi_id }})"></i>
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
                           
                        </div>
                    </div>

                </div>
            </div>
            <!-- Bordered table end -->
        </div>


    </section>

@endsection
 <script>
    function question_validation() {
        if ($('#questions_id').val() == "") {
            $('#questions_id').parent().addClass('has-error');
            $('#question_id_err').html('Please Choose Any Option.');
            $('#questions_id').focus();
            $('#questions_id').change(function() {
                $('#questions_id').parent().removeClass('has-error');
                $('#question_id_err').html('');
            });
            return false;
        }
        
    }
    function delQuestion(que_id,kpi_id)
    {
        // alert(que_id)
        var url = '<?php echo url('kpi_question/remove/'); ?>';
        url = url + '/' + kpi_id +'/' +que_id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove this Question');
        if (bConfirm) {
            window.location.href = url;
        } else {
            return false;
        }
    }

   

</script>     

