/*

	WP-GPX-Maps

*/

var t;
var funqueue = [];
var infowindow;
var mapLoading = false;
var CustomMarker;

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
	if ((google == undefined || google.maps == undefined || Highcharts == undefined))
	{
		t = setTimeout("unqueue()",200);
	}
	else
	{
		setup();
		while (funqueue.length > 0) {
			(funqueue.shift())();   
		}
	}
}

function setup()
{

	CustomMarker = function( map, latlng, src, img_w, img_h) {
		this.latlng_ = latlng;

		this.setMap(map);
		this.src_ = src;
		this.img_w_ = img_w;
		this.img_h_ = img_h;
	}

	CustomMarker.prototype = new google.maps.OverlayView();

	CustomMarker.prototype.draw = function() {
	
		var me = this;

		// Check if the el has been created.
		var el = this.img_;
		if (!el) {

			this.img_ = document.createElement('img');
			el = this.img_;
			el.style.cssText = "width:"+(this.img_w_/3)+"px;height:"+(this.img_h_/3)+"px;";
			el.setAttribute("class", "myngimages");
			el.setAttribute("lat",this.latlng_.lat());
			el.setAttribute("lon",this.latlng_.lng());
			el.src=this.src_;

			google.maps.event.addDomListener(el, "click", function(event) {
				google.maps.event.trigger(me, "click", el);
			});	
			
			google.maps.event.addDomListener(el, "mouseover", function(event) {
				var _t = el.style.top.replace('px','');
				var _l = el.style.left.replace('px','');
				jQuery(el).animate({
					height: me.img_h_,
					width : me.img_w_,
					top   : _t - (me.img_h_ / 3),
					left  : _l - (me.img_w_ / 3),
					'z-index' : 9999
				  }, 100);
			});

			google.maps.event.addDomListener(el, "mouseout", function(event) {
				jQuery(el).animate({
					height: me.img_h_ / 3,
					width: me.img_w_ / 3,
					top   : me.orig_top,
					left  : me.orig_left,
					'z-index' : 1
				  }, 100);
			});	

			// Then add the overlay to the DOM
			var panes = this.getPanes();
			panes.overlayImage.appendChild(el);
		}

		// Position the overlay 
		var point = this.getProjection().fromLatLngToDivPixel(this.latlng_);
			if (point) {
			  el.style.left = point.x + 'px';
			  el.style.top = point.y + 'px';
			  this.orig_left = point.x;
			  this.orig_top = point.y;
			}
	};

	CustomMarker.prototype.remove = function() {
		// Check if the overlay was on the map and needs to be removed.
		if (this.img_) {
		  this.img_.parentNode.removeChild(this.img_);
		  this.img_ = null;
		}
	};

}

