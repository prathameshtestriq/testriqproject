<div class="social-profile mb-0">
    <div class="">
      <div class="social-img-wrap">
        <h1 class="text-center">Program-Brand</h1> 
        <hr>     
        <div class="social-img">
          <h1>Add Program-Brand </h1>
          <h5>Brand Id :-{{ $master_brand->id }} </h5>
          <h6> Brand Name:-{{ $master_brand->brand_name }}</h6>
        </div>
       
      </div>
    </div>
</div>

<div class="employee-assign-device-details">
   <form class="form"
   id="categoryform1" action="" method="POST">
      <input type="hidden" name="form_type" value="assign_program_brand_form">
      <input type="hidden" id="id" name="brand_id" value="{{ $master_brand->id }}" autocomplete="off" />
      {{ csrf_field() }}

        <div class="row">
            <div class="col-md-12 col-12">   
                <div class="form-group select2 form-select" id="device">
                    <label class="form-label" for="validationTooltip01"> Program Name <span style="color:red"> *</span></label>
                    <select class=" form-control form-select  " id="program_id"  name='program_id'>
                        <option value=''>-- Select Program --</option>
                        
                        <?php 
                        foreach ($master_program as $val)
                        
                        {
                            $selected = '';
                            if('program_id' == $val->id){
                                $selected = 'selected';
                            }
                            ?>
                            <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>><?php echo $val->program_name; ?></option>
                            <?php 
                        }
                        ?>

                    </select>
                    @error('program_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <h5><small class="text-danger" id="program_id_err"></small></h5>
                </div>

                <div class="col-12 text-center mt-1">
                    <input type="submit" class="btn btn-primary mr-1" onClick="return validation()" value="Submit">
                    <a class="btn btn-outline-secondary" href="javascript:void(0);" onClick='$("#add_brand_program_modal").modal("toggle")'>Cancel</a>
                </div>
            </div> 
         
        </div>    
    </form>
    
</div>

<script>

    // $("#program_id").select2({
    //     dropdownParent: $('#program_id ')
    // });
    function validation() {    
        if ($('#program_id').val() == "") {
            $('#program_id').parent().addClass('has-error');
            $('#program_id_err').html('Please Select Program Name.');
            $('#program_id').focus();
            $('#program_id').change(function() {
                $('#program_id').parent().removeClass('has-error');
                $('#program_id_err').html('');
            });
            return false;
        }
    } 
</script>