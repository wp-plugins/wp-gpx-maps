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



  function CustomMarker( map, latlng, src, img_w, img_h) {
    this.latlng_ = latlng;

    // Once the LatLng and text are set, add the overlay to the map.  This will
    // trigger a call to panes_changed which should in turn call draw.
    this.setMap(map);
	this.src_ = src;
	this.img_w_ = img_w;
	this.img_h_ = img_h;
  }

  CustomMarker.prototype = new google.maps.OverlayView();

  CustomMarker.prototype.draw = function() {
    var me = this;

    // Check if the div has been created.
    var div = this.div_;
    if (!div) {
      // Create a overlay text DIV
      div = this.div_ = document.createElement('DIV');
	  div.style.cssText = "border:1px solid #fff;position:absolute;cursor:pointer;margin:0;background:url('"+this.src_+"') center;width:"+(this.img_w_/3)+"px;height:"+(this.img_h_/3)+"px;";
	  div.setAttribute("lat",this.latlng_.lat());
	  div.setAttribute("lon",this.latlng_.lng());
      google.maps.event.addDomListener(div, "click", function(event) {
        google.maps.event.trigger(me, "click",div);
      });

		google.maps.event.addDomListener(div, "mouseover", function(event) {
		
			var _t = div.style.top.replace('px','');
			var _l = div.style.left.replace('px','');
		
			jQuery(div).animate({
				height: me.img_h_,
				width : me.img_w_,
				top   : _t - (me.img_h_ / 3),
				left  : _l - (me.img_w_ / 3)
			  }, 100);
		});

		google.maps.event.addDomListener(div, "mouseout", function(event) {
			jQuery(div).animate({
				height: me.img_h_ / 3,
				width: me.img_w_ / 3,
				top   : me.orig_top,
				left  : me.orig_left
			  }, 100);
		});

      // Then add the overlay to the DOM
      var panes = this.getPanes();
      panes.overlayImage.appendChild(div);
    }

    // Position the overlay 
    var point = this.getProjection().fromLatLngToDivPixel(this.latlng_);
    if (point) {
      div.style.left = point.x + 'px';
      div.style.top = point.y + 'px';
	  
	  this.orig_left = point.x;
	  this.orig_top = point.y;
	  
    }
  };

  CustomMarker.prototype.remove = function() {
    // Check if the overlay was on the map and needs to be removed.
    if (this.div_) {
      this.div_.parentNode.removeChild(this.div_);
      this.div_ = null;
    }
  };





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
	var chartFrom1 = params.chartFrom1;
	var chartTo1 = params.chartTo1;
	var chartFrom2 = params.chartFrom2;
	var chartTo2 = params.chartTo2;
	var startIcon = params.startIcon;
	var endIcon = params.endIcon;
	var currentIcon = params.currentIcon;
	
	var el = document.getElementById("wpgpxmaps_" + targetId);
	var el_map = document.getElementById("map_" + targetId);
	var el_chart = document.getElementById("chart_" + targetId);
	
	var mapWidth = el_map.style.width;
	
	switch (mapType)
	{
		case 'TERRAIN': { mapType = google.maps.MapTypeId.TERRAIN; break;}
		case 'SATELLITE': { mapType = google.maps.MapTypeId.SATELLITE; break;}
		case 'ROADMAP': { mapType = google.maps.MapTypeId.ROADMAP; break;}
		default: { mapType = google.maps.MapTypeId.HYBRID; break;}
	}
	
	var mapOptions = {
		mapTypeId: mapType,
		scrollwheel: false
	};
	var map = new google.maps.Map(el_map, mapOptions); 
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
	divImages.style.left='-500px';	
	
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
				if ( chart )
				{
					var l1 = event.latLng.lat();
					var l2 = event.latLng.lng();					
					var ci = getClosestIndex(mapData,l1,l2);
					var r = chart.setSelection([{'row': parseInt(ci) + 1}]);
				}
			}
		});
	}
	
	map.setCenter(bounds.getCenter()); 
	map.fitBounds(bounds);
	
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
		else if (unit=="2")
		{
			numberFormat1 = "#,###0.#km";
			numberFormat2 = "#,###m";
		}
		
		var showSpeed = (graphData[0].length == 3);

		var data = new google.visualization.DataTable();
		data.addColumn('number', "Distance");		
		data.addColumn('number', "Altitude");

		if (!isNumeric(chartFrom1))
			chartFrom1 = null;
			
		if (!isNumeric(chartTo1))
			chartTo1 = null;
		
		if (!isNumeric(chartFrom2))
			chartFrom2 = null;

		if (!isNumeric(chartTo2))
			chartTo2 = null;		
			
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

		if ( chartFrom1 != null || chartTo1 != null )
		{
			options.vAxis.viewWindowMode = "explicit";
			options.vAxis.viewWindow = { min : chartFrom1, max : chartTo1};
		}
						
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
							
			if ( chartFrom1 != null || chartTo1 != null )
			{
				options.vAxes[0].viewWindowMode = "explicit";
				options.vAxes[0].viewWindow = { min : chartFrom1, max : chartTo1};
			}
			
			if ( chartFrom2 != null || chartTo2 != null )
			{
				options.vAxes[1].viewWindowMode = "explicit";
				options.vAxes[1].viewWindow = { min : chartFrom2, max : chartTo2};
			}							
							
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

		if( mapWidth = "100%")
		{
			var resizeChart = function(){
				el_chart.style.width = el_chart.clientWidth + "px";
				chart.draw(data, options);
			};
			google.maps.event.addListener(map, "idle", resizeChart);
		}

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