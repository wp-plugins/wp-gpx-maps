/*

	WP-GPX-Maps

*/

var loc_en = 
{
  "length"  : "Length",
  "altitude": "Altitude"
};
var loc_it = 
{
  "length"  : "Lunghezza",
  "altitude": "Altitudine"
};

var loc = loc_en;
var t;
var funqueue = [];
var infowindow

var wrapFunction = function(fn, context, params) {
    return function() {
        fn.apply(context, params);
    };
}

function wpgpxmaps(targhetId,mapType,mapData,graphData,waypoints,unit,color1,color2)
{
	funqueue.push( wrapFunction(_wpgpxmaps, this, [targhetId,mapType,mapData,graphData,waypoints,unit,color1,color2]));
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

function _wpgpxmaps(targhetId,mapType,mapData,graphData,waypoints,unit,color1,color2)
{
	var el = document.getElementById("wpgpxmaps_" + targhetId);
	var el_map = document.getElementById("map_" + targhetId);
	var el_chart = document.getElementById("chart_" + targhetId);
	
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
		for(var i in waypoints)
		{
			addWayPoint(map, image, shadow, waypoints[i][0], waypoints[i][1], waypoints[i][2], waypoints[i][3]);
		}
	}
	
	// Print Track
	if (mapData != '')		
	{
		var points = [];
		var bounds = new google.maps.LatLngBounds();
		for(var i in mapData)
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
			var numberFormat1 = "#,##0.#mi";
			var numberFormat2 = "#,###ft";		
		}

		var data = new google.visualization.DataTable();
		data.addColumn('number', loc['length']);		
		data.addColumn('number', loc['altitude']);
		data.addRows(graphData);
		var chart = new google.visualization.AreaChart(el_chart);
		var options = { curveType: "function",
						strictFirstColumnType: true, 
						hAxis : {format : numberFormat1, title : loc['length']},
						vAxis : {format : numberFormat2, title : loc['altitude']},
						legend : {position : 'none'},
						chartArea: {left:70,top:10,width:"100%",height:"75%"},
						colors:[color2]
						};
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
	for(var i in points)
	{
		var d = dist(points[i][0], points[i][1], lat, lon);
		if (d<dd)
		{
			ii=i;
			dd=d;
		}
	}
	return ii;
}

function dist(lat1,lon1,lat2,lon2)
{
	// mathematically not correct but fast
	var dLat = (lat2-lat1);
	var dLon = (lon2-lon1);
	var a = Math.sin(dLat) * Math.sin(dLat) +
			Math.sin(dLon) * Math.sin(dLon) * Math.cos(lat1) * Math.cos(lat2); 
	return Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
}



