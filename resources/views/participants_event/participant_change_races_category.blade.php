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
<div class="change_category_modal">
   
    <div class="row">
        <h3>Participant Change Races Category</h3><br><br>
       <div class="table">
            
            <form class="form" action="{{ url('participants_event/'.$event_id.'/edit_cat/'.$attendance_id) }}" method="post" enctype="multipart/form-data">
                 @csrf
                <input type="hidden" name="form_type" value="edit_category">
                <input type="hidden" id="event_id" name="event_id" value="{{  $event_id  }}" autocomplete="off" />
                <input type="hidden" id="attendance_id" name="attendance_id" value="{{  $attendance_id  }}" autocomplete="off" />
                
                <div class="row">
                    <div class="col-md-3 col-12">
                        <label>Races Category</label>
                    </div>

                    <div class="col-md-9 col-12">
                        <div class="form-group">
                            <select id="sel_ticket_id" name="sel_ticket_id" class="select2 form-control">
                                <option value="">All category</option>
                                <?php  
                                foreach ($races_category as $value)
                                {  
                                    // $selected = '';
                                    // if(old('sel_ticket_id', $ActualValue) == $value->id){
                                    //     $selected = 'selected';
                                    // }
                                    ?>
                                    <option value="<?php echo $value->id; ?>"><?php echo $value->ticket_name.' (₹ '.number_format($value->ticket_price,2).')'; ?></option>
                                    <?php 
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-1">
                    <input type="submit" class="btn btn-primary mr-1" onClick="return confirm('Are you sure you want to change this category?'); " value="Submit">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal" onclick="popupclose()">Close</button>  
                </div>
            </form>

		</div>   
       </div>
    </div>
</div> 

<script>
    function popupclose(){
            $('#change_category_modal').modal("hide");
        }
</script>

