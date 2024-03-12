/*
	1. a00000 - dark red
	2. 03689b - dark blue
	3. 2cc960 - light green
	4. 49a2fc - light blue
	5. ba51ad - pink
	6. 1d7f18 - dark green
	7. 5c5c63 - dark gray
	8. 7a79b7 - lavender
	9. 302020 - light black
   10. aa008e - dark pink
   
*/
;
var colors = Array('#a00000','#03689b','#aa008e','#49a2fc','#ba51ad', '#1d7f18','#5c5c63','#7a79b7','#302020','#2cc960');
var travelled_polylines = [],travelled_route_markers = [],defined_polylines = [],default_route_markers = []; 
function draw_new_line(map,color,drawline,marker_details,route_type){
	//console.log(drawline);
   var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";	

	var color = color;
	
	if(route_type == 'default_with_circle'){
		var lineSymbol = {
			path: google.maps.SymbolPath.CIRCLE,
			strokeColor: color,
			fillColor: color,
			fillOpacity: 1,
			scale : 2
		}; 
	}else if(route_type == 'stop_animate'){
		var lineSymbol = {
			path: google.maps.SymbolPath.CIRCLE,
			strokeColor: color,
			fillColor: color,
			fillOpacity: 1,
			scale : 3
		}; 
	}else{
		var lineSymbol = {
			path: car,
			scaledSize: new google.maps.Size(20, 20), // scaled size
			scale: .5,
			strokeColor: color,
			fillColor: color,
			fillOpacity: 1,
			offset: '5%'
		};  
	}
    var mapline = new google.maps.Polyline({
        path: drawline,
        geodesic: true,
        icons: [{
            icon: lineSymbol,
            offset: '100%'
        }],			
        strokeColor: color,
        strokeOpacity: 0.7,
        strokeWeight: 3,
        editable: false
         // editable: true
    });

	if(route_type == 'defined'){
		defined_polylines.push(mapline);
	}
	
	if(route_type == 'travelled'){
		travelled_polylines.push(mapline);
	}
	
	if(route_type == 'stop_animate'){			
	}else{
		animateCircle(mapline);
	}
    mapline.setMap(map);
	
    var start_lat = drawline[0].lat;
	//alert(start_lat);
    var start_lng = drawline[0].lng;
	//alert(start_lng);

    marker_pos = {lat: start_lat, lng: start_lng};
	//console.log(marker_pos);
	//alert(marker_pos);
	// alert(route_type);
	if(route_type != 'none'){
		//alert('test');
		if(route_type == 'defined'){
			//alert('test');
			var image = {
				url : base_url+'assets/img/skyblue_location.png',
				scaledSize: new google.maps.Size(65, 65) // scaled size
			}
		}else if(route_type == 'travelled'){
			var image = {
				//url :'assets/img/pink_location.png',
				url :base_url+'assets/img/pink_location.png',
				scaledSize: new google.maps.Size(65, 65) // scaled size
			}
		}else if(route_type == 'default_with_circle'){
			var image = {
				path: google.maps.SymbolPath.CIRCLE,
				scale: 2,
				strokeColor: color,
				fillColor: color,
				fillOpacity: 8
			}		
		}else if(route_type == 'stop_animate'){
			var image = {
				url :'assets/img/skyblue_location.png',
				//url :base_url+'assets/img/skyblue_location.png',
				scaledSize: new google.maps.Size(65, 65) // scaled size
			}		
		}
	}else{
		
		var image = {
			url :'assets/img/skyblue_location.png',
			//url :base_url+'assets/img/skyblue_location.png',
			scaledSize: new google.maps.Size(65, 65) // scaled size
		}
	}
   
	var marker = new google.maps.Marker({
			position: marker_pos,
			title: 'Start location',
			map:map,
			icon: image
		});
	if(marker_details != 'none'){
		var info_str = '<strong>Vehicle Number</strong> : <a href="">'+marker_details.vehicle_number+'</a>';
		var infowindow = new google.maps.InfoWindow()
		google.maps.event.addListener(marker,'click', (function(marker,info_str,infowindow){ 
			return function() {
			   infowindow.setContent(info_str);
			   infowindow.open(map,marker);
			};
		})(marker,info_str,infowindow));			
	} 

	if(route_type != 'none'){
		if(route_type == 'defined'){
			default_route_markers.push(marker);
		}

		if(route_type == 'travelled'){
			travelled_route_markers.push(marker);
		}
	}
/*	
    var marker = new google.maps.Marker({
        position: marker_pos ,
        label: '',
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 4,
            strokeColor: color,
            fillColor: color,
            fillOpacity: 8
        }, 
        draggable: false,				
        map: map,
        title: 'Start location'
    });	 
*/	
    var last_index = parseInt(drawline.length) - 1; 
    var end_lat = drawline[last_index].lat;
    var end_lng = drawline[last_index].lng;

    marker_pos = {lat: end_lat, lng: end_lng};
 /*   var marker = new google.maps.Marker({
    position: marker_pos ,
    label: '',
    icon: {
        path: google.maps.SymbolPath.CIRCLE,
        scale: 4,
        strokeColor: color,
        fillColor: color,
        fillOpacity: 8
    }, 
    draggable: false,				
    map: map,
    title: 'End Location'
    });   
	*/
	
/*	var image = {
		url :base_url+'assets/img/orange_location.png',
		scaledSize: new google.maps.Size(50, 50), // scaled size
	}
	*/
	
	if(route_type != 'none'){
		if(route_type == 'defined'){
			var image = {
				url : base_url+'assets/img/orange_location.png',
				scaledSize: new google.maps.Size(50, 50) // scaled size
			}
		}else if(route_type == 'travelled'){
			var image = {
				//url :'assets/img/brown_location.png',
				url :base_url+'assets/img/brown_location.png',
				scaledSize: new google.maps.Size(50, 50) // scaled size
			}		
		}else if(route_type == 'default_with_circle'){
			var image = {
				path: google.maps.SymbolPath.CIRCLE,
				scale: 2,
				strokeColor: color,
				fillColor: color,
				fillOpacity: 8
			}		
		}else if(route_type == 'stop_animate'){
			var image = {
				url :'assets/img/orange_location.png',
				//url :base_url+'assets/img/orange_location.png',
				scaledSize: new google.maps.Size(50, 50) // scaled size
			}		
		}
	}else{
		var image = {
			url :'assets/img/orange_location.png',
			//url :base_url+'assets/img/orange_location.png',
			scaledSize: new google.maps.Size(50, 50) // scaled size
		}
	}
	
	var marker = new google.maps.Marker({
			position: marker_pos,
			title: 'End Location',
			map:map,
			icon: image,
			zIndex:99999
		});

		if(route_type != 'none'){
			if(route_type == 'defined'){
				default_route_markers.push(marker);
			}

			if(route_type == 'travelled'){
				travelled_route_markers.push(marker);
			}
		}

}



	function animateCircle(line) {
		var count = 0;
		window.setInterval(function() {
			count = (count + 1) % 200;
			var icons = line.get('icons');
			icons[0].offset = (count / 2) + '%';
			line.set('icons', icons);
		}, 1000);
	}
	// Snap a user-created polyline to roads and draw the snapped path
