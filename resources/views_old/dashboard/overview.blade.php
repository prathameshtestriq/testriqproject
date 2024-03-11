
<div class="row flex ">
    <div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body" >
				<h5>Number of Farmers</h5>
					<div class="row">
						<div class="col-xm-4 ">
							<div class="float-lg-left m-1">
								<h6>{{$numberOfGenderFarmers['Female'] + $numberOfGenderFarmers['Male']}}</h6>
								<span>Total Project Farmers</span>
							</div>
							<div class="float-lg-left m-1">
								<h6>{{$numberOfGenderFarmers['Female']}}</h6>
								<span>Total Female Farmers</span>
							</div>
							<div class="float-lg-right m-1">
								<h6>{{$numberOfGenderFarmers['Male']}}</h6>
								<span>Total Male Farmers</span>
							</div>
						</div>
						<div class="col-xm-12">
							<div class="canvas1" id="number_farmers_chart">
					    </div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body" >
				<h5>Cotton area</h1>
					<div class="row">
						<div class="col-xm-4 ">
							<div class="float-lg-left m-1">
								<h6>{{$cottonArea['cotton_irrigated'] + $cottonArea['cotton_rainfed']}}</h6>
								<span>Total Cotton area</span>
							</div>
							<div class="float-lg-left m-1">
								<h6>{{$cottonArea['cotton_irrigated']}}</h6>
								<span>Total Irrigaated area</span>
							</div>
							<div class="float-lg-right m-1">
								<h6>{{$cottonArea['cotton_rainfed']}}</h6>
								<span>Total Rainfed Farmers</span>
							</div>
						</div>
						<div class="col-xm-12">
							<div class="canvas1" id="cotton_area_chart">
					    </div>
					</div>

				</div>
			</div>
		</div>
	</div>                          
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body" >
				<h5>Yeild <span>(Kg/Acre)</span>
				</h1>
				<div class="canvas" id="yeild_chart">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Chemical pesticide<span>(ml/acre)</span></h5>
				<div class="canvas" id="chemical_pesticide_chart">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Natural pesticide<span>(ml/acre)</span></h5>
				<div class="canvas" id="natural_pesticide_chart">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Water usage<span>(m3/acre)</span></h5>
				<div class="canvas" id="water_usage_chart">
					
				</div>
			</div>
		</div>
	</div> 

	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Chemical Fertilizer<span>(kg/acre)</span></h5>
				<div class="canvas" id="chemical_fertilizer_chart">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Natural Fertilizer<span>(kg/acre)</span></h5>
				<div class="canvas" id="natural_fertilizer_chart">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Input cost<span>(per/acre)</span></h5>
				<div class="canvas" id="input_cost_chart">
					
				</div>
			</div>
		</div>
	</div> 
	<div class="col-sm-4">
		<div class="card m-1" style="border-radius:15px">
			<div class="card-body">
				<h5>Profit<span>(per/acre)</span></h5>
				<div class="canvas" id="profit_chart">
					
				</div>
			</div>
		</div>
	</div>
</div>


<script>
   
function render_pie_chart(data, xaxis, chart_id){
	var options = {
		series: data,
		chart: {
			width: 380,
			type: 'pie',
		},
		labels: xaxis,
		legend: {
			position: 'bottom',
			fontSize: '20px',
			fontWeight: 600,
		},	
		responsive: [{
			breakpoint: 480,
			options: {
				chart: {
					width: 200
				}
			}
		}]
	};

	new ApexCharts(document.querySelector("#" + chart_id), options).render();
}

function render_bar_chart(data, xaxis, chart_id){
	const chartData = {
		series: [{
			name: `Project`,
			data: [data[0],0]
		},{
			name: `Control`,
			data: [0,data[1]]
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
			categories: ["",""]
			
		},
		yaxis: {
			title: {
				text: 'Percentage'
			}
		},
		colors: ['#008FFB','#00E396']
	};

	// Render the chart in the current container
	new ApexCharts(document.querySelector("#" + chart_id), chartData).render();
}

render_bar_chart([{{$yeild['project']}},{{$yeild['control']}}], ['Project', 'Control'], 'yeild_chart')
render_bar_chart([{{$chemicalPesticide['project']}},{{$chemicalPesticide['control']}}], ['Project', 'Control'], 'chemical_pesticide_chart')
render_bar_chart([{{$naturalPesticide['project']}},{{$naturalPesticide['control']}}], ['Project', 'Control'], 'natural_pesticide_chart')
render_bar_chart([{{$waterUsage['project']}},{{$waterUsage['control']}}], ['Project', 'Control'], 'water_usage_chart')
render_bar_chart([{{$chemicalFertilizer['project']}},{{$chemicalFertilizer['control']}}], ['Project', 'Control'], 'chemical_fertilizer_chart')
render_bar_chart([{{$naturalFertilizer['project']}},{{$naturalFertilizer['control']}}], ['Project', 'Control'], 'natural_fertilizer_chart')
render_bar_chart([{{$inputCost['project']}},{{$inputCost['control']}}], ['Project', 'Control'], 'input_cost_chart')
render_bar_chart([{{$profit['project']}},{{$profit['control']}}], ['Project', 'Control'], 'profit_chart')

render_pie_chart([{{$numberOfFarmers['project']}},{{$numberOfFarmers['control']}}], ['Project', 'Control'], 'number_farmers_chart')
render_pie_chart([{{$cottonArea['project']}},{{$cottonArea['control']}}], ['Project', 'Control'], 'cotton_area_chart')

</script>