@extends('layout.index')
@section('title', 'Users ')

<!-- Dashboard Ecommerce start -->
@section('content')
    <section>
        <div class="content-body">
            <!-- Bordered table start -->
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header w-100">
                            <div class="content-header-left">
                                <div class="row breadcrumbs-top">
                                    <div class="col-sm-12">
                                        <h2 class="content-header-title float-left mb-0">Users</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</a></li>
                                        <li class="breadcrumb-item">Users</li>
                                        <li class="breadcrumb-item active" aria-current="page">Users List</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bordered table end -->
        </div>
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
                        <form class="dt_adv_search" action="{{ url('users') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_user">
                            <div class="card-header w-100 m-0"> 
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-3 col-12">
                                                <label for="form-control">User Name</label>
                                                <input type="text" id="user_name" class="form-control"
                                                        placeholder="User Name" name="name"
                                                        value="{{ $search_name }}" autocomplete="off" />
                                            </div>

                                            <div class="col-sm-3 col-12">
                                                <label for="form-control">Email Id</label>
                                                <input type="text" id="email_id" class="form-control"
                                                    placeholder="Email Id" name="email_id" value="{{ $search_email_id }}"
                                                    autocomplete="off" />
                                            </div>
                                            
                                            <div class="col-sm-3 col-12">
                                                <label for="form-control">Mobile No</label>
                                                <input type="text" id="mobile_no" class="form-control"
                                                    placeholder="Mobile No" name="mobile_no" value="{{ $search_mobile }}"
                                                    autocomplete="off" />
                                            </div>

                                            <div class="col-sm-3 col-12">
                                                <label for="form-control">Country</label>
                                                <select id="country" name="country" class="select2 form-control">
                                                    <option value="">All country</option>
                                                    <?php  
                                                    foreach ($countries as $value)
                                                    {  
                                                        $selected = '';
                                                        if(old('country', $search_country) == $value->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->name; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3 col-12 mt-1">
                                                <label for="form-control">State</label>
                                                <select id="state" name="state" class="select2 form-control">
                                                    <option value="">All state</option>
                                                </select>  
                                            
                                            </div>
    
                                            <div class="col-sm-3 col-12 mt-1">
                                                <label for="form-control">City</label>
                                                <select id="city" name="city" class="select2 form-control">
                                                    <option value="">All City</option>
                                                </select>  
                                               
                                            </div>
                                            <div class="col-sm-3 col-12 mt-1">
                                                <?php 
                                                   $Gender = array(1=>'Male',2=>'Female',3=>'Other' );    
                                                ?>
                                                <label for="form-control">Gender</label>
                                                <select id="gender" name="gender" class="form-control select2 form-control">
                                                    <option value="">Select  Gender</option>
                                                    <?php 
                                                        foreach ($Gender as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if(old('gender',$search_gender) == $key){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3 col-12 mt-1">
                                                <?php 
                                                   $Status = array(0=>'Inactive',1=>'Active' );    
                                                ?>
                                                <label for="form-control"> Status</label>
                                                <select id="status" name="status" class="form-control select2 form-control">
                                                    <option value="">Select  Status</option>
                                                    <?php 
                                                        foreach ($Status as $key => $value)
                                                        {
                                                            $selected = '';
                                                            if(old('status',$search_status) == $key){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3 col-12 mt-1">
                                                <label for="form-control">Role</label>
                                                <select id="role" name="role" class="select2 form-control">
                                                    <option value="">All Role</option>
                                                    <?php  
                                                    foreach ($role_details as $value)
                                                    {  
                                                        $selected = '';
                                                        if(old('role', $search_role) == $value->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->name; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <div class="col-sm-3 mt-1">
                                                <?php 
                                                   $Rows = ['10','25','50','100'];    
                                                ?>
                                                <label for="form-control">Rows :</label>
                                                <select id="rows" name="rows" class="form-control select2 form-control">
                                                    <option value="">Select Rows</option>
                                                    <?php 
                                                        foreach ($Rows as  $value)
                                                        {
                                                            $selected = '';
                                                            if(old('rows',$search_rows) == $value){
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                            <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                         
                                            <div class="col-sm-3 mt-1">
                                                <label for="form-control">&nbsp;</label><br>
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                @if (!empty($search_name) || !empty($search_email_id) || !empty($search_mobile) || !empty($search_state) ||!empty($search_city) || !empty($search_gender) || ($search_status != '') || (!empty($search_rows)) || (!empty($search_country)) || (!empty($search_role)) )
                                                    <a title="Clear" href="{{ url('user/clear_search') }}"
                                                        type="button" class="btn btn-outline-primary">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif 
                                            </div>
                                           
                                        
                                            <div class="col-sm-3 float-right mt-1">
                                                <label for="form-control">&nbsp;</label><br>
                                                @if (!empty($user_array))
                                                    <a href="{{ url('/user/export_download') }}" class="btn btn-danger text-white float-right ml-1">Download </a>
                                                @endif 
                                                <a href="{{ url('/user/add_edit') }}" class="btn btn-outline-primary float-right ">
                                                    <i data-feather="plus"></i><span>Add User</span></a> &nbsp;

                                            </div>
                                        </div>
                                    </div>
                                </div>
                              
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mt-1">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-left">User Name</th>                                                <div class="col-xs-12 col-md-12">
                                            <div class="form-group mb-5">
                                                {{-- <label class="col-sm-4 float-left" style="margin-top:20px"  for="mobile" >Contact Number <span style="color:red;">*</span></label> --}}
                                                {{-- <input type="text" id="mobile" class="form-control col-sm-8 float-right" name="mobile"
                                                    placeholder="mobile" autocomplete="off" value="{{ old('mobile',$mobile) }}" /> --}}
                                                    <h5><small class="text-danger" id="mobile_err"></small></h5>
                                                    @error('mobile')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                      
                                        <th class="text-left">Email ID/Contact Number</th>
                                        <th class="text-left">Gender</th>
                                        <th class="text-left">Date of Birth</th>
                                        <th class="text-left">Country</th>
                                        <th class="text-left">State</th>
                                        <th class="text-left">City</th>
                                        <th class="text-center">Role Name</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Profile Completion <br> Percentage</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                    
                                    <?php 
                                    if (!empty($user_array)){
                                        $i = $Offset;
                                        // $i = 0;
                                        ?>
                                        <?php 
                                            foreach ($user_array as $val){
                                                $i++;
                                                $url =  config('custom.url_link');
                                        ?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-left">{{ $val->firstname }} {{ $val->lastname }}</td>
                                                {{-- <td class="text-left">{{ $val->username }}</td> --}}
                                                <td class="text-left">{{ $val->email }}<br>{{ $val->mobile }}</td>
                                                <td class="text-left">
                                                    @if ($val->gender == 1)
                                                        Male
                                                    @elseif ($val->gender == 2)
                                                        Female
                                                    @else
                                                        Other
                                                    @endif
                                                </td>
                                                <td class="text-left">{{ date('d-m-Y',strtotime($val->dob)) }}</td>
                                                <td class="text-left">{{ !empty($val->country_name) ? $val->country_name : '-' }}</td>
                                                <td class="text-left">{{ !empty($val->state_name) ? $val->state_name : '-'}}</td>
                                                <td class="text-left">{{ !empty($val->city_name) ? $val->city_name : '-' }}</td>
                                                <td class="text-left">{{ !empty($val->role_name) ? $val->role_name : '-' }}</td>
                                                <td class="text-center">
                                                    <div class="custom-control custom-switch custom-switch-success">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="{{ $val->id }}" {{ $val->is_active ? 'checked' : '' }}
                                                            onclick="change_status(event.target, {{ $val->id }});" />
                                                        <label class="custom-control-label" for="{{ $val->id }}">
                                                            <span class="switch-icon-left"></span>
                                                            <span class="switch-icon-right"></span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                <?php echo !empty($val->profile_completion_percentage) ? number_format($val->profile_completion_percentage,2).'%' : ''  ?>
                                                </td> 
                                               

                                                <td width="10%">
                                                     <a href="<?php echo  $url.''.$val->email; ?>/<?php echo $val->password; ?>" target="_blank">
                                                        <i class="fa fa-eye btn btn-primary btn-sm" title="Login As Organiser"></i>
                                                    </a>
                                                    
                                                    <a href="{{ url('/user/add_edit', $val->id) }}"><i
                                                            class="fa fa-edit btn btn-primary btn-sm" title="Edit"></i></a>
                                                    <i class="fa fa-trash-o btn btn-danger btn-sm"
                                                        onclick="delUser({{ $val->id }})" title="Delete"></i>
                                                </td>
                                            </tr>
                                      <?php }
                                    }else{?>
                                        <tr>
                                            <td colspan="16" style="text-align:center; color:red;">No Record Found</td>
                                        </tr>
                                  <?php }?>
                                </tbody>
                            </table>
                            <div class="card-body">
                                <div class="d-flex justify-content-end">
                                    {{ $Paginator->links() }}
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#state').change(function() {
        var stateId = $(this).val();
        if (stateId) {
            $.ajax({
                url: '/get-cities/' + stateId,
                type: 'GET',
                success: function(data) {
                    var cityDropdown = $('#city');
                    cityDropdown.empty();
                    cityDropdown.append('<option value="">Select City</option>');
                    $.each(data.cities, function(index, city) {
                        cityDropdown.append('<option value="' + city.id + '">' + city.name + '</option>');
                    });
                }
            });
        } else {
            $('#city').empty().append('<option value="">Select City</option>');
        }
    });
});
</script>
<script>
  function delUser(id) {
        // alert(id);
        var url = '<?php echo url('user/delete'); ?>';
        url = url + '/' + id;
        //    alert(url);
        bConfirm = confirm('Are you sure you want to remove this record ?');
        if (bConfirm) {
            window.location.href = url;
        } else {
            return false;
        }
    }


    function change_status(_this, id) {
        //  alert(id)
;
        var status = $(_this).prop('checked') == true ? 1 : 0;
        // alert(status);
        
        if (confirm("Are you sure want to change this status?")) {
            let _token = $('meta[name="csrf-token"]').attr('content');
            //alert(_token);
            $.ajax({
                url: "<?php echo url('user/change_status') ?>",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function(result) {
                    if(result == 1){
                        console.log(result);
                        alert('Status changed successfully')
                        //location.reload(); 
                    }else{
                        alert('Some error occured');
                        if(status)
                            $(_this).prop("checked" , false)
                        else
                            $(_this).prop("checked" , true)
                            return false;
                    }
                },
                error:function(){
                    alert('Some error occured');
                    if(status)
                        $(_this).prop("checked" , false)
                    else
                        $(_this).prop("checked" , true)
                        return false;
                }
            });
        }else{
            if(status)
                $(_this).prop("checked" , false)
            else
                $(_this).prop("checked" , true)
            return false;
        }
    }



$(document).ready(function() {
  $('#country').change(function() {
      var countryId = $(this).val();
      if (countryId) {
          $.ajax({
              url: '/get_states/' + countryId,
              type: 'GET',
              success: function(data) {
                  console.log(data);
                  var stateDropdown = $('#state');
                  stateDropdown.empty();
                  stateDropdown.append('<option value="">All State</option>');
                  $.each(data.states, function(index, state) {
                      stateDropdown.append('<option value="' + state.id + '">' + state.name + '</option>');
                  });
              }
          });
      } else {
          $('#state').empty().append('<option value="">All state</option>');
          $('#city').empty().append('<option value="">All City</option>');
      }
  });
 
  
});
$(document).ready(function() {
  $('#state').change(function() {
      // alert("hereemldcklk");
      var stateId = $(this).val();
      if (stateId) {
          $.ajax({
              url: '/get_cities/' + stateId,
              type: 'GET',
              success: function(data) {
                  console.log(data);
                  var cityDropdown = $('#city');
                  cityDropdown.empty();
                  cityDropdown.append('<option value="">All City</option>');
                  $.each(data.cities, function(index, city) {
                      cityDropdown.append('<option value="' + city.id + '">' + city.name + '</option>');
                  });
              
              }
          });
      } else {
          $('#city').empty().append('<option value="">All City</option>');
      }
  }); 
   
});
    
    
  
</script>