function _wpgpxmaps(params)
{

	var targetId = params.targetId;
	var mapType = params.mapType;
	var mapData = params.mapData;
	var graphDist = params.graphDist;
	var graphEle = params.graphEle;
	var graphSpeed = params.graphSpeed;
	var graphHr = params.graphHr;
	var graphCad = params.graphCad;
	var waypoints = params.waypoints;
	var unit = params.unit;
	var unitspeed = params.unitspeed;
	var color1 = params.color1;
	var color2 = params.color2;
	var color3 = params.color3;
	var color4 = params.color4;
	var color5 = params.color5;
	var chartFrom1 = params.chartFrom1;
	var chartTo1 = params.chartTo1;
	var chartFrom2 = params.chartFrom2;
	var chartTo2 = params.chartTo2;
	var startIcon = params.startIcon;
	var endIcon = params.endIcon;
	var currentIcon = params.currentIcon;
	var zoomOnScrollWheel = params.zoomOnScrollWheel;
	
	var el = document.getElementById("wpgpxmaps_" + targetId);
	var el_map = document.getElementById("map_" + targetId);
	var el_chart = document.getElementById("chart_" + targetId);
	
	var mapWidth = el_map.style.width;
	
	var mapTypeIds = [];
	for(var type in google.maps.MapTypeId) {
		mapTypeIds.push(google.maps.MapTypeId[type]);
	}
	mapTypeIds.push("OSM1");
	mapTypeIds.push("OSM2");
	mapTypeIds.push("OSM3");
	
	switch (mapType)
	{
		case 'TERRAIN': { mapType = google.maps.MapTypeId.TERRAIN; break;}
		case 'SATELLITE': { mapType = google.maps.MapTypeId.SATELLITE; break;}
		case 'ROADMAP': { mapType = google.maps.MapTypeId.ROADMAP; break;}
		case 'OSM1': { mapType = "OSM1"; break;}
		case 'OSM2': { mapType = "OSM2"; break;}
		case 'OSM3': { mapType = "OSM3"; break;}
		default: { mapType = google.maps.MapTypeId.HYBRID; break;}
	}

	var map = new google.maps.Map(el_map, {
		mapTypeId: mapType,
		scrollwheel: (zoomOnScrollWheel == 'true'),
		mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
			mapTypeIds: mapTypeIds
		}
	}); 
										
	map.mapTypes.set("OSM1", new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			return "http://tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
		tileSize: new google.maps.Size(256, 256),
		name: "Open Street Map",
		maxZoom: 18
	}));
	
	map.mapTypes.set("OSM2", new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			return "http://a.tile.opencyclemap.org/cycle/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
		tileSize: new google.maps.Size(256, 256),
		name: "Open Cycle Map",
		maxZoom: 18
	}));
	
	map.mapTypes.set("OSM3", new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			return "http://toolserver.org/tiles/hikebike/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
		tileSize: new google.maps.Size(256, 256),
		name: "Hike & Bike",
		maxZoom: 18
	}));

	var bounds = new google.maps.LatLngBounds();
	
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
	
	// Print Images
	
	var divImages = document.getElementById("ngimages_"+targetId);
	
	divImages.style.display='block';	
	divImages.style.position='absolute';
	divImages.style.left='-50000px';	
	
	var img_spans = divImages.getElementsByTagName("span");   

	if (img_spans.length > 0)
	{
		var bb = new google.maps.LatLngBounds();
		for (var i = 0; i < img_spans.length; i++) {   
		
			var imageLat  = img_spans[i].getAttribute("lat");
			var imageLon  = img_spans[i].getAttribute("lon");	
			var imageImg  = img_spans[i].getElementsByTagName('img')[0];
			var imageUrl  = imageImg.getAttribute("src");
			
			var img_w = imageImg.clientWidth;
			var img_h = imageImg.clientHeight;
			
			var p = new google.maps.LatLng(imageLat, imageLon);
			bounds.extend(p);

			var mc = new CustomMarker(map, p, imageUrl, img_w, img_h );
			
			google.maps.event.addListener(mc, "click", function(div) {
				var lat = div.getAttribute("lat");
				var lon = div.getAttribute("lon");
				var a = getClosestImage(lat,lon,targetId).childNodes[0];			
				if (a)
				{
					a.click();
				}
			});
			
		}  

	}
	
	// Print Track
	if (mapData != '')		
	{
		var points = [];

		for (i=0; i < mapData.length; i++) 
		{
			var p = new google.maps.LatLng(mapData[i][0], mapData[i][1]);
			points.push(p);
			bounds.extend(p);
		}
		
		if (startIcon != '')
		{
			var startIconImage = new google.maps.MarkerImage(startIcon);
			var startMarker = new google.maps.Marker({
					  position: points[0],
					  map: map,
					  title: "Start",
					  animation: google.maps.Animation.DROP,
					  icon: startIconImage,
					  zIndex: 10
				  });
		}

		if (endIcon != '')
		{
			var endIconImage = new google.maps.MarkerImage(endIcon);
			var startMarker = new google.maps.Marker({
					  position: points[ points.length -1 ],
					  map: map,
					  title: "Start",
					  animation: google.maps.Animation.DROP,
					  icon: endIconImage,
					  zIndex: 10
				  });
		
		}
		
		var poly = new google.maps.Polyline({
			path: points,
			strokeColor: color1,
			strokeOpacity: .7,
			strokeWeight: 4
		});
		poly.setMap(map);

		var first = getItemFromArray(mapData,0)
		
		if (currentIcon == '')
		{
			currentIcon = "http://maps.google.com/mapfiles/kml/pal4/icon25.png";
		}
		
		var current = new google.maps.MarkerImage(currentIcon,
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
				if (hchart)
				{
					var tooltip = hchart.tooltip;
					var l1 = event.latLng.lat();
					var l2 = event.latLng.lng();
					var ci = getClosestIndex(mapData,l1,l2);
					var items = [];
					var seriesLen = hchart.series.length;
					for(var i=0; i<seriesLen;i++)
					{
						items.push(hchart.series[i].data[ci]);
					}
					if (items.length > 0)
						tooltip.refresh(items);
				}
			}
		});
	}
	
	map.setCenter(bounds.getCenter()); 
	map.fitBounds(bounds);
	
	if (graphDist != '' && (graphEle != '' || graphSpeed != '' || graphHr != '' || graphCad != ''))
	{

		var valLen = graphDist.length;
	
		var l_x;
		var l_y;
		var l_y_arr = [];
		
		if (unit=="1")
		{
			l_x = { suf : "mi", dec : 1 };
			l_y = { suf : "ft", dec : 0 };
		}
		else if (unit=="2")
		{
			l_x = { suf : "km", dec : 1 };
			l_y = { suf : "m", dec : 0 };
		}
		else
		{
			l_x = { suf : "m", dec : 0 };
			l_y = { suf : "m", dec : 0 };
		}
		

		// define the options
		var hoptions = {
			chart: {
				renderTo: 'hchart_' + params.targetId,
				type: 'area'
			},
			title: {
				text: null
			},
			xAxis: {
				type: 'integer',
				gridLineWidth: 1,
				tickInterval: 100,
				labels: {
					align: 'left',
					x: 3,
					y: -3
				}
			},
			legend: {
				align: 'center',
				verticalAlign: 'top',
				y: -5,
				floating: true,
				borderWidth: 0
			},
			tooltip: {
				shared: true,
				crosshairs: true,
				formatter: function() {
				
					if (marker)
					{
						var hchart_xserie = hchart.xAxis[0].series[0].data;				
						for(var i=0; i<hchart_xserie.length;i++){
							var item = hchart_xserie[i];
							if(item.x == this.x)
							{
								var point = getItemFromArray(mapData,i)
								marker.setPosition(new google.maps.LatLng(point[0],point[1]));	
								marker.setTitle("Current Position");
								i+=10000000;
							}
						}			
					}
				
					var tooltip = "<b>" + Highcharts.numberFormat(this.x, l_x.dec) + l_x.suf + "</b><br />"; 
					for (i=0; i < this.points.length; i++)
					{
						tooltip += this.points[i].series.name + ": " + Highcharts.numberFormat(this.points[i].y, l_y_arr[i].dec) + l_y_arr[i].suf + "<br />"; 					
					}
					return tooltip;
				}
			},
			plotOptions: {
				area: {
					fillOpacity: 0.1,
					connectNulls : true,
					marker: {
						enabled: false,
						symbol: 'circle',
						radius: 2,
						states: {
							hover: {
								enabled: true
							}
						}
					}					
				}
			},
			xAxis: { 	
					labels: {
							formatter: function() {
								return Highcharts.numberFormat(this.value, l_x.dec) + l_x.suf;
							}
						} 
					},
			credits: {
				enabled: false
			},
			yAxis: [],			
			series: []
		};
	
		if (graphEle != '')
		{
			
			var eleData = [];
		
			for (i=0; i<valLen; i++) 
			{
				eleData.push([graphDist[i],graphEle[i]]);
			}

			var yaxe = { 
				title: { text: null },
				labels: {
					align: 'left',
					formatter: function() {
						return Highcharts.numberFormat(this.value, l_y.dec) + l_y.suf;
					}
				}
			}
						
			if ( chartFrom1 != '' )
			{
				yaxe.min = chartFrom1;
				yaxe.startOnTick = false;
			}
			
			if ( chartTo1 != '' )
			{
				yaxe.max = chartTo1;
				yaxe.endOnTick = false;
			}
								
			hoptions.yAxis.push(yaxe);
			hoptions.series.push({
									name: 'Altitude',
									lineWidth: 1,
									marker: { radius: 0 },
									data : eleData,
									color: color2,
									yAxis: hoptions.series.length
								});			
			
			l_y_arr.push(l_y);
		}
		
		if (graphSpeed != '')
		{
			
			var l_s;
			
			if (unitspeed == '2') // miles/h
			{
				l_s = { suf : "mi/h", dec : 0 };
			} 
			else if (unitspeed == '1') // km/h
			{
				l_s = { suf : "km/h", dec : 0 };
			} 
			else
			{
				l_s = { suf : "m/s", dec : 0 };
			}
			
			var speedData = [];
		
			for (i=0; i<valLen; i++) 
			{
				speedData.push([graphDist[i],graphSpeed[i]]);
			}

			var yaxe = { 
				title: { text: null },
				labels: {
					//align: 'right',
					formatter: function() {
						return Highcharts.numberFormat(this.value, l_s.dec) + l_s.suf;
					}
				},
				opposite: true
			}
						
			if ( chartFrom2 != '' )
			{
				yaxe.min = chartFrom2;
				yaxe.startOnTick = false;				
			}
			
			if ( chartTo2 != '' )
			{
				yaxe.max = chartTo2;
				yaxe.endOnTick = false;				
			}
								
			hoptions.yAxis.push(yaxe);
			hoptions.series.push({
									name: 'Speed',
									lineWidth: 1,
									marker: { radius: 0 },
									data : speedData,
									color: color3,
									yAxis: hoptions.series.length
								});			
			
			l_y_arr.push(l_s);
		}
		
		if (graphHr != '')
		{
			
			var l_hr = { suf : "", dec : 0 };
			
			var hrData = [];
		
			for (i=0; i<valLen; i++) 
			{
				var c = graphHr[i];
				if (c==0)
					c = null;
				hrData.push([graphDist[i],c]);
			}

			var yaxe = { 
				title: { text: null },
				labels: {
					//align: 'right',
					formatter: function() {
						return Highcharts.numberFormat(this.value, l_hr.dec) + l_hr.suf;
					}
				},
				opposite: true
			}
								
			hoptions.yAxis.push(yaxe);
			hoptions.series.push({
									name: 'Heart rate',
									lineWidth: 1,
									marker: { radius: 0 },
									data : hrData,
									color: color4,
									yAxis: hoptions.series.length
								});			
			
			l_y_arr.push(l_hr);
		}
		
		if (graphCad != '')
		{
			
			var l_cad = { suf : "", dec : 0 };
			
			var cadData = [];
		
			for (i=0; i<valLen; i++) 
			{
				var c = graphCad[i];
				if (c==0)
					c = null;
				cadData.push([graphDist[i],c]);
			}

			var yaxe = { 
				title: { text: null },
				labels: {
					//align: 'right',
					formatter: function() {
						return Highcharts.numberFormat(this.value, l_cad.dec) + l_cad.suf;
					}
				},
				opposite: true
			}
								
			hoptions.yAxis.push(yaxe);
			hoptions.series.push({
									name: 'Cadence',
									lineWidth: 1,
									marker: { radius: 0 },
									data : cadData,
									color: color5,
									yAxis: hoptions.series.length
								});			
			
			l_y_arr.push(l_cad);
		}
		
		var hchart = new Highcharts.Chart(hoptions);
	
	}
	else  {
		jQuery("#hchart_" + params.targetId).css("display","none");
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
		var cnt = '';	
		if (title=='')
		{
			cnt = "<center>" + descr + "</center>";
		}
		else
		{
			cnt = "<b>" + title + "</b></br />" + descr;
		}
		infowindow = new google.maps.InfoWindow({ content: cnt});
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

function getClosestImage(lat,lon,targetId)
{
	var dd=10000;
	var img;
	var divImages = document.getElementById("ngimages_"+targetId);
	var img_spans = divImages.getElementsByTagName("span");   
	for (var i = 0; i < img_spans.length; i++) {   
		var imageLat = img_spans[i].getAttribute("lat");
		var imageLon = img_spans[i].getAttribute("lon");	
		var d = dist(imageLat, imageLon, lat, lon);
		if ( d < dd )
		{
			img = img_spans[i];
			dd = d;
		}		
	}
	return img;
}

function isNumeric(input){
    var RE = /^-{0,1}\d*\.{0,1}\d+$/;
    return (RE.test(input));
}

function dist(lat1,lon1,lat2,lon2)
{
	// mathematically not correct but fast
	var dLat = (lat2-lat1);
	var dLon = (lon2-lon1);
	return Math.sqrt(dLat * dLat + dLon * dLon);
}