function runSnapToRoad(path) {
  var pathValues = [];
  for (var i = 0; i < path.length; i++) {
  	latlong = path[i].lat+','+path[i].lng;
    pathValues.push(latlong);
  }
console.log(pathValues.join('|'));
  $.get('https://roads.googleapis.com/v1/snapToRoads', {
    interpolate: true,
    key: apiKey,
    path: pathValues.join('|')
  }, function(data) { console.log(data);
    processSnapToRoadResponse(data);
    drawSnappedPolyline();
   // getAndDrawSpeedLimits();
  });
}

// Store snapped polyline returned by the snap-to-road service.
function processSnapToRoadResponse(data) {
  snappedCoordinates = [];
  placeIdArray = [];
  for (var i = 0; i < data.snappedPoints.length; i++) {
    var latlng = new google.maps.LatLng(
        data.snappedPoints[i].location.latitude,
        data.snappedPoints[i].location.longitude);
    snappedCoordinates.push(latlng);
    placeIdArray.push(data.snappedPoints[i].placeId);
  }
}

// Draws the snapped polyline (after processing snap-to-road response).
function drawSnappedPolyline() {
console.log(snappedCoordinates);
  var snappedPolyline = new google.maps.Polyline({
    path: snappedCoordinates,
    strokeColor: 'red',
    strokeWeight: 5
  });

  snappedPolyline.setMap(map);
  //polylines.push(snappedPolyline);
}


function getAndDrawSpeedLimits() {
  for (var i = 0; i <= placeIdArray.length / 100; i++) {
    // Ensure that no query exceeds the max 100 placeID limit.
    var start = i * 100;
    var end = Math.min((i + 1) * 100 - 1, placeIdArray.length);

    drawSpeedLimits(start, end);
  }
}

// Gets speed limits for a 100-segment path and draws a polyline color-coded by
// speed limit. Must be called after processing snap-to-road response.
function drawSpeedLimits(start, end) {
    var placeIdQuery = '';
    for (var i = start; i < end; i++) {
      placeIdQuery += '&placeId=' + placeIdArray[i];
    }

    $.get('https://roads.googleapis.com/v1/speedLimits',
        'key=' + apiKey + placeIdQuery,
        function(speedData) {
          processSpeedLimitResponse(speedData, start);
        }
    );
}

// Draw a polyline segment (up to 100 road segments) color-coded by speed limit.
function processSpeedLimitResponse(speedData, start) {
  var end = start + speedData.speedLimits.length;
  for (var i = 0; i < speedData.speedLimits.length - 1; i++) {
    var speedLimit = speedData.speedLimits[i].speedLimit;
    var color = getColorForSpeed(speedLimit);

    // Take two points for a single-segment polyline.
    var coords = snappedCoordinates.slice(start + i, start + i + 2);

    var snappedPolyline = new google.maps.Polyline({
      path: coords,
      strokeColor: color,
      strokeWeight: 6
    });
    snappedPolyline.setMap(map);
   polylines.push(snappedPolyline);
  }
}

function getColorForSpeed(speed_kph) {
  if (speed_kph <= 40) {
    return 'purple';
  }
  if (speed_kph <= 50) {
    return 'blue';
  }
  if (speed_kph <= 60) {
    return 'green';
  }
  if (speed_kph <= 80) {
    return 'yellow';
  }
  if (speed_kph <= 100) {
    return 'orange';
  }
  return 'red';
}
