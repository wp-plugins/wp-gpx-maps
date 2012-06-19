<?php

	function isNGGalleryActive() {
		if (!function_exists('is_plugin_active')) {
			require_once(sitePath() . '/wp-admin/includes/plugin.php');
		}		
		return is_plugin_active("nextgen-gallery/nggallery.php");
	}
	
	function getNGGalleryImages($ngGalleries, $ngImages, &$error)
	{

		$result = array();
		$galids = explode(',', $ngGalleries);
		$imgids = explode(',', $ngImages);
		
		if (!isNGGalleryActive())
			return '';
			
		try {

			$pictures = array();
			foreach ($galids as $g) {						
				$pictures = array_merge($pictures, nggdb::get_gallery($g));
			}
			foreach ($imgids as $i) {
				array_push($pictures, nggdb::find_image($i));
			}
			foreach ($pictures as $p) {
				$item = array();
				$item["data"] = $p->thumbHTML;
				if (is_callable('exif_read_data'))
				{
					$exif = @exif_read_data($p->imagePath);	
					if ($exif !== false)
					{
						$item["lon"] = getExifGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
						$item["lat"] = getExifGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
						if (($item["lat"] != 0) || ($item["lon"] != 0)) 
						{
							$result[] = $item;
						}					
					}
				}
				else
				{
					$error .= "Sorry, <a href='http://php.net/manual/en/function.exif-read-data.php' target='_blank' >exif_read_data</a> function not found! check your hosting..<br />";
				}
			}			
			
		} catch (Exception $e) {
			$error .= 'Error When Retrieving NextGen Gallery galleries/images: $e <br />';
		}

		return $result;
	}
	
	function getExifGps($exifCoord, $hemi) 
	{
		$degrees = count($exifCoord) > 0 ? gps2Num($exifCoord[0]) : 0;
		$minutes = count($exifCoord) > 1 ? gps2Num($exifCoord[1]) : 0;
		$seconds = count($exifCoord) > 2 ? gps2Num($exifCoord[2]) : 0;
		$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
		return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
	}

	function gps2Num($coordPart) 
	{
		$parts = explode('/', $coordPart);
		if (count($parts) <= 0)
			return 0;
		if (count($parts) == 1)
			return $parts[0];
		return floatval($parts[0]) / floatval($parts[1]);
	}

?>