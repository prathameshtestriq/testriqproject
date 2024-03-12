{{-- 
@php
    dd($a_chart);
@endphp --}}
<div class="row flex ">
    <?php foreach($a_chart as $chart_id=>$chart){ ?>
        <div class="<?php echo $chart['class_name']; ?>">
            <div class="card" style="border-radius:15px">
                <div class="card-body" >
                    <h5><?php echo  $chart['name_description']; ?></h1>
                    <div class="canvas" id="chart_<?php echo $chart_id; ?>">
						
					</div>
                        
                    </div>
                </div>
            </div>
       
        
    <?php } ?>        
	
</div>

<script>
	
    function renderDashboardCharts() {

	<?php foreach($a_chart as $chart_id=>$chart){  ?> 
	 Project_data = <?php echo (!empty($chart['series'])) ?  json_encode($chart['series']) : "[]"; ?>;
	
	labels=<?php echo (!empty($chart['label'])) ? json_encode($chart['label']) : "[]" ?>;
    console.log(Project_data,labels);
	// Generate chart data with dynamic data
     chartData = {
        series: [{
            name: 's',
            data: Project_data
        }],
        chart: {
            type: 'bar',
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

    // Render the chart in the current container
    new ApexCharts(document.querySelector('#chart_<?php echo $chart_id; ?>'), chartData).render();
		<?php } ?> 

}

	
renderDashboardCharts();

</script> 