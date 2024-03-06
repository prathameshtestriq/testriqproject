<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <title>Chart</title>
	

	<style>
		* {
		  box-sizing: border-box;
		}
		
		/* Create three unequal columns that floats next to each other */
		.column {
		  float: left;
		  padding: 10px;
		  height: 70px; /* Should be removed. Only for demonstration */
		}
		
		.left, .right {
		  width: 25%;
		}
		
		.middle {
		  width: 50%;
		}
		
		/* Clear floats after the columns */
		.row:after {
		  content: "";
		  display: table;
		  clear: both;
		}
		h1 {
    text-align:center;
}
		</style>
</head>
<body>
	{{-- {{ dd($country) }} --}}
    <h1>Farmer chart</h1>
        <div class="row">
            {{-- <div class="col-md-12"> --}}
				  
				
					<div class="column">
						<label class="form-label" for="validationTooltip01"> Country Name: </label><br>
						<input type="text" value="{{ $country_name }}">
					</div>
					<div class="column">
						<label class="form-label" for="validationTooltip01"> State Name: </label><br>
						<input type="text" value="{{ $state_name }}">
					</div>
					<div class="column">
						<label class="form-label" for="validationTooltip01"> District Name: </label><br>
						<input type="text" value="{{ $district_name }}">
					</div>
				
				</div>
				<div class="row">   
					<div class="column">
						<label class="form-label" for="validationTooltip01"> Block Name </label><br>
						<input type="text" value="{{ $block_name }}">
					</div>
					<div class="column">
						<label class="form-label" for="validationTooltip01"> Village Name </label><br>
						<input type="text" value="{{ $village_name }} ">
					
					</div>
					<div class="column">
						<label class="form-label" for="validationTooltip01"> Kpi </label><br>
						<input type="text" value="{{ $kpi_name }} ">
					
					</div>
					
				</div>
				<div class="row">
					<div class="column">
						<label class="form-label" for="validationTooltip01"> Y-axis </label><br>
						<input type="text" value="{{ $kpi_que }} ">
					
					</div>
				</div>
				
				<div>
					<img src="{{ $file }}" alt="" width="700px" height="450px">
				</div>
				
	
</body>
</html>