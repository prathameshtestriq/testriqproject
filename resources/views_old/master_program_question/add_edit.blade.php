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
								 id="categoryform" action="" method="POST" enctype="multipart/form-data">
									<input type="hidden" name="form_type" value="add_edit_program_question">
                                    {{ csrf_field() }}
                                  
                                    <div class="row">
                                        <div class="col-md-4 col-12">
											<div class="form-group">
                                                <label for="form" >Master form<span style="color:red;"> *</span></label>
                                               
                                                <select name="form" id="form" class="form-control form-select" onchange="return get_form_id()">
                                                    <option value="">Select Form</option>
                                                    <?php 
                                                 
                                                    foreach ($forms as $value)
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
                                                <span class="error" style="color:red;">{{ $message }}</span>
                                            @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-12 mt-2 ">
                                            
                                             <a href="{{ url('/change_text') }}" class="btn btn-primary float-md-right">update question text</a>
                                        </div>
                                       
                                    </div>   
                                   <div class="row">
									<div class="col-sm-12" id="tablecontent">
										
											
										
									</div>
								   </div>
								   

                                    <div class="col-12 text-center mt-1">
                                        <input type="submit" class="btn btn-primary mr-1" onclick="return validate()" value="Submit">
                                        
                                    </div>
                               
                                </form>
							</div>
                            </div>
                        </div>
                    </div>
                   
                    
            </section>
        </div>
        <?php $live_url = config('custom.base_url'); ?>
    </section>
    
   
@endsection
<script>
	function get_form_id()
	{
		var form_id=$('#form').val();
		var url='';
		if(form_id!='')
		{ 
			$.ajax({
			url: "<?php echo url('get_question_program'); ?>",
			type: 'post',
			data: {
				_token: "{{ csrf_token() }}",
				form_id:form_id,
				
			},
			success: function(result) {
				// console.log(result)
                $('#tablecontent').html(result);
			},
			error: function(jqXHR, testStatus, error) {
				// console.log(error);
				alert("Page " + url + " cannot open. Error:" + error);
				$('#loader').hide();
			},
			});
		}
	}

    function validate()
    {
        if ($('#form').val() == ""){
            $('#form').parent().addClass('has-error');
            $('#form_err').html('Please Select form.');
            $('#form').focus();
            $('#form').click(function () {
            $('#form').parent().removeClass('has-error');
            $('#form_err').html('');
            });
            return false;
        }
    }

</script>


