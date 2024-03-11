
<div class="Customer-details">
    <div class="row">
       <div class="table">
        <table class="table no-border" style="text-align: left">
            <h4>BRAND PROGRAM INFORMATION</h4>
            <thead>
              <tr>
              <th scope="col">Id</th>  
              <th scope="col">Program Name</th>
              <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
              @php  $id = 0; @endphp
              <?php 
               if(!empty($Details)){ 
                ?>
              @foreach ($Details as $adetails)
          
              <tr> 
                <th scope="row">{{ ++$id }}</th>                     
                <td>{{ $adetails->program_name }}</td>
                <td>
                  <i class="fa fa-trash-o btn btn-danger btn-sm"
                    onclick="delbrandprogram({{ $adetails->program_id }})"></i>
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
      <a class="btn btn-outline-secondary" href="javascript:void(0);" onClick='$("#program_details_modal").modal("toggle")'>Cancel</a>
    </div> 
    </div>
</div>
<script>
  function delbrandprogram(id) {
        // alert(id);
        var url = '<?php echo url('/master_brands/program/remove'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove this Brand-Program');
        if (bConfirm) {
            window.location.href = url;
        } else {
            return false;
        }
    }
</script>
