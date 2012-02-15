/*

	WP-GPX-Maps

*/

var t;
var funqueue = [];
var infowindow

var wrapFunction = function(fn, context, params) {
    return function() {
        fn.apply(context, params);
    };
}

function wpgpxmaps(params)
{
	funqueue.push( wrapFunction(_wpgpxmaps, this, [params]));
	unqueue();
}

function unqueue()
{
	if ((google == undefined || google.maps == undefined || google.visualization == undefined))
	{
		t = setTimeout("unqueue()",200);
	}
	else
	{
		while (funqueue.length > 0) {
			(funqueue.shift())();   
		}
	}
}

function _wpgpxmaps(params)
{

	var targetId = params.targetId;
	var mapType = params.mapType;
	var mapData = params.mapData;
	var graphData = params.graphData;
	var waypoints = params.waypoints;
	var unit = params.unit;
	var unitspeed = params.unitspeed;
	var color1 = params.color1;
	var color2 = params.color2;
	var color3 = params.color3;

	var el = document.getElementById("wpgpxmaps_" + targetId);
	var el_map = document.getElementById("map_" + targetId);
	var el_chart = document.getElementById("chart_" + targetId);
	
	switch (mapType)
	{
		case 'TERRAIN': { mapType = google.maps.MapTypeId.TERRAIN; break;}
		case 'SATELLITE': { mapType = google.maps.MapTypeId.SATELLITE; break;}
		case 'ROADMAP': { mapType = google.maps.MapTypeId.ROADMAP; break;}
		default: { mapType = google.maps.MapTypeId.HYBRID; break;}
	}
	var mapOptions = {
	  mapTypeId: mapType
	};
	var map = new google.maps.Map(el_map, mapOptions); 
	
	// Print WayPoints
	if (waypoints != '')
	{
		var image = new google.maps.MarkerImage('http://maps.google.com/mapfiles/ms/micons/flag.png',
			new google.maps.Size(32, 32),
			new google.maps.Point(0,0),
			new google.maps.Point(16, 32)
		);
		var shadow = new google.maps.MarkerImage('http://maps.google.com/mapfiles/ms/micons/flag.shadow.png',
			new google.maps.Size(59, 32),
			new google.maps.Point(0,0),
			new google.maps.Point(16, 32)
		);
		
		for (i=0; i < waypoints.length; i++) 
		{
			addWayPoint(map, image, shadow, waypoints[i][0], waypoints[i][1], waypoints[i][2], waypoints[i][3]);
		}
	}
	
	// Print Track
	if (mapData != '')		
	{
		var points = [];
		var bounds = new google.maps.LatLngBounds();

		for (i=0; i < mapData.length; i++) 
		{
			var p = new google.maps.LatLng(mapData[i][0], mapData[i][1]);
			points.push(p);
			bounds.extend(p);
		}
		
		var poly = new google.maps.Polyline({
			path: points,
			strokeColor: color1,
			strokeOpacity: .7,
			strokeWeight: 4
		});
		poly.setMap(map);
		map.setCenter(bounds.getCenter()); 
		map.fitBounds(bounds);
		var first = getItemFromArray(mapData,0)
		
		var current = new google.maps.MarkerImage("http://maps.google.com/mapfiles/kml/pal4/icon25.png",
			new google.maps.Size(32, 32),
			new google.maps.Point(0,0),
			new google.maps.Point(16, 16)
		);
		
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(first[0], first[1]),
			title:"Start",
			icon: current,
			map: map,
			zIndex: 10
		});
		
		google.maps.event.addListener(poly,'mouseover',function(event){
			if (marker)
			{
				marker.setPosition(event.latLng);	
				marker.setTitle("Current Position");
				if ( chart )
				{
					var l1 = event.latLng.Qa;				
					if (!(l1))
						l1 = event.latLng.Oa;
						
					var l2 = event.latLng.Ra;
					if (!(l2))
						l2 = event.latLng.Pa;	
						
					var ci = getClosestIndex(mapData,l1,l2);
					var r = chart.setSelection([{'row': parseInt(ci) + 1}]);
				}
			}
		});
	}
	
	// Print Graph
	if (graphData!= '')
	{
	
		var numberFormat1 = "#,###m";
		var numberFormat2 = "#,###m";
		
		if (unit=="1")
		{
			numberFormat1 = "#,##0.#mi";
			numberFormat2 = "#,###ft";		
		}
		
		var showSpeed = (graphData[0].length == 3);

		var data = new google.visualization.DataTable();
		data.addColumn('number', "Distance");		
		data.addColumn('number', "Elevation");

		var options = { curveType: "function",
						strictFirstColumnType: true, 
						hAxis : {format : numberFormat1},
						vAxis : {format : numberFormat2},
						legend : {position : 'none'},
						chartArea: {left:50,top:10,width:"100%",height:"75%"},
						colors:[color2,color3],
						tooltip: { showColorCode: true},
						fontSize:11
						};
						
		if (showSpeed)
		{
			var speedFormat="";
			
			if (unitspeed == '2') // miles/h
			{
				speedFormat = "#,##0.#mi/h";
			} 
			else if (unitspeed == '1') // km/h
			{
				speedFormat = "#,##0.#km/h";
			} 
			else
			{
				speedFormat = "#,##0.#m/s";
			}
			data.addColumn('number', "Speed");
			options.vAxes = { 0:{format : numberFormat2, targetAxisIndex : 0},
							  1:{format : speedFormat,    targetAxisIndex : 1}
							};
			options.series = {	0:{color: color2, visibleInLegend: true, targetAxisIndex : 0}, 
								1:{color: color3, visibleInLegend: true, targetAxisIndex : 1}
							 };
			options.chartArea.width="85%";
			//alert(el_chart.clientWidth);
		}
		
		data.addRows(graphData);
		var chart = new google.visualization.AreaChart(el_chart);		
		chart.draw(data, options);
		
		google.visualization.events.addListener(chart, 'onmouseover', function (e) {
			var r = e['row'];
			chart.setSelection([e]);
			if (marker)
			{
				var point = getItemFromArray(mapData,r)
				marker.setPosition(new google.maps.LatLng(point[0],point[1]));	
				marker.setTitle("Current Position");
			}
		});
		//google.visualization.events.addListener(chart, 'onmouseout', function (e) {
			//chart.setSelection([e]);
		//});
	}	
	else	
	{		
		el_chart.style.display='none';	
	}
}

