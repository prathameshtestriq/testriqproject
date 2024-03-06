@extends('layout.index')
@section('title', 'Program Tab List')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-8 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Master Farmer List</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-4 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Master</a>
                        </li>
                        <li class="breadcrumb-item active">Master Farmer List
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
                    <form class="dt_adv_search" action="" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="form_type" value="search_master_farmer">
                        <div class="card-header w-100 m-0">
                            <div class="row w-150">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-2 col-12">   
                                            <div class="form-group">
                                                <label class="form-label" for="validationTooltip01"> Season </label>
                                                <select class=" form-control form-select select2 " id="season"  name='season'>
                                                    <?php 
                                                   
                                                    foreach ($master_seasons as $val)
                                                    
                                                    {
                                                        $selected = '';
                                                        if(old('season',$season) == $val->season){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $val->season; ?>" <?php echo $selected; ?>><?php echo $val->season; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                            
                                                </select>                

                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2 col-12">   
                                            <div class="form-group">
                                                <label class="form-label" for="validationTooltip01"> Brand Name </label>
                                                <select class=" form-control form-select  " id="brand_id"  name='brand_name'>
                                                    <option value=''>-- Select Brand --</option>
                                                    
                                                    <?php 
                                                    foreach ($master_brand as $val)
                                                    
                                                    {
                                                        $selected = '';
                                                        if(old('brand_name',$brand_name) == $val->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>><?php echo $val->brand_name; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                            
                                                </select>
                                                
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2 col-12">   
                                            <div class="form-group">
                                                <label class="form-label" for="validationTooltip01"> Program Name </label>
                                                <select class=" form-control form-select " id="program_id"  name='program_name'>
                                                    <option value=''>-- Select Program --</option>
                                                    
                                                    <?php 
                                                    foreach ($master_program as $val)
                                                    
                                                    {
                                                        $selected = '';
                                                        if(old('program_name',$program_name) == $val->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>><?php echo $val->program_name; ?></option>
                                                        <?php 
                                                    }
                                                    ?>

                                                </select>
                                                
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-12">   
                                            <div class="form-group">
                                                <label class="form-label" for="validationTooltip01"> State Name </label>
                                                <select class=" form-control form-select " id="state_id"  name='state_name' onclick="get_district()">
                                                    <option value='0'>All State</option>

                                                    <?php                                                    
                                                    foreach ($master_states as $val)  
                                                                                                    
                                                    {
                                                        $selected = '';
                                                        if(old('state_name',$state_name) == $val['id']){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $val['id']; ?>" <?php echo $selected; ?>><?php echo $val['state_name']; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                            
                                                </select>
                                                
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-12">   
                                            <div class="form-group">
                                                <label class="form-label" for="validationTooltip01"> District Name </label>
                                                <select class=" form-control form-select " id="district_id"  name='district_name' onclick="get_block();">
                                                    <option value='0'>All District</option>
                        
                                                    <?php 
                                                    foreach ($master_districts as $val)
                                                    
                                                    {
                                                        $selected = '';
                                                        if(old('district_name',$district_name) == $val['id']){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $val['id']; ?>" <?php echo $selected; ?>><?php echo $val['district_name']; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                            
                                                </select>
                                                
                                            </div>
                                        </div>


                                        <div class="col-md-2 col-12">   
                                            <div class="form-group">
                                                <label class="form-label" for="validationTooltip01">Block Name </label>
                                                <select class=" form-control form-select " id="block_id"  name='block_name' onclick="get_village();">
                                                    <option value=''>All Block</option>
                                                    
                                                    <?php 
                                                    foreach ($master_blocks as $val)
                                                    
                                                    {
                                                        $selected = '';
                                                        if(old('block_name',$block_name) == $val['id']){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $val['id']; ?>" <?php echo $selected; ?>><?php echo $val['block_name']; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                            
                                                </select>
                                                
                                            </div>
                                        </div>


                                        <div class="col-md-2 col-12">   
                                            <div class="form-group">
                                                <label class="form-label" for="validationTooltip01"> Village Name </label>
                                                <select class=" form-control form-select " id="village_id"  name='village_name' >
                                                    <option value='0'>All village</option>
                                                    
                                                    <?php 
                                                    foreach ($master_villages as $val)
                                                    
                                                    {
                                                        $selected = '';
                                                        if(old('village_name',$village_name) == $val['id']){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $val['id']; ?>" <?php echo $selected; ?>><?php echo $val['village_name']; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                            
                                                </select>
                                                
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12 " >   
                                            <div class="form-group m-2">
                                                <button type="button" onclick="get_farmers()" class="btn btn-primary">Search</button>
                                                <div id="clear_btn" class="float-xl-right" style="margin-right: 210px ">

                                                </div>

                                                {{-- @if ($brand_name || $program_name || $state_name || $district_name || $block_name ||$village_name)
                                                    <a title="Clear" href="{{ url('master_farmers/clear_search') }}" type="button"
                                                        class="btn btn-outline-primary ">
                                                        <i data-feather="rotate-ccw" class="me-25"></i> Clear Search
                                                    </a>
                                                @endif --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table_content" id="farmers_table">
                            <thead><tr> <th class="text-center">Sr. No</th>
                                <th class="text-left">Brand Name</th>
                                <th class="text-left">Program Name</th>
                                <th class="text-left">Country Name</th>
                                <th class="text-left">State Name</th>
                                <th class="text-left">District Name</th>
                                <th class="text-left">Block Name</th>
                                <th class="text-left">Village Name</th>
                                <th class="text-left">Name</th>
                                <th class="text-left">Agriculture Irrigated</th>
                                <th class="text-left">Agriculture Rainfed</th>
                                <th class="text-left">Cotton Irrigated</th>
                                <th class="text-left">Cotton Rainfed</th>
                            </tr>
                        </thead>
                        </table>
                    </div> 
                    <div class="card-body" >
                        <div id="page">

                        </div>   
                    </div>
                </div>

            </div>
        </div>
        <!-- Bordered table end -->
    </div>

    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        var cc_farmer_datatable = '';
        var country_id = "<?php echo $country_id ?>";
        var season = $('#season').val();
     
        function get_farmers(){
            // d = []
            // d.country_id = country_id;
            // d.te 
            // var season = $('#season').val();
            var brand_id = $('#brand_id').val();
            var program_id = $('#program_id').val();
            var state_id = $('#state_id').val();
            var district_id = $('#district_id').val();
            var block_id = $('#block_id').val();
            var village_id = $('#village_id').val();
           
            csrf_token = $('input[name="_token"]').val();

            url = "<?php echo url('/master_farmers/get_master_farmer'); ?>?";
            url += "season="+season + "&";
            url += "brand_id=" + brand_id + "&";
            url += "program_id=" + program_id + "&";
            url += "state_id=" + state_id + "&";
            url += "district_id=" + district_id + "&";
            url += "block_id=" + block_id + "&";
            url += "village_id=" + village_id;
            // console.log(url);
            cc_farmer_datatable.ajax.url(url).load();        
            
            $html=`
            <button type="button" onclick="clear_search()" class="btn btn-outline-primary">Clear Search</button>

            `;
            $('#clear_btn').html($html);
        }
        
        
        cc_farmer_datatable = $('#farmers_table').DataTable({
            paging: true,
            serverSide: true,
            processing:true,
            searching: false,
            ordering: false,
            lengthChange: false,
            drawCallback: function(settings) {
                var api = this.api();
                var data = api.rows().data();
               
                if (data.length === 0) {
                    
                    $('.dataTables_paginate, .dataTables_info').hide();
                } else {
                    $('.dataTables_paginate, .dataTables_info').show();
                }
            },
            ajax:{
                url:"<?php echo url('/master_farmers/get_master_farmer'); ?>",
                dataSrc: 'farmers',
                error: 'message',
                data: function (d) {
                    // console.log(d);
                    d.season = $('#season').val();
                    d.country_id= country_id; 
                }    
            },
            
            columns: [
                { 
                    data: 'id',
                    render: function (data, type, row, meta) {
                        let link = "{{route('edit_master_farmer',['country_id'=>':country_id','farmer_id'=>':farmer_id','program_id'=>':program_id'])}}";
                        link = link.replace(':country_id', country_id);
                        link = link.replace(':farmer_id', row.id);
                        link = link.replace(':program_id', row.program_id);
                        return meta.row + meta.settings._iDisplayStart + 1 + " <a href='"+link+"' title='Edit'><i class='fa fa-edit'></a>";
                    }
                },
                { data: 'brand_name'},
                { data: 'program_name', },
                { data: 'country_name'},
                { data: 'state_name'},
                { data: 'district_name' },
                { data: 'block_name' },
                { data: 'village_name' },
                { data: 'name' },
                { data: 'agriculture_irrigated' },
                { data: 'agriculture_rainfed' },
                { data: 'cotton_irrigated' },
                { data: 'cotton_rainfed' },
            ], 

            error: function (xhr, textStatus, errorThrown) {
                console.error('Error: ' + textStatus, errorThrown);
            }

            
        });  
        

       var a_states = <?php echo (!empty($master_states)) ? json_encode($master_states) : '[]'; ?>;
	   var a_districts = <?php echo (!empty($master_districts)) ? json_encode($master_districts) : '[]'; ?>;
	   var a_block = <?php echo (!empty($master_blocks)) ? json_encode($master_blocks) : '[]'; ?>;
	   var a_village = <?php echo (!empty($master_villages)) ? json_encode($master_villages) : '[]'; ?>;

       $( document ).ready(function() {
			// var val='chart_'+j+'_country';
            var country_id = "<?php echo $country_id ?>";
			s_states_options = '<option value="0" >All State</option>'
			$.each( a_states, function( key, state_obj ) { 
				// console.log(state_obj)
				if(country_id != 0 && state_obj.country_id == country_id){
					s_states_options += '<option value='+state_obj.id+' >'+state_obj.state_name+'</option>' 
				}else if(country_id == 0){
					s_states_options += '<option value='+state_obj.id+' >'+state_obj.state_name+'</option>' 	
				}
			})
			$('#state_id').html(s_states_options);

		});
        
        function get_district()
		{   
            var country_id = "<?php echo $country_id ?>";
			var state_id= $('#state_id').val();
            // alert(state_id);
           
			s_district_options = '<option value="0" >All District</option>'
			$.each( a_districts, function( key, district_obj ) { 
				// console.log(district_obj)
				if(country_id != 0 && state_id !=0 && district_obj.country_id == country_id && district_obj.state_id == state_id){
					s_district_options += '<option value='+district_obj.id+' >'+district_obj.district_name+'</option>' 
				}else if(country_id == 0 && state_id ==0){
					s_district_options += '<option value='+district_obj.id+' >'+district_obj.district_name+'</option>' 	
				}else if(state_id !=0 && district_obj.state_id == state_id){
					s_district_options += '<option value='+district_obj.id+' >'+district_obj.district_name+'</option>' 
                }
			})
			$('#district_id').html(s_district_options);
		}

        function get_block()
        {
            var country_id = "<?php echo $country_id ?>";
			var state_id= $('#state_id').val();
            var district_id = $('#district_id').val();

            s_block_options = '<option value="0" >All Block</option>'
			$.each( a_block, function( key, block_obj ) { 
			
				if(country_id != 0 && state_id !=0 && district_id !=0 && block_obj.country_id == country_id && block_obj.state_id == state_id && block_obj.district_id == district_id ){
					s_block_options += '<option value='+block_obj.id+' >'+block_obj.block_name+'</option>' 
				}else if(country_id == 0 && state_id ==0 && district_id ==0){
					s_block_options += '<option value='+block_obj.id+' >'+block_obj.block_name+'</option>' 	
				}else if(district_id !=0 && block_obj.district_id == district_id){
					s_block_options += '<option value='+block_obj.id+' >'+block_obj.block_name+'</option>' 
                }
			})
			$('#block_id').html(s_block_options);

        }

        function get_village(chart_number)
        {
            var country_id = "<?php echo $country_id ?>";
			var state_id= $('#state_id').val();
            var district_id = $('#district_id').val();
            var block_id= $('#block_id').val();

            s_village_options = '<option value="0" >All village</option>'
			$.each( a_village, function( key, village_obj ) { 
			
				if(country_id != 0 && state_id !=0 && district_id !=0 && block_id !=0 && village_obj.country_id == country_id && 
                village_obj.state_id == state_id && village_obj.district_id == district_id  && village_obj.block_id == block_id ){
					s_village_options += '<option value='+village_obj.id+' >'+village_obj.village_name+'</option>' 
				}else if(country_id == 0 && state_id ==0 && district_id ==0 && block_id ==0){
					s_village_options += '<option value='+village_obj.id+' >'+village_obj.village_name+'</option>' 	
				}else if(block_id !=0 && village_obj.block_id == block_id){
					s_village_options += '<option value='+village_obj.id+' >'+village_obj.village_name+'</option>' 
                }
			})
			$('#village_id').html(s_village_options);

        }

        function clear_search()
        {
            location.reload();
        }
    </script>
      
</section>
    
@endsection

    

   







