
@extends('layout.index')

    @section('title', 'Add Program Details')
<!-- Dashboard Ecommerce start -->
@section('content')
    <section>
        <div class="content-body">
            <div class="content-header row">
                <div class="content-header-left col-md-7 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12 d-flex">
                            <h2 class="content-header-title float-start mb-0">
                                
                                Add Question Program
                              
                                </h2>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-5 col-12 d-md-block d-none">
                    <div class="mb-1 breadcrumb-right">
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb" style="justify-content: flex-end">
                                <li class="breadcrumb-item"><a href="#">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Program</a>
                                </li>
                                <li class="breadcrumb-item active">
                                  
									Add Question Program
                                    
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="demo-spacing-0 mb-1">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
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
			
        <div class="content-body">
            <section id="multiple-column-form">
                <div class="row justify-content-center">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <form class="form"
    id="categoryform1" action="" method="POST">
    <input type="hidden" name="form_type" value="question_text_change">

    
        {{ csrf_field() }}
        
        <div class="row">
			<div class="col-md-4 col-12">
				
            <div class=" form-group  form-select" id="device">
                <label class="form-label" for="validationTooltip01"> Form Name <span style="color:red"> *</span></label>
                <select class=" form-control form-select  " id="form_name"  name='form_name'>
                    <option value=''>-- Select Form Name --</option>
                    
                    <?php 
                    foreach ($forms as $val)
                    
                    {
                        $selected = '';
                        if(old('form_name',$form_id) == $val->id){
                            $selected = 'selected';
                        }
                        ?>
                        <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>><?php echo $val->form_name; ?></option>
                        <?php 
                    }
                    ?>

                </select>
                @error('form_name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <h5><small class="text-danger" id="form_name_err"></small></h5>
            </div></div>
			
			<div class="col-md-4 col-12">
		      <div class=" form-group  form-select" id="device">
                <label class="form-label" for="validationTooltip01">Program <span style="color:red"> *</span></label>
                <select class=" form-control form-select  " id="program_name"  name='program_name'>
                    <option value=''>-- Select Program --</option>
                    
                    <?php 
                    foreach ($program as $val)
                    
                    {
                        $selected = '';
                        if(old('program_name',$program_id) == $val->id){
                            $selected = 'selected';
                        }
                        ?>
                        <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>><?php echo $val->program_name; ?></option>
                        <?php 
                    }
                    ?>

                </select>
                @error('program_name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <h5><small class="text-danger" id="program_name_err"></small></h5>
            </div></div>
            

            <div class="col-md-1 col-12 text-center mt-2">
                <input type="submit" class="btn btn-primary mr-1" onClick="return validation()" value="Search">
                {{-- <a class="btn btn-outline-secondary" href="javascript:void(0);" onClick='$("#program_form_details_modal").modal("toggle")'>Cancel</a> --}}
            </div>
        
        </div>    
    </form><br><hr>
	{{-- <?php if(!empty($que)){dd($que);?> --}}
    <form  action="" method="post" id="contact-form">
        @csrf
        <input type="hidden" name="form_type" value="contact-form">
        <div class="table">
            <table class="table no-border" style="text-align: left">
                <h4>BRAND PROGRAM INFORMATION</h4>
                <thead>
                <tr>
                <th scope="col">Id</th>  
                <th scope="col">Question Name</th>
                <th scope="col">Sort Order</th>
                </tr>
                </thead>
                <tbody>
                    
                @php  $id = 0; @endphp
                <?php 
                if(!empty($que)){ 
                    ?>
                @foreach ($que as $aQuestion)
            
                <tr draggable="true" ondragstart="start()"  ondragover="dragover()"> 
                    <th scope="row">{{ ++$id }}</th>                     
                    <td><input type="text" name="question_text[]" value="{{ $aQuestion->question_text }} "> <input type="hidden" name="detail[]" value="{{ $aQuestion->question_id }}_<?php if( !empty($program_form_id))echo $program_form_id; else echo 0;?>_<?php if( !empty($program_id))echo $program_id; else echo 0;?>"></td>
                    <td>{{ $aQuestion->sort_order }} <input type="hidden" name="sort[]" value="{{ $aQuestion->question_id  }}"></td>
                
                </tr>
                <input type="hidden" name="sort_order[]" value="{{ $id }}">
                {{-- <input type="hidden" name="que_id[]" value="{{ $aQuestion->question_id }}"> --}}
                        <input type="hidden" name="program_form_id[]" value="{{ $program_form_id }}">
                        <input type="hidden" name="program_id[]" value="{{ $program_id }}">
                @endforeach  
                <?php  
                }else{
                ?>
                <tr><td colspan="6" class="text-center">DATA NOT FOUND</td></tr>
                <?php 
                } ?>   
                </tbody>         
            </table>
            {{-- <?php }?> --}}
    </div>  
    <button type="submit" class="btn btn-primary" name="table_submit" value="Submit" id="submit_form">Submit</button>
    </form>
							</div>
                            </div>
                        </div>
                    </div>
                   
             
            </section>
        </div>
        <div></div>

        <div class="modal fade" id="change_que_modal" tabindex="-1" role="dialog" aria-labelledby="change_que_modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
              <div class="modal-content">
                <div class="col-xl-12">
                  <div class="card social-profile mb-0">
               
                    <div class="card-body" >
                        <form class="form" id="model" action="" method="post">
                            @csrf
                            <div id="change_que_modal_body">
                                
                            </div>
                        </form>
                    </div>         
                    <br>
                  </div>
                </div>  
              </div>
            </div>
        </div>
        <?php $live_url = config('custom.base_url'); ?>
    </section>
    
   
@endsection

<script>
    var row;

function start(){  
  row = event.target; 
}
function dragover(){
  var e = event;
  e.preventDefault(); 
  
  let children= Array.from(e.target.parentNode.parentNode.children);
  
  if(children.indexOf(e.target.parentNode)>children.indexOf(row))
    e.target.parentNode.after(row);
  else
    e.target.parentNode.before(row);
}
</script>
<script>
	 function validation() {    
        if ($('#form_name').val() == "") {
            $('#form_name').parent().addClass('has-error');
            $('#form_name_err').html('Please Choose Any Option.');
            $('#form_name').focus();
            $('#form_name').change(function() {
                $('#form_name').parent().removeClass('has-error');
                $('#form_name_err').html('');
            });
            return false;
        }

		if ($('#program_name').val() == "") {
            $('#program_name').parent().addClass('has-error');
            $('#program_name_err').html('Please Choose Any Option.');
            $('#program_name').focus();
            $('#program_name').change(function() {
                $('#program_name').parent().removeClass('has-error');
                $('#program_name_err').html('');
            });
            return false;
        }

    } 
    
  

    function text_validate(id)
    {
        // if($('#que_text').val)

        if ($('#que_text').val() == "") {
            $('#que_text').parent().addClass('has-error');
            $('#que_text_err').html('Please Enter question text.');
            $('#que_text').focus();
            $('#que_text').change(function() {
                $('#que_text').parent().removeClass('has-error');
                $('#que_text_err').html('');
            });
            return false;
        }
        
    }
</script>