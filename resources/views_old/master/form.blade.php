@extends('layout.index')

@if (isset($id))
    @section('title', 'Edit Master Question')
@else
    @section('title', 'Add Master Question')
@endif
<!-- Dashboard Ecommerce start -->
@section('content')
    <section>

        <div class="content-body">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12 d-flex">
                            <h2 class="content-header-title float-start mb-0"><?php if(!empty($id)){
                                echo "Edit Master Question";} else{
                            echo  'Add Master Question';}?>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <div class="mb-1 breadcrumb-right">
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb" style="justify-content: flex-end">
                                <li class="breadcrumb-item"><a href="#">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Master</a>
                                </li>
                                <li class="breadcrumb-item active">Question
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
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
        @endif

        <div class="content-body">
            <section id="multiple-column-form">
                <div class="row justify-content-center">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <form class="form" action="" method="post">
                                    <input type="hidden" name="form_type" value="add_edit_master">
                                    {{ csrf_field() }}
                                
                                    <div class="row">
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                
                                                <label for="form" >Master Form<span style="color:red;"> *</span></label>
                                                @php $disabled='';
                                                if(!empty ($id))
                                                {
                                                   $disabled='disabled';
                                                }
                                            @endphp
                                                <select name="form" id="form" class="form-control form-select" {{ $disabled }}>
                                                    <option value="">Select Form</option>
                                                    <?php 
                                                 
                                                    foreach ($master_forms as $value)
                                                    {
                                                        $selected = '';
                                                        if(old('form',$form_id) == $value->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                       
                                                        <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->form_name; ?></option>
                                                        
                                                        <?php 
                                                    }
                                                    ?>
                                                 
                                                </select>
                                                <h5><small class="text-danger" id="form_err"></small></h5>
                                                @error('form')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            
                                            <div class="form-group">
                                                <label for="city-column">Select Country <span
                                                    style="color:red;">*</span></label>
                                                   <?php $country_name=[];
                                                   if(!empty($country_ids))
                                                   { 
                                                       $country_name=explode(",",$country_ids);
                                                   }
                                                    
                                                   ?>
                                                    <select name="country[]" id="myDropdown" class="form-control form-select select2"  <?php if($country_ids!=0) echo 'multiple';?> onchange="checkDropdown()" >
                                                        <option value="0" >All Country</option>
                                                        <?php  
                                                        foreach ($master_countries as $value)
                                                        {  $selected = 'selected';
                                                            
                                                            if(!in_array($value->id,$country_name)){
                                                                $selected = '';
                                                            }
                                                            
                                                            ?>
                                                           
                                                            <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->country_name; ?></option>
                                                            
                                                            <?php 
                                                        }
                                                        ?>

                                                     
                                                </select>
                                                <h5><small class="text-danger" id="country_ids_err"></small></h5>
                                                @error('country')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                               
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="question">Question <span style="color:red;">*</span></label>
                                                <input type="text" id="question" class="form-control"
                                                    placeholder="Question" name="question" autocomplete="off" value="{{ old('question',$name_description) }}" />
                                                    <h5><small class="text-danger" id="question_err"></small></h5>
                                                    @error('question')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="type" >Type<span style="color:red;"> *</span></label>
                                               
                                                <select name="type" id="type" class="form-control form-select">
                                                    <option value="">Master Type</option>
                                                    <?php 
                                                 
                                                    foreach ($master_types as $value)
                                                    {
                                                        $selected = '';
                                                        if(old('type',$type) == $value->type_name){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                       
                                                        <option value="<?php echo $value->type_name; ?>" <?php echo $selected; ?>><?php echo $value->type_name; ?></option>
                                                        
                                                        <?php 
                                                    }
                                                    ?>
                                                 
                                                </select>
                                                <h5><small class="text-danger" id="type_err"></small></h5>
                                                @error('type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            </div>
                                        </div>
                                       
                                      
                                        <div class="col-12 text-center mt-1">
                                                <?php if(!empty($id)){
                                                    // dd($options_inline,$options_table);
                                                    
                                                    if($type== 'radio'|| $type== 'selection'|| $type== 'checkbox' ){?>
                                                   
                                                    <a href="{{ url('master_questions/view/'.$id) }}" ><i class="fa btn btn-success float-lg-right" id="savebtn" >Add Option</i> </a>
                                                
                                                <?php }else{
                                                    echo '';
                                                }
                                                }?>
                                                <button type="submit" class="btn btn-primary mr-1" onClick="return ()">Submit</button>
                                                <a href="{{ url('/master_questions') }}" type="reset"
                                                class="btn btn-outline-secondary btn-danger">Cancel</a>
                                            </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        {{-- <div class="modal fade" id="master_question_modal" tabindex="-1" role="dialog" aria-labelledby="master_question_modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
              <div class="modal-content">
                <div class="col-xl-12">
                  <div class="card social-profile mb-0">
               
                    <div class="card-body" >
                        <form class="form" id="model" action="{{ url('master_questions/view/'.$id) }}" method="post">
                            @csrf
                            <div id="master_question_modal_body">
                                
                            </div>
                        </form>
                    </div>         
                    <br>
                  </div>
                </div>  
              </div>
            </div>
        </div> --}}
        <div class="modal fade" id="link_table_modal" tabindex="-1" role="dialog" aria-labelledby="link_table_modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
              <div class="modal-content">
                <div class="col-xl-12">
                  <div class="card social-profile mb-0">
               
                    <div class="card-body" >
                        <form class="form" id="model" action="{{ url('master_questions/link/'.$id) }}" method="post">
                            @csrf
                            <div id="link_table_modal_body">
                                
                            </div>
                        </form>
                    </div>         
                    <br>
                  </div>
                </div>  
              </div>
            </div>
        </div>

        
      
    </section>
   
@endsection
<script>
        //     var selectedValue = $("#myDropdown").val();
        // alert(selectedValue);
    function checkDropdown()
    {
        var selectedValue = $("#myDropdown").val();
       
        if (selectedValue == 0) {
       
        $("#myDropdown").attr("multiple", false);
        } else {
       
        $("#myDropdown").attr("multiple", true);
        }
    }



function closebtn()
{
    // alert('here');
    $("#link_table_modal").modal("toggle");
    location.reload();
}
</script>

