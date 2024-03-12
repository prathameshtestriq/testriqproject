@extends('layout.index')
@section('title', 'Dashboard')

<!-- Dashboard Ecommerce start -->
@section('content')
    <section>
        {{-- <div class="content-header row"> --}}

        <div class="col-md-1"></div>
            <div class="content-body">
                <section id="multiple-column-form">
                    <div class="row justify-content-center">
                        <div class="col-sm-12">
                            <form method="post">
                                @csrf
                            <!-- <div class="card" >
                                <div class="card-body" style="margin-bottom:-10px">
                                    <div class="row ml-1 mr-1">
                                        <input type="hidden" name="active_field" value="" id="active_field">
                                        <div class="col-xs-1">
                                            <div class="form-group"> -->
                                       <h1>Welcome to dashboard</h1>

                            </form>
                            {{-- @include('dashboard.tabs') --}}
                            <?php ?>
                           <div class="tab-content">

                           <!-- </div>
                        </div>
                    </div> -->
        </section>
    </section>
<script>

$(document).ready(function() {
    loadTabContent('overview');
    $('#overview').addClass('active');
    // document.getElementById("overview").addClass('active');
    // Handle click on tabs using event delegation
    $('.dashboard-tab').on('click', '.btn', function() {
        $('.tab-link').removeClass('active');
        // Add 'active' class to the clicked tab
        $(this).addClass('active');
        var tab = $(this).attr('id'); // Get the ID of clicked tab
        loadTabContent(tab);
    });
});
function loadTabContent(tab) {
    var country_id = $('#chart_country').val();
    var state_id = $('#chart_state').val();
    var district_id = $('#chart_district').val();
    var block_id = $('#chart_block').val();
    var village_id = $('#chart_village').val();
    var season = $('#chart_season').val();
    var brand_id = $('#chart_brand_id').val();
    var program_id = $('#chart_program_id').val();
    if (tab) {
        $('#loader_outer').show(); // Show loader before AJAX request
        $.ajax({
            url: '/dashboard/' + tab,
            type: 'Post',
            data: {
                _token: "{{ csrf_token() }}",
                'season': season,
                'country': country_id,
                'state_id': state_id,
                'district_id': district_id,
                'block_id': block_id,
                'village_id': village_id,
                'brand_id': brand_id,
                'program_id': program_id,
            },
            success: function(response) {
                if (response && response.html) {
                    $('.tab-content').html(response.html);

                    if (response.a_chart) {
                        $.each(response.a_chart, function(chart_id, chart) {
                            // console.log(chart);
                            if (chart.chart_type == 'bar') {
                                var Project_data = chart.project || [];
                                var Control_data = chart.control || [];
                                var labels = chart.label || [];


                                var chartData = {
                                    series: [{
                                        name: 'Project',
                                        data: Project_data
                                    }],
                                    chart: {
                                        type: chart.chart_type,
                                        height: 350,
                                    },
                                    plotOptions: {
                                        bar: {
                                            horizontal: false,
                                            columnWidth: '55%',
                                            endingShape: 'rounded'
                                        },
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    stroke: {
                                        show: true,
                                        width: 2,
                                        colors: ['transparent']
                                    },
                                    xaxis: {
                                        labels: {
                                            rotate: 0,
                                            trim: true,
                                            hideOverlappingLabels: false,
                                            show: true,
                                        },
                                        categories: labels
                                    },
                                    yaxis: {
                                        title: {
                                            text: 'Percentage'
                                        }
                                    }
                                };

                                if (Control_data.length > 0) {
                                    chartData.series.push({
                                        name: 'Control',
                                        data: Control_data
                                    });
                                }

                                new ApexCharts(document.querySelector('#chart_' + chart_id), chartData).render();

                            }
                        });
                    }

                    if (response.p_chart) {
                        $.each(response.p_chart, function(chart_id, chart) {
                            var options = {
                                series: [chart.project, chart.control],
                                chart: {
                                    width: 380,
                                    type: 'pie',
                                },
                                labels: ['Project', 'Control'],
                                responsive: [{
                                    breakpoint: 480,
                                    options: {
                                        chart: {
                                            width: 200
                                        },
                                        legend: {
                                            position: 'bottom',
                                            fontSize: '20px',
                                            fontWeight: 600,
                                        }
                                    }
                                }],
                                plotOptions: {
                                    pie: {
                                        colors: function(data, seriesIndex, options) {
                                            return ['#008FFB', '#00E396']; // Define colors dynamically based on data
                                        }
                                    }
                                }
                            };

                            new ApexCharts(document.querySelector("#" + chart.chart_name), options).render();

                        });
                    }
                }
            },
            complete: function() {
                $('#loader_outer').hide(); // Hide loader after AJAX request completes
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#loader_outer').hide();
            }
        });
    }
}

</script>

    @endsection
    <script>


        var a_states = <?php echo !empty($state) ? json_encode($state) : '[]'; ?>;
     var a_season = <?php echo !empty($season) ? json_encode($season) : '[]'; ?>;
     var a_districts = <?php echo !empty($districts) ? json_encode($districts) : '[]'; ?>;
     var a_block = <?php echo !empty($block) ? json_encode($block) : '[]'; ?>;
     var a_village = <?php echo !empty($village) ? json_encode($village) : '[]'; ?>;



     function get_state() {

            // var val='chart_'+j+'_country';
            var country_id = $('#chart_country').val();

            s_states_options = '<option value="0" >All State</option>'
            $.each(a_states, function(key, state_obj) {

                if (country_id != 0 && state_obj.country_id == country_id) {

                    s_states_options += '<option value=' + state_obj.id + ' country_id='+state_obj.country_id+' >' + state_obj.state_name + '</option>'
                }else if (country_id == 0) {
                    s_states_options += '<option value=' + state_obj.id + ' country_id='+state_obj.country_id+' >' + state_obj.state_name + '</option>'
                }
            })

            $('#chart_state').html(s_states_options);
    }

        function get_district() {
            var country_id = $('#chart_country').val();
            var state_id = parseInt($('#chart_state').val());
            // alert(state_id);
            var selectedCountry = null;
            if(state_id > 0){
                var country_id = $('#chart_state option:selected').attr('country_id');
                $.each(a_states, function(key, state_obj) {

                    if (state_obj.id == state_id && state_obj.country_id == country_id) {
                        selectedCountry = state_obj.country_id;
                        return false; // Exit the loop early since we found the district
                    }
                });
            }

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
            $('#chart_district').html(s_district_options);
        }


        function get_block() {
            var country_id = $('#chart_country').val();
            var state_id = $('#chart_state').val();
            var district_id = $('#chart_district').val();

                    var selectedCountry = null;
            if(district_id > 0){
                var country_id = $('#chart_district option:selected').attr('country_id');
                $.each(a_districts, function(key, district_obj) {
                    if (district_obj.id == district_id && district_obj.country_id == country_id) {
                        selectedCountry = district_obj.country_id;
                        return false; // Exit the loop early since we found the district
                    }
                });
            }

            if(state_id > 0){
                var country_id = $('#chart_state option:selected').attr('country_id');
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
            $('#chart_block').html(s_block_options);

        }

        function get_village() {
            var country_id = $('#chart_country').val();
            var state_id = $('#chart_state').val();
            var district_id = $('#chart_district').val();
            var block_id = $('#chart_block').val();

            var selectedCountry = null;
            if(district_id > 0){
                var country_id = $('#chart_district option:selected').attr('country_id');
                $.each(a_districts, function(key, district_obj) {
                    if (district_obj.id == district_id && district_obj.country_id == country_id) {
                        selectedCountry = district_obj.country_id;
                        return false; // Exit the loop early since we found the district
                    }
                });
            }

            if(state_id > 0){
                var country_id = $('#chart_state option:selected').attr('country_id');
                $.each(a_states, function(key, state_obj) {
                    if (state_obj.id == state_id && state_obj.country_id == country_id) {
                        selectedCountry = state_obj.country_id;
                        return false; // Exit the loop early since we found the district
                    }
                });
            }

            if(block_id > 0){
                var country_id = $('#chart_block option:selected').attr('country_id');

                $.each(a_block, function(key, block_obj) {
                    if (block_obj.id == block_id && block_obj.country_id == country_id) {
                        selectedCountry = block_obj.country_id;
                        return false; // Exit the loop early since we found the district
                    }
                });
            }

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
            $('#chart_village').html(s_village_options);

        }

        function fetchdata()
        {
            var tab = $('.active').attr('id');
            loadTabContent(tab);

        }

 </script>
