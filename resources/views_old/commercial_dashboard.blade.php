@extends('layout.index')
@section('title', 'Dashboard')
<style>
    .form-control {
        font-family: '&#xF002;', 'FontAwesome', 'Arial', sans-serif;
    }
    .btn_remove_chart{
        position: absolute;
        right: -15px;
        top: -15px;
    }
</style>
<!-- Dashboard Ecommerce start -->
@section('content')
    
    <section>
        <div class="row">
            <div class="col-md-12" id="chart_outer">
               
            </div>
            <div class="col-md-12  row d-flex justify-content-end">
                <div class="text-right">
                    
                        <p><button type="button" class="btn btn-outline-danger" onclick="add_chart()">
                                <i class="fa fa-plus"  aria-hidden="true"></i>Add Chart</button></p>
                   
                </div>
            </div>
    </section>

    <script type="text/javascript">
        var card_height = window.innerHeight / 2;
        if(card_height < 450){
            card_height = 450;
        }
        var chart_height = card_height - 100;
        var a_country = <?php echo !empty($country) ? json_encode($country) : '[]'; ?>;
        var a_states = <?php echo !empty($state) ? json_encode($state) : '[]'; ?>;
        var a_season = <?php echo !empty($season) ? json_encode($season) : '[]'; ?>;
        var a_districts = <?php echo !empty($districts) ? json_encode($districts) : '[]'; ?>;
        var a_block = <?php echo !empty($block) ? json_encode($block) : '[]'; ?>;
        var a_village = <?php echo !empty($village) ? json_encode($village) : '[]'; ?>;
        var a_kpi = <?php echo !empty($filter) ? json_encode($filter) : '[]'; ?>;
        var a_filter = <?php echo !empty($forms) ? json_encode($forms) : '[]'; ?>;
        var a_y_axis = <?php echo !empty($kpi_que) ? json_encode($kpi_que) : '[]'; ?>;
        var current_season=<?php echo !empty($current_season) ? json_encode($current_season) : ''; ?>;
        var chart_data='';
        var chart_number = 1;
        $(document).ready(function() {

          //  get_chart();
            add_chart();
        });
        var charts1=[];
        function add_chart() {
            // console.log(j)
            j = chart_number;
            // $('.main_card_' + chart1 + '').addClass('overflow-auto');
            // $('.main_container_' + chart1 + '').addClass('m-1');
            // $('.main_card_' + chart1 + '').css('height', '300px');

            var country = '';
            if (a_country != []) {
                $.each(a_country, function(key, country_obj) {
                    country += '<option value=' + country_obj.id + ' >' + country_obj.country_name + '</option>'
                })
            }

            var state = '';
            if (a_states != []) {
                $.each(a_states, function(key, state_obj) {
                    state += '<option value=' + state_obj.id + ' country_id='+state_obj.country_id+' >' + state_obj.state_name + '</option>'
                })
            }

            var district = '';
            if (a_districts != []) {
                $.each(a_districts, function(key, district_obj) {
                    district += '<option value=' + district_obj.id +  ' country_id='+district_obj.country_id+'>' + district_obj.district_name + '</option>'
                })
            }

            var block = '';
            if (a_block != []) {
                $.each(a_block, function(key, block_obj) {
                    block += '<option value=' + block_obj.id + ' country_id='+block_obj.country_id+' >' + block_obj.block_name + '</option>'
                })
            }

            var village = '';
            if (a_village != []) {
                $.each(a_village, function(key, village_obj) {
                    village += '<option value=' + village_obj.id + '  country_id='+village_obj.country_id+' >' + village_obj.village_name + '</option>'
                })
            }

            var x_filter = '';
            if (a_filter != []) {
                $.each(a_filter, function(key, x_filter_obj) {
                    x_filter += '<option value=' + x_filter_obj.id + ' >' + x_filter_obj.filter_name + '</option>'
                })
            }

            var kpi = '';
            if (a_kpi != []) {
                $.each(a_kpi, function(key, kpi_obj) {
                    kpi += '<option value=' + kpi_obj.id + ' >' + kpi_obj.name + '</option>'
                })
            }
            // console.log(a_season)
             var seasons='';
             if(a_season !=[])
             {
                $.each(a_season,function(key,season_obj){
                    selected = '';
                    if(season_obj.season==current_season)
                    {
                        selected='selected'
                    }
                    seasons += '<option value=' + season_obj.season + ' '+selected+'>' + season_obj.season + '</option>'
                })
             }

            var result = `<div class="row " >
            <div class="col-md-3 ">
                <div class="card main_card_` + j + `" style="overflow-y:scroll; overflow-x: hidden;border-radius: 15px; height:`+card_height+`px">
                    <div class="container main_container_` + j + ` m-1 ">
                        <input type='hidden' class="hidden `+j+`" name="hidden_data[]">
                        <h6 class="text-primary">Chart `+j+` Filter</h6>
                        <div class="col-md-11 col-12 ">
                            <div class="form-group">
                                <label class="form-label" for="validationTooltip01"> Season</label>
                                <select class=" form-control form-select " id="chart_` + j + `_season"
                                    name='season'>
                                    <option value='0'>All Season</option>
                                        `+seasons+`
                                </select>
                            </div>
                        </div>

                        <div class="col-md-11 col-12 ">
                            <div class="form-group">
                                <label class="form-label" for="validationTooltip01"> Country Name </label>
                                <select class=" form-control form-select "
                                    id="chart_` + j + `_country" name='country[]'
                                    onchange="get_state(` + j + `);get_village(` + j + `);get_block(` + j + `); get_district(` + j + `);">
                                    <option value='0'>All Country</option>
                                    `+country+`
                                </select>
                                <h5><small class="text-danger" id="chart_` + j + `_country_err"></small></h5>
                            </div>
                        </div>
                        <div class="col-md-11 col-12 ">
                            <div class="form-group">
                                <label class="form-label" for="validationTooltip01"> State Name </label>
                                <select class=" form-control form-select " id="chart_` + j + `_state"
                                    name='state' onchange="get_district(` + j + `); get_village(` + j + `);get_block(` + j + `);">
                                    <option value='0'>All State</option>
                                    ` + state + `
                                </select>
                            </div>
                        </div>
                        <div class="col-md-11 col-12 ">
                            <div class="form-group">
                                <label class="form-label" for="validationTooltip01"> District Name </label>
                                <select class=" form-control form-select "
                                    id="chart_` + j + `_district" name='district'
                                    onchange="get_block(` + j + `);get_village(` + j + `); ">
                                    <option value='0'>All District</option>
                                    ` + district + `
                                </select>
                            </div>
                        </div>
                        <div class="col-md-11 col-12 ">
                            <div class="form-group">
                                <label class="form-label" for="validationTooltip01"> Block Name </label>
                                <select class=" form-control form-select" id="chart_` + j + `_block"
                                    name='block' onchange="get_village(` + j + `)">
                                    <option value='0'>All Block</option>
                                    ` + block + `
                                </select>
                            </div>
                        </div>

                        <div class="col-md-11 col-12 ">
                            <div class="form-group">
                                <label class="form-label" for="validationTooltip01"> Village Name </label>
                                <select class=" form-control form-select"
                                    id="chart_` + j + `_village" name='village'>
                                    <option value='0'>All Village</option>
                                    ` + village + `
                                </select>
                            </div>
                        </div>

                        <div class="col-md-11 col-12 ">
                            <div class="form-group">
                                <label class="form-label" for="validationTooltip01"> Brand Name </label>
                                <select class=" form-control form-select"
                                    id="chart_` + j + `_brand_id" name='brand'>
                                    <option value='0'>All Brand</option>

                                    <?php 
                                foreach ($brand as $val)
                                
                                {
                                    $selected = '';
                                    if(old('brand') == $val->id){
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>>
                                        <?php echo $val->brand_name; ?></option>
                                    <?php 
                                }
                                ?>

                                </select>
                                <h5><small class="text-danger"
                                        id="chart_` + j + `_brand_err"></small></h5>
                            </div>
                        </div>
                        <div class="col-md-11 col-12 ">
                            <div class="form-group">
                                <label class="form-label" for="validationTooltip01"> Program Name </label>
                                <select class=" form-control form-select"
                                    id="chart_` + j + `_program_id" name='program'>
                                    <?php 
                                foreach ($program as $val)
                                
                                {
                                    $selected = '';
                                    if(old('program') == $val->id){
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>>
                                        <?php echo $val->program_name; ?></option>
                                    <?php 
                                }
                                ?>

                                </select>
                                <h5><small class="text-danger" id="chart_` + j + `_program_err"></small></h5>
                            </div>
                        </div>

                        <div class="col-md-11 col-12 ">

                            <div class="form-group">
                                <label class="form-label" for="validationTooltip01"> Project/Control </label>
                                <select class=" form-control form-select "
                                    id="chart_` + j + `_project_control" name='project_control' ">
                                    <option value='0'>All</option>

                                    <?php 
                                foreach ($option as $val)
                                
                                {
                                    $selected = '';
                                    if(old('option') == $val->id){
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>>
                                        <?php echo $val->name; ?></option>
                                    <?php 
                                }
                                ?>

                                </select>
                                <h5><small class="text-danger" id="chart_` + j + `_country_err"></small></h5>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-md-9 ">
                <div class="card main_card_` + j + `" style=" border-radius: 15px; height:`+card_height+`px">
                    <div class="container" style="margin-top: 20px">
                        <div class="row">
                            <div class="btn_remove_chart">
                                <i class="fa fa-remove btn btn-danger float-lg-right" aria-hidden="true" onclick="return remove_card(`+j+`)"  ></i>
                            </div>
                            <div class="col-md-3 d-flex">
                                <div class="col-sm-4" style=" margin-top:5px">
                                    <label for="x-axis"> X Axis </label>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group ">
                                        <select class=" form-control form-select" id="chart_` + j + `_filter" name='filter[]'>
                                            <?php 
                                            foreach ($filter as $val)
                                            {
                                                $selected = '';
                                                if(old('filter') == $val->id){
                                                    $selected = 'selected';
                                                }
                                                ?>
                                            <option value="<?php echo $val->filter_name; ?>"
                                                <?php echo $selected; ?>><?php echo $val->filter_name; ?></option>
                                            <?php 
                                            }
                                            ?>
                                        </select>                                            
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex">
                                <div class="col-sm-2" style=" margin-top:5px">
                                    <label for="x-axis"> KPI </label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="form-group ">

                                        <select class=" form-control form-select"
                                            id="chart_` + j + `_kpi" name='kpi[]'
                                            onchange="return change_que(` + j + `)">
                                            <option value='0'>Select KPI</option>
                                            <?php 
                                            foreach ($kpi as $val)
                                            { ?>
                                            <option value="<?php echo $val->id; ?>" is_questions="<?php echo $val->fields_map; ?>"><?php echo $val->name; ?></option>
                                            <?php 
                                            }
                                            ?>

                                        </select>
                                         <h5><small class="text-danger" id="chart_` + j + `_kpi_err"></small></h5>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex" >
                                <div class="col-md-6 d-flex" id="div_questions_exists_` + j +`" style="display:none !important">
                                    <div class="col-sm-4" style=" margin-top:5px">
                                        <label for="y-axis"> Y Axis </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="form-group ">
                                            <select  class=" form-control form-select"
                                                id="chart_` + j + `_y_axis_filter"
                                                name='y_filter[]'>
                                                <option value='0'>Select KPI first</option>
                                                ` + x_filter + `  
                                            </select>
                                            <h5><small class="text-danger"
                                                    id="chart_` + j + `_y_filter_err"></small>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <!--<div class="col-sm-3">
                                    <div class="form-group ">
                                        <select class=" form-control form-select"
                                            id="chart_` + j + `_unit_filter"
                                            name='unit[]'>
                                            <option value='0'>Select unit</option>
                                            <option value='1'>Acre</option>
                                            <option value='1'>Hectore</option>
                                        </select>
                                        <h5><small class="text-danger"
                                                id="chart_` + j + `_unit_err"></small>
                                        </h5>
                                    </div>
                                </div>-->
                                <div id='loader_outer'>
                                    <div id='loader' ></div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary "  onclick=" drawchart(` + j + `);"><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        </div>
                        <div class=" chart_container` + j + `" ></div>          
                    </div>
                </div>
            </div>
            `;
            $("#chart_outer").append(result);
            //$('#chart_'+chart_number+'_district').select2();
            chart_number++;
        }

        function get_state(chart_number) {
            // var val='chart_'+j+'_country';
            var country_id = $('#chart_' + chart_number + '_country').val();

            s_states_options = '<option value="0" >All State</option>'
            $.each(a_states, function(key, state_obj) {
                // console.log(state_obj)
                if (country_id != 0 && state_obj.country_id == country_id) {
                    s_states_options += '<option value=' + state_obj.id + ' country_id='+state_obj.country_id+' >' + state_obj.state_name + '</option>'
                } else if (country_id == 0) {
                    s_states_options += '<option value=' + state_obj.id + ' country_id='+state_obj.country_id+' >' + state_obj.state_name + '</option>'
                }
            })
            $('#chart_' + chart_number + '_state').html(s_states_options);
        }

        function get_district(chart_number) {
            var country_id = $('#chart_' + chart_number + '_country').val();
            var state_id = parseInt($('#chart_' + chart_number + '_state').val());
            // alert(state_id);
            var selectedCountry = null;
            if(state_id > 0){
                var country_id = $('#chart_' + chart_number + '_state option:selected').attr('country_id');
                $.each(a_states, function(key, state_obj) {
                // console.log(state_obj)
                    if (state_obj.id == state_id && state_obj.country_id == country_id) {
                        selectedCountry = state_obj.country_id;
                        return false; // Exit the loop early since we found the district
                    }
                });
            }
            // console.log(selectedCountry)
            s_district_options = '<option value="0" >All District</option>'
            $.each(a_districts, function(key, district_obj) {
                // console.log(district_obj)

                if(country_id != 0 || state_id != 0){
                  
                    if(state_id != 0){
                        if(district_obj.country_id == selectedCountry && district_obj.state_id == state_id){
                            s_district_options += '<option value=' + district_obj.id + ' country_id='+district_obj.country_id+' >' + district_obj.district_name +
                        '</option>'
                        }
                    }else if(country_id != 0){
                        if( district_obj.country_id == country_id){
                            s_district_options += '<option value=' + district_obj.id + ' country_id='+district_obj.country_id+' >' + district_obj.district_name +
                        '</option>'
                        }
                    }
                }else{
                    s_district_options += '<option value=' + district_obj.id + '  country_id='+district_obj.country_id+'>' + district_obj.district_name +
                        '</option>'
                }
            })
            $('#chart_' + chart_number + '_district').html(s_district_options);
        }
   

        function get_block(chart_number) {
            var country_id = $('#chart_' + chart_number + '_country').val();
            var state_id = $('#chart_' + chart_number + '_state').val();
            var district_id = $('#chart_' + chart_number + '_district').val();

                    var selectedCountry = null;
            if(district_id > 0){
                var country_id = $('#chart_' + chart_number + '_district option:selected').attr('country_id');        
                $.each(a_districts, function(key, district_obj) {
                    if (district_obj.id == district_id && district_obj.country_id == country_id) {
                        selectedCountry = district_obj.country_id;
                        return false; // Exit the loop early since we found the district
                    }
                });
            }

            if(state_id > 0){
                var country_id = $('#chart_' + chart_number + '_state option:selected').attr('country_id');
                $.each(a_states, function(key, state_obj) {
                    if (state_obj.id == state_id && state_obj.country_id == country_id) {
                        selectedCountry = state_obj.country_id;
                        return false; // Exit the loop early since we found the district
                    }
                });
            }

            s_block_options = '<option value="0" >All Block</option>'
            $.each(a_block, function(key, block_obj) {

                if(country_id != 0 || state_id != 0 || district_id != 0){
                  if(district_id != 0){
                    if(block_obj.country_id == selectedCountry && block_obj.district_id == district_id ){
                          s_block_options += '<option value=' + block_obj.id + ' country_id='+block_obj.country_id+' >' + block_obj.block_name +
                      '</option>'
                      }
                  }else if(state_id != 0){
                   
                    if(block_obj.country_id == selectedCountry && block_obj.state_id == state_id ){
                          s_block_options += '<option value=' + block_obj.id + ' country_id='+block_obj.country_id+' >' + block_obj.block_name +
                      '</option>'
                      }
                  }else if(country_id != 0){
                      if( block_obj.country_id == country_id){
                       
                          s_block_options += '<option value=' + block_obj.id + ' country_id='+block_obj.country_id+' >' + block_obj.block_name +
                      '</option>'
                      }
                  }
              }else{
                  s_block_options += '<option value=' + block_obj.id + '  country_id='+block_obj.country_id+'>' + block_obj.block_name +
                      '</option>'
              }
                
            })
            $('#chart_' + chart_number + '_block').html(s_block_options);

        }

        function get_village(chart_number) {
            var country_id = $('#chart_' + chart_number + '_country').val();
            var state_id = $('#chart_' + chart_number + '_state').val();
            var district_id = $('#chart_' + chart_number + '_district').val();
            var block_id = $('#chart_' + chart_number + '_block').val();

            var selectedCountry = null;
            if(district_id > 0){
                var country_id = $('#chart_' + chart_number + '_district option:selected').attr('country_id');        
                $.each(a_districts, function(key, district_obj) {
                    if (district_obj.id == district_id && district_obj.country_id == country_id) {
                        selectedCountry = district_obj.country_id;
                        return false; // Exit the loop early since we found the district
                    }
                });
            }

            if(state_id > 0){
                var country_id = $('#chart_' + chart_number + '_state option:selected').attr('country_id');
                $.each(a_states, function(key, state_obj) {
                    if (state_obj.id == state_id && state_obj.country_id == country_id) {
                        selectedCountry = state_obj.country_id;
                        return false; // Exit the loop early since we found the district
                    }
                });
            }

            if(block_id > 0){
                var country_id = $('#chart_' + chart_number + '_block option:selected').attr('country_id');

                $.each(a_block, function(key, block_obj) {
                    if (block_obj.id == block_id && block_obj.country_id == country_id) {
                        selectedCountry = block_obj.country_id;
                        return false; // Exit the loop early since we found the district
                    }
                });
            }    
            console.log(selectedCountry)
            s_village_options = '<option value="0" >All village</option>'
            $.each(a_village, function(key, village_obj) {

                if(country_id != 0 || state_id != 0 || district_id != 0 || block_id !=0){
                    if(block_id != 0){
                        if(village_obj.country_id == selectedCountry && village_obj.block_id == block_id ){
                            s_village_options += '<option value=' + village_obj.id + ' country_id='+village_obj.country_id+'>' + village_obj.village_name +
                        '</option>'
                        }
                    }else if(district_id != 0){
                        if(village_obj.country_id == selectedCountry && village_obj.district_id == district_id ){
                            s_village_options += '<option value=' + village_obj.id + ' country_id='+village_obj.country_id+'>' + village_obj.village_name +
                        '</option>'
                        }
                    }else if(state_id != 0){
                    
                        if(village_obj.country_id == selectedCountry && village_obj.state_id == state_id ){
                            s_village_options += '<option value=' + village_obj.id + ' country_id='+village_obj.country_id+'>' + village_obj.village_name +
                        '</option>'
                        }
                    }else if(country_id != 0){
                      if( village_obj.country_id == country_id){
                        
                        s_village_options += '<option value=' + village_obj.id + ' country_id='+village_obj.country_id+'>' + village_obj.village_name +
                        '</option>'
                      }
                  }
              }else{
                    s_village_options += '<option value=' + village_obj.id + ' country_id='+village_obj.country_id+'>' + village_obj.village_name +
                        '</option>'
              }
                
            })
            $('#chart_' + chart_number + '_village').html(s_village_options);

        }

        function change_que(chart_number) {
            var kpi_id = $('#chart_' + chart_number + '_kpi').val();
            //  console.log(kpi_id);
            var is_question = $('#chart_' + chart_number + '_kpi option:selected').attr('is_questions');
            
            if(is_question == 1){
                $('#div_questions_exists_'+chart_number).show();
            }else{
                console.log('hh');
                $('#div_questions_exists_'+chart_number).attr('style', 'display:none !important');
            }
            y_axis_option = '<option value="0" >All</option>'
            $.each(a_y_axis, function(key, y_axis_obj) {

                if (kpi_id == y_axis_obj.kpi_id) {
                    y_axis_option += '<option value=' + y_axis_obj.id + ' >' +y_axis_obj.form_name+'(' + y_axis_obj.name_description +')'
                        '</option>'
                } else if (kpi_id == 0) {
                    y_axis_option += '<option value=' + y_axis_obj.id + ' >' +y_axis_obj.form_name+ '('+ y_axis_obj.name_description +')'
                        '</option>'
                }
            })
            $('#chart_' + chart_number + '_y_axis_filter').html(y_axis_option);

        }
       
        var charts = [];
        function drawchart(chart_number) {
            is_valid = chartvalidation(chart_number);

            
            var season = $('#chart_' + chart_number + '_season').val();
            var country_id = $('#chart_' + chart_number + '_country').val();
            var state_id = $('#chart_' + chart_number + '_state').val();
            var district_id = $('#chart_' + chart_number + '_district').val();
            var block_id = $('#chart_' + chart_number + '_block').val();
            var village_id = $('#chart_' + chart_number + '_village').val();
            var brand_id = $('#chart_' + chart_number + '_brand_id').val();
            var project_control = $('#chart_' + chart_number + '_project_control').val();

            var x_axis = $('#chart_' + chart_number + '_filter').val();
            var kpi_id = $('#chart_' + chart_number + '_kpi').val();
            var kpi_has_fields = $('#chart_' + chart_number + '_kpi option:selected').attr('is_questions');
            var program_id = $('#chart_' + chart_number + '_program_id').val();
            var y_axis = $('#chart_' + chart_number + '_y_axis_filter').val();
            if (is_valid) {
                $('#loader_outer').show();
           
                $.ajax({
                    url: "{{ url('api/get_comparison_chart_data') }}",
                    type: 'post',
                    data: {
                        _token: "{{ csrf_token() }}",
                        'season': season,
                        'country_id': country_id,
                        'state_id':state_id,
                        'district_id':district_id,
                        'block_id':block_id,
                        'village_id':village_id,
                        'brand_id':brand_id,
                        'project_control':project_control,
                        'xaxis': x_axis,
                        'kpi_id': kpi_id,
                        'program_id': program_id,
                        'yaxis_id': y_axis,
                        'kpi_has_fields':kpi_has_fields
                    },
                    success: function(result) {
                        has_sub_question = parseInt($('#chart_'+chart_number+'_kpi option:selected').attr('is_questions'));
                        if(has_sub_question){
                            if(parseInt($('#chart_'+chart_number+'_y_axis_filter option:selected').val()) == 0){
                                y_axis_lable = $('#chart_'+chart_number+'_kpi option:selected').text();
                            }else{
                                y_axis_lable = $('#chart_'+chart_number+'_y_axis_filter option:selected').text();
                            }
                            

                        }  else{
                            y_axis_lable = $('#chart_'+chart_number+'_kpi option:selected').text();
                        }
                        var series1 = result.data.series1;
                        var series2 = result.data.series2;
                        var labels = result.data.labels;

                        var options = {
                            series: [{
                                name: '',
                                type: 'column',
                                data: series1
                            }],
                            chart: {
                                height: chart_height,
                                type: 'bar'
                            },
                            dataLabels: {
                                enabled: true,
                                offsetY: -20,
                                style: {
                                    fontSize: '12px',
                                    colors: ["#304758"]
                                }
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 5,
                                    dataLabels: {
                                    position: 'top', // top, center, bottom
                                    },
                                }
                            },
                            stroke: {
                                width: 2
                            },
                            
                            labels: labels,
                            xaxis: {
                                type: 'data'
                            },
                            tooltip: {
                                enabled:false,
                            },
                            yaxis: [{
                                title: {
                                    text: y_axis_lable
                                }
                            }
                            //,
                            //  {
                            //     opposite: true,
                            //     title: {
                            //         text: 'District'
                            //     }
                            // }
                         ]
                        };
                        var chartElement = document.querySelector(".chart_container" + chart_number);
                        var chart = new ApexCharts(chartElement, options);
                        if(charts[chart_number] !==undefined){
                            charts[chart_number].destroy();
                        }
                        charts[chart_number] = chart;
                        //charts.push(chart);
                        chart.render();    
                        $('#loader_outer').hide();                    
                    },
                    error: function(jqXHR, testStatus, error) {
                        alert("Page cannot open. Error: " + error);
                        $('#loader_outer').hide()
                    }
                });
                
            }
        }   
    
        function get_pdf(chart_number) {
            
            let chart_length=charts.length;
           
            charts[chart_length-1].dataURI().then(({ imgURI, blob }) => {
        
                var imgbase64 = imgURI;
                // console.log(imgbase64)
                var country_id = $('#chart_' + chart_number + '_country').val();
                var state_id = $('#chart_' + chart_number + '_state').val();
                var district_id = $('#chart_' + chart_number + '_district').val();
                var block_id = $('#chart_' + chart_number + '_block').val();
                var kpi_id = $('#chart_' + chart_number + '_kpi').val();
                var chart_y_axis = $('#chart_' + chart_number + '_y_axis_filter').val();
                var village_id = $('#chart_' + chart_number + '_village').val();
                var url = '';
               
                if (country_id != 0) {
                    $.ajax({
                        url: "<?php echo url('download_pdf'); ?>",
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            'country_id': country_id,
                            'state_id': state_id,
                            'district_id': district_id,
                            'block_id': block_id,
                            'kpi_id': kpi_id,
                            'village_id': village_id,
                            'chart_image': imgbase64,
                            'chart_y_axis': chart_y_axis,
                            'state': JSON.stringify(a_states),
                            'district': JSON.stringify(a_districts),
                            'block': JSON.stringify(a_block),
                            'village': JSON.stringify(a_village)
                        },
                        success: function(result) {
                            const data = result.url;
                            const link = document.createElement('a');
                            link.setAttribute('href', data);
                            link.setAttribute('download', 'chart.pdf'); // Need to modify filename ...
                            link.click();
                        },
                        error: function(jqXHR, testStatus, error) {
                            // console.log(error);
                            alert("Page " + url + " cannot open. Error:" + error);
                            $('#loader_outer').hide();
                        },
                    });
                }else{
                   
                    if ($('#chart_'+chart_number+'_country').val() == "0"){
                        $('#chart_'+chart_number+'_country').parent().addClass('has-error');
                        $('#chart_'+chart_number+'_country_err').html('Please Select country.');
                        $('#chart_'+chart_number+'_country').focus();
                        $('#chart_'+chart_number+'_country').keyup(function () {
                        $('#chart_'+chart_number+'_country').parent().removeClass('has-error');
                        $('#chart_'+chart_number+'_country_err').html('');
                        });
                        return false;
                    }
                }
            });
        }

        function remove_card(chart_number){
          if(chart_number !== 1){
            $(".main_card_" + chart_number).remove();
          }
        }

        function chartvalidation(chart_number){
        
           if ($('#chart_'+chart_number+'_country').val() == "0"){
                $('#chart_'+chart_number+'_country').parent().addClass('has-error');
                $('#chart_'+chart_number+'_country_err').html('Please Select country.');
                $('#chart_'+chart_number+'_country').focus();
                $('#chart_'+chart_number+'_country').click(function () {
                $('#chart_'+chart_number+'_country').parent().removeClass('has-error');
                $('#chart_'+chart_number+'_country_err').html('');
                });
                return false;
            }

            if ($('#chart_'+chart_number+'_kpi').val() == "0"){
                $('#chart_'+chart_number+'_kpi').parent().addClass('has-error');
                $('#chart_'+chart_number+'_kpi_err').html('Please Select kpi.');
                $('#chart_'+chart_number+'_kpi').focus();
                $('#chart_'+chart_number+'_kpi').click(function () {
                $('#chart_'+chart_number+'_kpi').parent().removeClass('has-error');
                $('#chart_'+chart_number+'_kpi_err').html('');
                });
                return false;
            }
            return true;
        }
    </script>
@endsection