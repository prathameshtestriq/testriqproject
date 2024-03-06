
<div class="row flex ">
                               
	<div class="col-sm-6">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body" >
				<h5>Konw which brand has initiated this program</h1>
				<div class="canvas" id="chart-container-1">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-6">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Decent Labour Standards:Percentage of farmer aware of</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 
	
</div>


<script>
    function renderDashboardCharts() {
  
    const chartContainers = document.querySelectorAll('.canvas');
	 
const dynamicData = [
    { seriesName: 'Series 1', data: [990, 575,575] ,label: ['agricultural activities', 'Child regularly attend school','Others']},
    { seriesName: 'Series 2', data: [800, 575],label: ['Legal age for connecting', 'Government-mandeted minimum wage for labour'] },

];


// Loop through each container
chartContainers.forEach((container, index) => {
    // Get the dynamic data for the current index
    const currentData = dynamicData[index];
    // Generate chart data with dynamic data
    const chartData = {
        series: [{
            name: currentData.seriesName,
            data: currentData.data
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
            categories: currentData.label
        },
        yaxis: {
            title: {
                text: 'Percentage'
            }
        }
    };
		// Render the chart in the current container
		new ApexCharts(container, chartData).render();
		});
        // Render the chart in the current container
     
   
}renderDashboardCharts();

</script>