function addWayPoint(map, image, shadow, lat, lon, title, descr)
{
	var p = new google.maps.LatLng(lat, lon);
	var m = new google.maps.Marker({
						  position: p,
						  map: map,
						  title: title,
						  animation: google.maps.Animation.DROP,
						  shadow: shadow,
						  icon: image,
						  zIndex: 5
					  });
	google.maps.event.addListener(m, 'mouseover', function() {
		if (infowindow)
		{
			infowindow.close(); 		
		}
		infowindow = new google.maps.InfoWindow({
			content: "<b>" + title + "</b></br />" + descr
		});
		infowindow.open(map,m);
	});	
}

function getItemFromArray(arr,index)
{
	try
	{
	  return arr[index];
	}
	catch(e)
	{
		return [0,0];
	}
}

function getClosestIndex(points,lat,lon)
{
	var dd=10000;
	var ii=0;
	for (i=0; i < points.length; i++) 
	{
		var d = dist(points[i][0], points[i][1], lat, lon);
		if ( d < dd )
		{
			ii = i;
			dd = d;
		}
	}
	return ii;
}

function dist(lat1,lon1,lat2,lon2)
{
	// mathematically not correct but fast
	var dLat = (lat2-lat1);
	var dLon = (lon2-lon1);
	return Math.sqrt(dLat * dLat + dLon * dLon);
}