
<div class="row flex ">
                               
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body" >
				<h5>Always manage to solve difficulties problems if i try hard enough</h1>
				<div class="canvas" id="chart-container-1">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Confident that they can deal efficiently with unexpected events</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>In trouble, can usually think of solution</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>When in a situation, think they can negotiate well to manage the solution</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 

	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Confidence level when communicating thoughts in family and society</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Enrollment status in any saving group</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 
</div>


<script>
    function renderDashboardCharts() {
    const chartContainers = document.querySelectorAll('.canvas');

    chartContainers.forEach((container, index) => {

        // Generate sample data for the chart (replace with your own data)
        const chartData = {
            series: [{
                name: `Series ${index + 1}`,
                data:  [995, 575, 565]
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
                categories: ['Never', 'Sometimes', 'Always']
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
	
}renderDashboardCharts();

</script>

