<?php
	function getPoints($gpxPath,$gpxOffset = 10)
	{
		$points = array();
		$dist=0;
		
		$lastLat=0;
		$lastLon=0;
		$lastEle=0;
		$lastOffset=0;
			
		//Default Offset = 10 mt
		if (!($gpxOffset > 0))
		{
			$gpxOffset = 10;
		}
			
		if (file_exists($gpxPath))
		{
			$points = parseXml($gpxPath, $gpxOffset);			
		}
		else
		{
			array_push($points, array((float)0,(float)0,(float)0,(float)0));
			echo "File $gpxPath not found!";
		}
		// riduco l'array a circa 200 punti per non appensantire la pagina(mappa e grafico)!
		$count=sizeof($points);
		if ($count>200)
		{
			$f = round($count/200);
			if ($f>1)
				for($i=$count;$i>0;$i--)
					if ($i % $f != 0)
						unset($points[$i]);
		}
		return $points;
	}

	function parseXml($filePath, $gpxOffset)
	{

		$points = array();
		$gpx = simplexml_load_file($filePath);	
		$gpx->registerXPathNamespace('10', 'http://www.topografix.com/GPX/1/0'); 
		$gpx->registerXPathNamespace('11', 'http://www.topografix.com/GPX/1/1'); 
		$gpx->registerXPathNamespace('gpxx', 'http://www.garmin.com/xmlschemas/GpxExtensions/v3'); 		
		
		$nodes = $gpx->xpath('//trkpt | //10:trkpt | //11:trkpt');
		
		if ( count($nodes) > 0 )	
		{
			// normal case
			foreach($nodes as $trkpt)
			{ 
				$lat = $trkpt['lat'];
				$lon = $trkpt['lon'];
				$ele = $trkpt->ele;
				if ($lastLat == 0 && $lastLon == 0)
				{
					//Base Case
					array_push($points, array((float)$lat,(float)$lon,(float)round($ele,1),(float)round($dist,1)));
					$lastLat=$lat;
					$lastLon=$lon;
					$lastEle=$ele;				
				}
				else
				{
					//Normal Case
					$offset = calculateDistance($lat, $lon, $ele,$lastLat, $lastLon, $lastEle);
					$dist = $dist + $offset;
					if (((float) $offset + (float) $lastOffset) > $gpxOffset)
					{
						//Bigger Offset -> write coordinate
						$lastOffset=0;
						array_push($points, array((float)$lat,(float)$lon,(float)round($ele,1),(float)round($dist,1)));
					}
					else
					{
						//Smoller Offset -> continue..
						$lastOffset= (float) $lastOffset + (float) $offset ;
					}
				}
				$lastLat=$lat;
				$lastLon=$lon;
				$lastEle=$ele;
			}
		
		}
		else
		{
		
			$nodes = $gpx->xpath('//gpxx:rpt');
		
			if ( count($nodes) > 0 )	
			{
			
				// Garmin case
				foreach($nodes as $rpt)
				{ 
				
					$lat = $rpt['lat'];
					$lon = $rpt['lon'];
					if ($lastLat == 0 && $lastLon == 0)
					{
						//Base Case
						array_push($points, array((float)$lat,(float)$lon,null,null));
						$lastLat=$lat;
						$lastLon=$lon;
					}
					else
					{
						//Normal Case
						$offset = calculateDistance($lat, $lon, 0,$lastLat, $lastLon, 0);
						$dist = $dist + $offset;
						if (((float) $offset + (float) $lastOffset) > $gpxOffset)
						{
							//Bigger Offset -> write coordinate
							$lastOffset=0;
							array_push($points, array((float)$lat,(float)$lon,null,null));
						}
						else
						{
							//Smoller Offset -> continue..
							$lastOffset= (float) $lastOffset + (float) $offset;
						}
					}
					$lastLat=$lat;
					$lastLon=$lon;
				}
				
			}
			else
			{	
				echo "Gpx Empty or not supported!";
			}
		}
		return $points;
	}	
	
	function getWayPoints($gpxPath)
	{
		$points = array();
		if (file_exists($gpxPath))
		{
			$points = array();
			$gpx = simplexml_load_file($gpxPath);	
			$gpx->registerXPathNamespace('10', 'http://www.topografix.com/GPX/1/0'); 
			$gpx->registerXPathNamespace('11', 'http://www.topografix.com/GPX/1/1'); 
			$nodes = $gpx->xpath('//wpt | //10:wpt | //11:wpt');
			
			if ( count($nodes) > 0 )	
			{
				// normal case
				foreach($nodes as $wpt)
				{
					$lat = $wpt['lat'];
					$lon = $wpt['lon'];
					$ele = $wpt->ele;
					$time = $wpt->time;
					$name = $wpt->name;
					$desc = $wpt->desc;
					$sym = $wpt->sym;
					$type = $wpt->type;
					array_push($points, array((float)$lat,(float)$lon,(float)$ele,$time,$name,$desc,$sym,$type));
				}
			}
		}
		return $points;
	}
	
	function toRadians($degrees)
	{
		return $degrees * 3.1415926535897932385 / 180;
	}
	
	function calculateDistance($lat1,$lon1,$ele1,$lat2,$lon2,$ele2)
	{
		//Distance in meters
		$dLat = toRadians((float) $lat2 - (float) $lat1);
		$dLng = toRadians((float) $lon2 - (float) $lon1);
		$a = (float) ( sin($dLat / 2) * sin($dLat / 2)) +  (float) ( cos( toRadians($lat1)) * cos( toRadians($lat2)) * sin($dLng / 2) * sin($dLng / 2) );
		$dist = 2 * 3958.75 * atan2(sqrt($a), sqrt(1 - (float) $a));
		return sqrt(pow($dist * 1609.00, 2) + pow((float) $lat1 - (float)$lat2, 2));	
	}

	
?>