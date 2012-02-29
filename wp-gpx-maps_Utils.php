<?php


	function sitePath()
	{
		return substr(substr(__FILE__, 0, strrpos(__FILE__,'wp-content')), 0, -1);
		//		$uploadsPath = 	substr($uploadsPath, 0, -1);
	}

	function gpxFolderPath()
	{
		$upload_dir = wp_upload_dir();
		$uploadsPath = $upload_dir['basedir'];	
		$ret = $uploadsPath.DIRECTORY_SEPARATOR."gpx";
		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $ret);
	}
	
	function gpxCacheFolderPath()
	{
		$upload_dir = wp_upload_dir();
		$uploadsPath = $upload_dir['basedir'];	
		$ret = $uploadsPath.DIRECTORY_SEPARATOR."gpx".DIRECTORY_SEPARATOR."cache";
		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $ret);
	}
	
	function relativeGpxFolderPath()
	{
		$sitePath = sitePath();
		$realGpxPath = gpxFolderPath();
		$ret = str_replace($sitePath,'',$realGpxPath).DIRECTORY_SEPARATOR;
		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $ret);
	}
	
	function recursive_remove_directory($directory, $empty=FALSE)
	{
		if(substr($directory,-1) == '/')
		{
			$directory = substr($directory,0,-1);
		}
		if(!file_exists($directory) || !is_dir($directory))
		{
			return FALSE;
		}elseif(is_readable($directory))
		{
			$handle = opendir($directory);
			while (FALSE !== ($item = readdir($handle)))
			{
				if($item != '.' && $item != '..')
				{
					$path = $directory.'/'.$item;
					if(is_dir($path)) 
					{
						recursive_remove_directory($path);
					}else{
						unlink($path);
					}
				}
			}
			closedir($handle);
			if($empty == FALSE)
			{
				if(!rmdir($directory))
				{
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	function getPoints($gpxPath,$gpxOffset = 10, $donotreducegpx)
	{

		$points = array();
		$dist=0;
		
		$lastLat=0;
		$lastLon=0;
		$lastEle=0;
		$lastOffset=0;
				
		if (file_exists($gpxPath))
		{
			$points = parseXml($gpxPath, $gpxOffset);			
		}
		else
		{
			array_push($points, array((float)0,(float)0,(float)0,(float)0));
			echo "File $gpxPath not found!";
		}
		
		// reduce the points to around 200 to speedup
		if ( $donotreducegpx != true)
		{
			$count=sizeof($points);
			if ($count>200)
			{
				$f = round($count/200);
				if ($f>1)
					for($i=$count;$i>0;$i--)
						if ($i % $f != 0)
							unset($points[$i]);
			}		
		}
		return $points;
	}

	function parseXml($filePath, $gpxOffset)
	{

		$points = array();
		$gpx = simplexml_load_file($filePath);	
		
		if($gpx === FALSE) 
			return;
		
		$gpx->registerXPathNamespace('10', 'http://www.topografix.com/GPX/1/0'); 
		$gpx->registerXPathNamespace('11', 'http://www.topografix.com/GPX/1/1'); 
		$gpx->registerXPathNamespace('gpxx', 'http://www.garmin.com/xmlschemas/GpxExtensions/v3'); 		
		
		$nodes = $gpx->xpath('//trkpt | //10:trkpt | //11:trkpt');
		
		if ( count($nodes) > 0 )	
		{
		
			$lastLat = 0;
			$lastLon = 0;
			$lastEle = 0;
			$lastTime = 0;
			$dist = 0;
			$lastOffset = 0;
			$speedBuffer = array();
		
			// normal case
			foreach($nodes as $trkpt)
			{ 
				$lat = $trkpt['lat'];
				$lon = $trkpt['lon'];
				$ele = $trkpt->ele;
				$time = $trkpt->time;
				$speed = (float)$trkpt->speed;

				if ($lastLat == 0 && $lastLon == 0)
				{
					//Base Case
					array_push($points, array((float)$lat,(float)$lon,(float)round($ele,2),(float)round($dist,2), 0 ));
					$lastLat=$lat;
					$lastLon=$lon;
					$lastEle=$ele;				
					$lastTime=$time;	
				}
				else
				{
					//Normal Case
					$offset = calculateDistance((float)$lat, (float)$lon, (float)$ele, (float)$lastLat, (float)$lastLon, (float)$lastEle);
					$dist = $dist + $offset;
					
					if ($speed == 0)
					{
						$datediff = (float)date_diff($lastTime,$time);
						//echo "------------$time-------$lastTime-----";
						//echo "------------$datediff------------";
						if ($datediff>0)
						{
							$speed = $offset / $datediff;
						}
					}
					
					array_push($speedBuffer, $speed);
					
					if (((float) $offset + (float) $lastOffset) > $gpxOffset)
					{
						//Bigger Offset -> write coordinate
						
						$avgSpeed = 0;
						
						foreach($speedBuffer as $s)
						{ 
							$avgSpeed += $s;
						}
						
						$avgSpeed = $avgSpeed / count($speedBuffer);
						$speedBuffer = array();
						
						$lastOffset=0;
						array_push($points, array(
													(float)$lat,
													(float)$lon,
													(float)round($ele,1),
													(float)round($dist,1), 
													(float)round($avgSpeed,1) 
												)
									);
					}
					else
					{
						//Smoller Offset -> continue..
						$lastOffset = (float) $lastOffset + (float) $offset ;
					}
				}
				$lastLat=$lat;
				$lastLon=$lon;
				$lastEle=$ele;
				$lastTime=$time;	
			}
			unset($nodes);
		
		}
		else
		{
		
			$nodes = $gpx->xpath('//gpxx:rpt');
		
			if ( count($nodes) > 0 )	
			{
			
				$lastLat = 0;
				$lastLon = 0;
				$lastEle = 0;
				$dist = 0;
				$lastOffset = 0;
			
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
				unset($nodes);
			}
			else
			{	
				echo "Empty Gpx or not supported File!";
			}
		}
		unset($gpx);
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
		return (float)($degrees * 3.1415926535897932385 / 180);
	}
	
	function calculateDistance($lat1,$lon1,$ele1,$lat2,$lon2,$ele2)
	{
		$alpha = (float)sin((float)toRadians((float) $lat2 - (float) $lat1) / 2);
		$beta = (float)sin((float)toRadians((float) $lon2 - (float) $lon1) / 2);
		//Distance in meters
		$a = (float) ( (float)$alpha * (float)$alpha) +  (float) ( (float)cos( (float)toRadians($lat1)) * (float)cos( (float)toRadians($lat2)) * (float)$beta * (float)$beta );
		$dist = 2 * 6369628.75 * (float)atan2((float)sqrt((float)$a), (float)sqrt(1 - (float) $a));
		$d = (float)sqrt((float)pow((float)$dist, 2) + pow((float) $lat1 - (float)$lat2, 2));	
		return sqrt((float)pow((float)$ele1-(float)$ele2,2)+(float)pow((float)$d,2));
	}
	
	function date_diff($old_date, $new_date) {
	
		$t1 = strtotime($new_date);
		$t2 = strtotime($old_date);
		
		// milliceconds fix
		$t1 += date_getDecimals($new_date);
		$t2 += date_getDecimals($old_date);
	
		$offset = (float)($t1 - $t2);
	  
		//echo "$offset = $new_date - $old_date; ".strtotime($new_date)." ".strtotime($old_date)." <br />";
	  
	  return $offset;
	}

	function date_getDecimals($date)
	{
		if (preg_match('(\.([0-9]{2})Z?)', $date, $matches))
		{
			return (float)((float)$matches[1] / 100);
		}
		else
		{
			return 0;
		}
	}


	
?>