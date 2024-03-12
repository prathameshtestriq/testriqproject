<div class="social-profile mb-0">
    <div class="">
      <div class="social-img-wrap">
        <h1 class="text-center">Program Tab Form</h1> 
        <hr>     
        <div class="social-img">
          <h1>Add Program Form </h1>
        </div>
       
      </div>
    </div>
</div>

<div class="Customer-details">
    <form class="form"
    id="categoryform1" action="{{ url('/program_tabs/program_form_ajx/'.$program_tab->id) }}" method="POST">
    <input type="hidden" name="form_type" value="assign_program_tab_form">
    <input type="hidden" id="id" name="program_tab_id" value="{{  $program_tab->id  }}" autocomplete="off" />
    {{-- <input type="hidden" id="id2" name="brand_id" value="{{ $program_tab->brand_id }}" autocomplete="off" /> --}}
    <input type="hidden" id="id3" name="program_id" value="{{ $program_tab->program_id }}" autocomplete="off" />
    <input type="hidden" id="id4" name="tab_id" value="{{ $program_tab->tab_id }}" autocomplete="off" />
    
        {{ csrf_field() }}
        
        <div class="row">
            <div class="col-md-4 form-group select2 form-select" id="device">
                <label class="form-label" for="validationTooltip01"> Form Name <span style="color:red"> *</span></label>
                <select class=" form-control form-select  " id="form_name"  name='form_name'>
                    <option value=''>-- Select Form Name --</option>
                    
                    <?php 
                    foreach ($master_form as $val)
                    
                    {
                        $selected = '';
                        if('form_name' == $val->id){
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
            </div>

            <div class="col-md-4 ">   
                <div class="form-group">
                    <label for="form_title">Form Title<span style="color:red"> *</span> </label>
                    <input type="text" id="form_title" class="form-control"
                        placeholder="Enter Form Title" name="form_title"
                        value="" autocomplete="off" />
                    @error('form_title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <h5><small class="text-danger" id="form_title_err"></small></h5>
                </div>
            </div>
            <div class="col-md-4 ">   
                <div class="form-group">
                    <label for="order_sort">Order Sort<span style="color:red"> *</span> </label>
                    <input type="number" id="order_sort" class="form-control"
                        placeholder="Enter Order Sort" name="order_sort"
                        value="" autocomplete="off" />
                    @error('order_sort')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <h5><small class="text-danger" id="order_sort_err"></small></h5>
                </div>
            </div> 

            <div class="col-12 text-center mt-1">
                <input type="submit" class="btn btn-primary mr-1" onClick="return validation()" value="Submit">
                <a class="btn btn-outline-secondary" href="javascript:void(0);" onClick='$("#program_form_details_modal").modal("toggle")'>Cancel</a>
            </div>
        
        </div>    
    </form><br><hr>
    <div class="table">
            <table class="table no-border" style="text-align: left">
                <h4>BRAND PROGRAM IMFORMTION</h4>
                <thead>
                <tr>
                <th scope="col">Id</th>  
                <th scope="col">Form Name</th>
                <th scope="col">Sort Order</th>
                <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                    
                @php  $id = 0; @endphp
                <?php 
                if(!empty($program_forms)){ 
                    ?>
                @foreach ($program_forms as $adetails)
            
                <tr> 
                    <th scope="row">{{ ++$id }}</th>                     
                    <td>{{ $adetails->form_name }}({{ $adetails->form_title }})</td>
                    <td>{{ $adetails->sort_order }}</td>
                    <td>
                    <i class="fa fa-trash-o btn btn-danger btn-sm"
                        onclick="delprogramform({{ $adetails->id }})"></i>
                    </td>
                
                </tr>
                @endforeach  
                <?php  
                }else{
                ?>
                <tr><td colspan="6" class="text-center">DATA NOT FOUND</td></tr>
                <?php 
                } ?>   
                </tbody>         
            </table>
    </div>  
    <div class="col-12 text-center mt-1">
    <a class="btn btn-outline-secondary" href="javascript:void(0);" onClick='$("#program_form_details_modal").modal("toggle")'>Cancel</a>
    </div> 

</div>


<script>

  function delprogramform(id) {
        // alert(id);
        var url = '<?php echo url('/program_tabs/program_form/remove'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove this Program Form');
        if (bConfirm) {
            window.location.href = url;
        } else {
            return false;
        }
    }


    function validation() {    
        if ($('#form_name').val() == "") {
            $('#form_name').parent().addClass('has-error');
            $('#form_name_err').html('Please Select Form Name.');
            $('#form_name').focus();
            $('#form_name').change(function() {
                $('#form_name').parent().removeClass('has-error');
                $('#form_name_err').html('');
            });
            return false;
        }

        if ($('#form_title').val() == "") {
            $('#form_title').parent().addClass('has-error');
            $('#form_title_err').html('Please Select Form Title.');
            $('#form_title').focus();
            $('#form_title').change(function() {
                $('#form_title').parent().removeClass('has-error');
                $('#form_title_err').html('');
            });
            return false;
        }

        if ($('#order_sort').val() == "") {
            $('#order_sort').parent().addClass('has-error');
            $('#order_sort_err').html('Please Select Sort Order.');
            $('#order_sort').focus();
            $('#order_sort').change(function() {
                $('#order_sort').parent().removeClass('has-error');
                $('#order_sort_err').html('');
            });
            return false;
        }
    } 
    
    $(document).ready(function () {
        // Get references to the Form Name dropdown and Form Title input
        var formNameDropdown = $('#form_name');
        var formTitleInput = $('#form_title');

        // Add an event listener to the Form Name dropdown
        formNameDropdown.on('change', function () {
            // Update the Form Title input with the selected Form Name
            formTitleInput.val(formNameDropdown.find(':selected').text());
        });
    });
</script>


