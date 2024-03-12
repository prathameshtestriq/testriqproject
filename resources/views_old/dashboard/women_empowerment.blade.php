
<div class="row flex ">
                               
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body" >
				<h5>Purchase/sale of household asset</h1>
				<div class="canvas" id="chart-container-1">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Choosing which crop to plant</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Purchase of inputs for agriculture</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Purchase of big agricultural assets</h5>
				<p>(tractor,drip,etc)</p>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 

	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Selling of crop</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Household food expenditure for the family</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Decisions of education</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Health-realted decisions</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Household savings</h5>
				<div class="canvas" id="chart-container-2">
					
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Non-food expenditure decisions</h5>
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
                data: [800, 799, 575]
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
                categories: ['Low', 'Moderae', 'High']
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