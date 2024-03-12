@extends('layout.index')
@section('title', 'Master Forms')
<!-- Dashboard Ecommerce start -->
@section('content')
    <section>

        <div class="content-body">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12 d-flex">
                            <h2 class="content-header-title float-start mb-0">Add Master Question</h2>
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
							<form class="form" id="model" 
								{{-- action="{{ url('master_questions/view/'.$id) }}" --}}
								 method="post">
									@csrf
									<input type="hidden" name="form_type" value="add_question">
										<div class="row">
											<div class="col-md-3 col-12">
												<div class="form-group">
													<label for="key">Key <span style="color:red;">*</span></label>
													<input type="text" id="Key_id" class="form-control"
														placeholder="key" name="key" autocomplete="off" value="" />
														<h5><small class="text-danger" id="key_err"></small></h5>         
												</div>
											</div>

											<div class="col-md-2 col-12">
												<div class="form-group">
												<label for="">active</label><br/>   
												<input type="checkbox" class="form-group-input" id="" name="active_check"
												value="checked">
												
												</div>
											</div>
                                            <div class="col-md-6 col-12">
												<div class="form-group">
										            <button type="button" id="validatebtn" class="btn btn-success mt-2 float-lg-right" onClick="return linkModel(<?php echo $iId;?>)" >Link Table</button>
												</div>
											</div>
										</div>
                                       
										<button type="submit" id="validatebtn" class="btn btn-primary mr-1" onClick="return validation()" >Submit</button>
										<a href="{{ url('master_questions/add_edit/'.$iId) }}" class="btn btn-danger">Cancel</a>
							</form>
						</div>
                    </div>
                </div>
            </div>
        	</section>
        </div>

        <div class="modal fade" id="add_language_model" tabindex="-1" role="dialog" aria-labelledby="add_language_model" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="col-xl-12">
                    <div class="card social-profile mb-0">
                        <div class="card-body">
                        <div id="add_language_model_body">


                        </div>
                </div>         
                
                <br>
            </div>
            </div>  
            </div>
            </div>
        </div>

        <div class="modal fade" id="add_subquestion_modal" tabindex="-1" role="dialog" aria-labelledby="add_subquestion_modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
              <div class="modal-content">
                <div class="col-xl-12">
                  <div class="card social-profile mb-0">
               
                    <div class="card-body" >
                   
                        <form class="form" id="model" action="{{ url('option/add_child_question') }}" method="post">
                       
                            @csrf
                            <div id="add_subquestion_modal_body">
                                
                            </div>
                        </form>
                    </div>         
                    <br>
                  </div>
                </div>  
              </div>
            </div>
        </div>

         <div class="modal fade" id="link_table_modal" tabindex="-1" role="dialog" aria-labelledby="link_table_modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
              <div class="modal-content">
                <div class="col-xl-12">
                  <div class="card social-profile mb-0">
               
                    <div class="card-body" >
                        <form class="form" id="model" action="{{ url('master_questions/link/'.$iId) }}" method="post">
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
      
		<div class="table-responsive mt-5">
			<table class="table table-striped table-bordered text-center">
				<thead>
					<tr>
						<th>Sr. No</th>
						<th >Name</th>
						<th >Active</th>
						<th >Language</th>
						<th >Action</th>
						<th >Status</th>
					</tr>
				</thead>
				<tbody>
			  <?php
					if (!empty($data)){
					   
					   $i =0;
						foreach ($data as $key => $val){
					   
							$name=!empty($key) ?  $key:'';
						   
								$i++;?>
							<tr>
								<td>{{ $i }}</td>
								<td >{{ $name }}</td>
								<td>
							  <?php  if(!empty($val->active)){
								   echo "YES";
								}else{
									echo 'No';
								}
							   ?> </td> <td >
							   
							   <?php if(!empty($val->options)){
								   
										foreach($val->options as $k =>$value)
										{ ?>
											<?php echo $k.':'.$value; ?>
										<button type="button" id="validatebtn" class="float-lg-right btn-danger" onclick=" delete_lang({{ $iId }},'{{ $name }}','{{ $value }}','{{ $k }}')"> <i class="fa fa-remove" ></i> </button><br><br>
   
									  <?php  }    }         ?>           
							</td>
							<td> 
                                <button type="button" id="validatebtn" class="btn btn-warning mr-1" onclick="return getVal({{ $iId }},'{{ $name }}')"><i class="fa fa-solid fa-plus"> Language </i></button>
                                <button type="button" id="validatebtn" class="btn btn-primary mr-1" onclick="return add_subquestion({{ $iId }},'{{ $name }}')"><i class="fa fa-solid fa-plus"> Child Question </i></button>
                            </td>
								<td>
									<div class="custom-control custom-switch custom-switch-success">
								   
										<input type="checkbox" class="custom-control-input" id="{{ $key }}"
									   <?php if($val->active){ echo' checked';  }else{ echo' ';}   ?>
									   onclick="change_active_status({{$iId}},{{ $val->active }},'{{ $name}}')"/>
   
										<label class="custom-control-label" for="{{ $key }}">
											<span class="switch-icon-left"></span>
											<span class="switch-icon-right"></span>
										</label>
									</div>
								</td>
					   
						   </tr><?php
					   }
				   }else{
				   ?>   <tr>
						   <td colspan="8" style="text-align:center; color:red;">No Record Found</td>
					   </tr><?php
			   }
				   ?> </tbody>
		   </table>
		   </div>
        
       
    </section>
   
	@endsection		

	<script>
       
