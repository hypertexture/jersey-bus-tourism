<?php
$meta = array(
	'title' => 'Your Nearest Bus Stops',
	'description' => '',
	'keywords' => ''
);
$heading = 'YOUR NEAREST BUS STOPS';

require 'app/views/header.php' ;
?>
<style type="text/css">
.map-info { min-width:100px; }
	.map-info p { margin:5px auto; font-size:13px; }
	.map-info a { display:inline-block; text-transform:uppercase; background:#000; color:#fff; line-height:2em; padding:0 10px; letter-spacing:1px; margin:5px 0 0; }

#user-location { }
	#user-location h2 { }
.stop-info { }
.bus-info { }
</style>

 <!-- CONTENT SECTION -->
            <div id="content-home">

				<div id="panel"></div>
				<div id="map-canvas" style="width:100%; height:600px"></div>

            </div>
 <!-- END OF CONTENT SECTION -->
	
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="//maps.google.com/maps/api/js?v=3.exp"></script>
<script src="/nearby/linq.min.js"></script>
<script>
var map;

$(document).ready(function(){
	//get_start_selection();
		
	if(navigator.geolocation){
		navigator.geolocation.getCurrentPosition(initialize, get_start_selection(), {enableHighAccuracy:true,timeout:10000,maximumAge:60000});
	} else {
		alert('Functionality not available');
	}
	
	function initialize(user){
		
		//console.log(user);
		
		var myLocation = {
			Latitude: user.coords.latitude.toFixed(5),
			Longitude: user.coords.longitude.toFixed(5)
		};
		
		var mapOptions = {
			zoom: 17,
			center: new google.maps.LatLng(myLocation.Latitude, myLocation.Longitude),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			
		};

		map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
	
		myLocationMarker(myLocation);
		getJourneyCodes();
		getLiveBus();
		getNearbyStopPoints(myLocation);
		//getGeo(myLocation);
		
		//Corbiere
		//L'Etacq
		//Durrell
		//Bus Station
		//Gorey Pier
		//getNearbyStopPoints2(myLocation);

	}
	
	function get_start_selection(){
		var stops = [];
		$.getJSON("bus-stops.json", function (data) {
			$.each(data, function (index, d) {
				stops.push({ Name: d.Name, Latitude: d.Latitude, Longitude: d.Longitude, Code: d.Code });
			});
			
			if(typeof stops == 'object'){
				var sortedStops = stops.sort(SortByName);
				
				var start_select = '<div id="start_journey"><form><label for="start_select">Select starting point:</label> <select name="start_select"><option></option>';
				
				$.each(sortedStops, function(index, v){
					start_select += '<option value="' + v.Latitude + '|' + v.Longitude + '">' + v.Name + '</option>';					
				});
		
				start_select += '</select>&nbsp;&nbsp;&nbsp;<i>or</i>&nbsp;&nbsp;&nbsp;<label for="start_code">Enter code:</label> <input name="start_code" type="number" value="" maxlength="4"/> <button type="submit">Go</button></form></div>';
				
				$('#map-canvas').before(start_select);
			}
		});		
	}
	
	$(document).on('change','[name=start_select]',function(){
		var position = $(this).val();
		
		var coords = position.split('|');
		
		var startingPoint = {coords: {latitude: parseFloat(coords[0]), longitude: parseFloat(coords[1])}};
		
		initialize(startingPoint);
	});
	
	function SortByName(a, b) {
		var aName = a.Name,
			bName = b.Name;
		return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
	}
	
	$(document).on('click',$('#start_select button'),function(e){
		e.preventDefault();
		
		var code = $('[name=start_code]').val();
		
		if(code.length == 4){
			$.getJSON("bus-stops.json", function (data) {
				$.map(data,function(object){
					if(object.Code == code){
						var startingPoint = {coords: {latitude: parseFloat(object.Latitude), longitude: parseFloat(object.Longitude)}};
						
						initialize(startingPoint);
					}
				});
			});
		}
	});

	$(document).on('click','.stop-info a',function(e){
		e.preventDefault();
		var stopCode = $(this).data('code');
		
		console.log(stopCode);
	});

});
</script>
<script src="/app/js/new-app.js"></script>

<?php 
require 'app/views/footer.php';