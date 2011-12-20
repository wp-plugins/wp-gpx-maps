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

function sleep(ms)
{
	var dt = new Date();
	dt.setTime(dt.getTime() + ms);
	while (new Date().getTime() < dt.getTime());
}

function wpgpxmaps(targhetId,mapType,mapData,graphData)
{
	var i = 0;
	while ((google == undefined || google.maps == undefined || google.visualization == undefined))
	{
		sleep(100);
		i++;
		if (i== 100)
		{
			return;
		}
	}
	_wpgpxmaps(targhetId,mapType,mapData,graphData);
}


function _wpgpxmaps(targhetId,mapType,mapData,graphData)
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
	if (graphData!= '')
	{
		var data = new google.visualization.DataTable();
		data.addColumn('number', loc['length']);		
		data.addColumn('number', loc['altitude']);
		data.addRows(graphData);
		var chart = new google.visualization.AreaChart(el_chart);
		var options = { curveType: "function",
						strictFirstColumnType: true, 
						hAxis : {format : '#,###m', title : loc['length']},
						vAxis : {format : '#,###m', title : loc['altitude']},
						legend : {position : 'none'},
						chartArea: {left:70,top:10,width:"100%",height:"75%"}
						};
		chart.draw(data, options);
		google.visualization.events.addListener(chart, 'onmouseover', function (e) {
			var r = e['row'];
			if (marker)
			{
				var point = getItemFromArray(mapData,r)
				marker.setPosition(new google.maps.LatLng(point[0],point[1]));					marker.setTitle("Current Position");
			}
		});
		google.visualization.events.addListener(chart, 'onmouseout', function (e) {
			if (marker)
			{
				if (chart.getSelection() != '')
				{
					var r = chart.getSelection()[0]['row'];
					var point = getItemFromArray(mapData,r)
					marker.setPosition(new google.maps.LatLng(point[0], point[1]));					marker.setTitle("Graph Selection");
				}
			}
		});
	}	else	{		el_chart.style.display='none';	}
	if (mapData != '')		{
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
			strokeColor: "#3366cc",
			strokeOpacity: .7,
			strokeWeight: 4
		});
		poly.setMap(map);
		map.setCenter(bounds.getCenter()); 
		map.fitBounds(bounds);
		var first = getItemFromArray(mapData,0)
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(first[0], first[1]),
			title:"Start"
		});
		marker.setMap(map);
	}
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