function validation()
 {
    if ($('#Key_id').val() == ""){
        $('#Key_id').parent().addClass('has-error');
        $('#key_err').html('Please Enter key Name.');
        $('#Key_id').focus();
        $('#Key_id').keyup(function () {
        $('#Key_id').parent().removeClass('has-error');
        $('#key_err').html('');
        });
        return false;
    }
    
 }

 function change_active_status(id,status,name)
{        console.log(id,status,name)
        var url = '';
        if (confirm("Are you sure want to change this status?")) {
        var active =status;
        let _token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({

            url: "<?php echo url('option/status_change'); ?>",
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                name:name,
                active:active
            },
            success: function(result) {
                if (result==1) {
                    alert('Status updated Successfully');
                    location.reload();
                }else {
                    alert('Some error occured');
                }
            },
            error: function(jqXHR, testStatus, error) {
                console.log(error);
                alert("Page " + url + " cannot open. Error:" + error);
                $('#loader').hide();
            },
        });
        }else{
            return false;
        }        
            
}


    function linkModel(id)
    {
        // alert(id);
        var url='';
        $.ajax({
             url: "<?php echo url('master_questions/link') ?>/"+id,
            beforeSend: function() {
                $('#loader').show();
            },
            // return the result
            success: function(result) {
                // console.log(result);
                if(result){
                    $('#link_table_modal_body').html(result);
                $('#link_table_modal').modal("show");
                }
             
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
        });
        // $('#master_question_modal').modal("show");
    }

function delete_lang(id,name,value,lang)
{
    var url2 = '<?php echo url('master_questions/view'); ?>';
             url2 = url2 + '/' + id;
             var url='';
    if(lang != '' && name !=''){
     $.ajax({
    url: "<?php echo url('master_questions/delete_language'); ?>/"+id,
    type: 'post',
    data: {
        _token: "{{ csrf_token() }}",
        language:lang,
        key:value,
        name:name
    },
    success: function(result) {

        if(result==1)
        {
            bConfirm = confirm('Are you sure you want to remove this language');
            if (bConfirm) {
                window.location.href = url2;
            } else {
                return false;
            }
            
        }
    },
    error: function(jqXHR, testStatus, error) {
        // console.log(error);
        alert("Page " + url + " cannot open. Error:" + error);
        $('#loader').hide();
    },
    });
    return false;
}
}

function getVal (id,val) 
{
    var url;
    if(id!=0)
    { 
        $.ajax({

            url: "<?php echo url('master_questions/add_language'); ?>/"+id,
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                Name: val
            },
            success: function(result) {

                $('#add_language_model_body').html(result);
                    $('#add_language_model').modal("show");

            },
            error: function(jqXHR, testStatus, error) {
                // console.log(error);
                alert("Page " + url + " cannot open. Error:" + error);
                $('#loader').hide();
            },
        }); 
    }
}

function success(id)
{
	
var val= $('#text').val();
// alert(val);
var lang= $('#language').val();
var url = '<?php echo url('master_questions/view/'); ?>';
    url = url + '/' + id;
   
if( val != '' )
{
    window.location.href = url;
}
if( lang == '' || val == '')
{
    if ($('#language').val() == ""){
        $('#language').parent().addClass('has-error');
        $('#language_err').html('Please select language.');
        $('#language').focus();
        $('#language').keyup(function () {
        $('#language').parent().removeClass('has-error');
        $('#language_err').html('');
        });
        return false;
    }
    if ($('#text').val() == ""){
        $('#text').parent().addClass('has-error');
        $('#text_err').html('Please enter text.');
        $('#text').focus();
        $('#text').keyup(function () {
        $('#text').parent().removeClass('has-error');
        $('#text_err').html('');
        });
        return false;
    }
}
}

function addlang(id,val)
{
    var url;
    var value=$('#language').val();  
    var textval= $('#text').val();
    if(textval != ''){
        $.ajax({
        url: "<?php echo url('master_questions/add_language'); ?>/"+id,
        type: 'post',
        data: {
            _token: "{{ csrf_token() }}",
            Text:textval,
            Name:val,
            Value:value,
        },
        success: function(result) {
            // console.log(result)
        },
        error: function(jqXHR, testStatus, error) {
            // console.log(error);
            alert("Page " + url + " cannot open. Error:" + error);
            $('#loader').hide();
        },
        });
        // return false;
    }
}

function add_subquestion(id,name)
 {
    var url='';
    if(id!=0)
    { 
        $.ajax({

            url: "<?php echo url('option/add_child_question'); ?>",
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                id:id,
                name:name,
            },
            success: function(result) {

                $('#add_subquestion_modal_body').html(result);
                    $('#add_subquestion_modal').modal("show");
                   
                    $('#allquestion').select2({
                     });
            },
            error: function(jqXHR, testStatus, error) {
                // console.log(error);
                alert("Page " + url + " cannot open. Error:" + error);
                $('#loader').hide();
            },
        }); 
    }
 }
 function closebtn()
{
    // alert('here');
    $("#link_table_modal").modal("toggle");
    location.reload();
}
function close_add_subquestion()
{
    // alert('here');
    $("#add_subquestion_modal").modal("toggle");
    location.reload();
}

function question_validate()
{
    // if ($('#allquestion').val() == ""){
    //     $('#allquestion').parent().addClass('has-error');
    //     $('#allquestion_err').html('Please Select Any Option.');
    //     $('#allquestion').focus();
    //     $('#allquestion').keyup(function () {
    //     $('#allquestion').parent().removeClass('has-error');
    //     $('#allquestion_err').html('');
    //     });
    //     return false;
    // } 
    // return true;
   
}
function table_validation()
{
 
    if ($('#table').val() == ""){
        $('#table').parent().addClass('has-error');
        $('#table_err').html('Please enter table name.');
        $('#table').focus();
        $('#table').keyup(function () {
        $('#table').parent().removeClass('has-error');
        $('#table_err').html('');
        });
        return false;
    } 
    return true;
}
	</script>