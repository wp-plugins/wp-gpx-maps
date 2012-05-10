<?php
/*
Plugin Name: WP-GPX-Maps
Plugin URI: http://www.darwinner.it/
Description: Draws a gpx track with altitude graph
Version: 1.1.18
Author: Bastianon Massimo
Author URI: http://www.pedemontanadelgrappa.it/
License: GPL
*/

//error_reporting (E_ALL);

include 'wp-gpx-maps_utils.php';
include 'wp-gpx-maps_admin.php';

add_action( 'wp_print_scripts', 'print_WP_GPX_Maps_scripts' );
add_shortcode('sgpx','handle_WP_GPX_Maps_Shortcodes');
register_activation_hook(__FILE__,'WP_GPX_Maps_install'); 
register_deactivation_hook( __FILE__, 'WP_GPX_Maps_remove');	
add_filter('plugin_action_links', 'WP_GPX_Maps_action_links', 10, 2);
add_action('wp_enqueue_scripts', 'enqueue_WP_GPX_Maps_scripts');

function WP_GPX_Maps_action_links($links, $file) {
    static $this_plugin;
 
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
 
    // check to make sure we are on the correct plugin
    if ($file == $this_plugin) {
        // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=WP-GPX-Maps">Settings</a>';
        // add the link to the list
        array_unshift($links, $settings_link);
    }
 
    return $links;
}

function enqueue_WP_GPX_Maps_scripts()
{
    wp_deregister_script( 'WP-GPX-Maps' );
    wp_register_script( 'WP-GPX-Maps', plugins_url('/WP-GPX-Maps.js', __FILE__), null, "1.1.15");
    wp_enqueue_script( 'WP-GPX-Maps' );
    wp_deregister_script( 'highcharts' );
    wp_register_script( 'highcharts', plugins_url('/highcharts.js', __FILE__), null, "2.2.1", true);
    wp_enqueue_script( 'highcharts' );
}

function print_WP_GPX_Maps_scripts()
{
?>
	<script type='text/javascript' src='https://www.google.com/jsapi?ver=3.2.1'></script>
	<script type='text/javascript'>
		google.load("maps", "3", {other_params: 'sensor=false'});
	</script>
	<style type="text/css">
		.wpgpxmaps { clear:both; }
		.entry-content .wpgpxmaps img,
		.wpgpxmaps img { max-width: none; width: none; }
		.wpgpxmaps .ngimages { display:none; }
	</style>
<?php
}

function findValue($attr, $attributeName, $optionName, $defaultValue)
{
	$val = '';
	if ( isset($attr[$attributeName]) )
	{
		$val = $attr[$attributeName];
	}
	if ($val == '')
	{
		$val = get_option($optionName);
	}
	if ($val == '')
	{
		$val = $defaultValue;
	}
	return $val;
}

function handle_WP_GPX_Maps_Shortcodes($attr, $content='')
{

	$error = '';

	$gpx =                findValue($attr, "gpx",                "",                          		 "");
	$w =                  findValue($attr, "width",              "wpgpxmaps_width",           		 "100%");
	$mh =                 findValue($attr, "mheight",            "wpgpxmaps_height",          		 "450px");
	$mt =                 findValue($attr, "mtype",              "wpgpxmaps_map_type",        		 "HYBRID");
	$gh =                 findValue($attr, "gheight",            "wpgpxmaps_graph_height",    		 "200px");
	$showCad =            findValue($attr, "showcad",            "wpgpxmaps_show_cadence",   		 false);
	$showHr =             findValue($attr, "showhr",             "wpgpxmaps_show_hr",   		 	 false);
	$showW =              findValue($attr, "waypoints",          "wpgpxmaps_show_waypoint",   		 false);
	$showSpeed =          findValue($attr, "showspeed",          "wpgpxmaps_show_speed",      		 false);
	$donotreducegpx =     findValue($attr, "donotreducegpx",     "wpgpxmaps_donotreducegpx", 		 false);
	$pointsoffset =       findValue($attr, "pointsoffset",       "wpgpxmaps_pointsoffset",     		 10);
	$uom =                findValue($attr, "uom",                "wpgpxmaps_unit_of_measure",        "0");
	$uomspeed =           findValue($attr, "uomspeed",           "wpgpxmaps_unit_of_measure_speed",  "0");
	$color_map =          findValue($attr, "mlinecolor",         "wpgpxmaps_map_line_color",         "#3366cc");
	$color_graph =        findValue($attr, "glinecolor",         "wpgpxmaps_graph_line_color",       "#3366cc");
	$color_graph_speed =  findValue($attr, "glinecolorspeed",    "wpgpxmaps_graph_line_color_speed", "#ff0000");
	$color_graph_hr =  	  findValue($attr, "glinecolorhr",       "wpgpxmaps_graph_line_color_hr",    "#ff77bd");
	$color_graph_cad =    findValue($attr, "glinecolorcad",      "wpgpxmaps_graph_line_color_cad",   "#beecff");
	$chartFrom1 =         findValue($attr, "chartfrom1",         "wpgpxmaps_graph_offset_from1",     "");
	$chartTo1 =           findValue($attr, "chartfo1",           "wpgpxmaps_graph_offset_to1",       "");
	$chartFrom2 =         findValue($attr, "chartfrom2",         "wpgpxmaps_graph_offset_from2", 	 "");
	$chartTo2 =           findValue($attr, "chartto2",           "wpgpxmaps_graph_offset_to2", 		 "");
	$startIcon =          findValue($attr, "starticon",          "wpgpxmaps_map_start_icon", 		 "");
	$endIcon =            findValue($attr, "endicon",            "wpgpxmaps_map_end_icon", 			 "");
	$currentIcon =        findValue($attr, "currenticon",        "wpgpxmaps_map_current_icon", 		 "");
	$ngGalleries =        findValue($attr, "nggalleries",        "wpgpxmaps_map_ngGalleries", 		 "");
	$ngImages =           findValue($attr, "ngimages",           "wpgpxmaps_map_ngImages", 		     "");

	$r = rand(1,5000000);
	
	$cacheFileName = "$gpx,$w,$mh,$mt,$gh,$showW,$showHr,$showCad,$donotreducegpx,$pointsoffset,$showSpeed,$uom,v1.1.16";

	$cacheFileName = md5($cacheFileName);
	
	$gpxcache = gpxCacheFolderPath();
	
	if(!(file_exists($gpxcache) && is_dir($gpxcache)))
		@mkdir($gpxcache,0755,true);
	
	$gpxcache.= DIRECTORY_SEPARATOR.$cacheFileName.".tmp";
	
	// Try to load cache
	if (file_exists($gpxcache))
	{
		try {
			$cache_str = file_get_contents($gpxcache);
			$cache_obj = unserialize($cache_str);
			$points_maps = $cache_obj["points_maps"];
			$points_graph_dist = $cache_obj["points_graph_dist"];
			$points_graph_ele = $cache_obj["points_graph_ele"];
			$points_graph_speed = $cache_obj["points_graph_speed"];
			$points_graph_hr = $cache_obj["points_graph_hr"];
			$points_graph_cad = $cache_obj["points_graph_cad"];
			$waypoints = $cache_obj["waypoints"];
		} catch (Exception $e) {
			$points_maps= '';
			$points_graph_dist = '';
			$points_graph_ele = '';
			$points_graph_speed = '';
			$points_graph_hr = '';
			$points_graph_cad = '';
			$waypoints= '';
		}
	}

	if ($points_maps == '' && $gpx != '')
	{
	
		$sitePath = sitePath();
		
		$gpx = trim($gpx);
		
		if (strpos($gpx, "http://") !== 0)
		{
			$gpx = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $gpx);
			$gpx = $sitePath . $gpx;
		}
		else
		{
			$gpx = downloadRemoteFile($gpx);
		}

		if ($gpx == '')
		{
			return "No gpx found";
		}
		
		$points = getPoints( $gpx, $pointsoffset, $donotreducegpx);
		
		$points_maps = '';
		$points_graph_dist = '';
		$points_graph_ele = '';
		$points_graph_speed = '';
		$points_graph_hr = '';
		$points_graph_cad = '';
		$waypoints = '';
			
		foreach(array_keys($points->lat) as $i) 
		{
	
			$points_maps .= '['.(float)$points->lat[$i].','.(float)$points->lon[$i].'],';
	
			$_ele = (float)$points->ele[$i];	
			$_dist = (float)$points->dist[$i];	
			
			if ($uom == '1')
			{
				// Miles and feet			
				$_dist *= 0.000621371192;
				$_ele *= 3.2808399;
			} else if ($uom == '2')
			{
				// meters / kilometers
				$_dist = (float)($_dist / 1000);
			}
			
			$points_graph_dist .= $_dist.',';
			$points_graph_ele .= $_ele.',';
				
			if ($showSpeed == true) {
			
				$_speed = (float)$points->speed[$i]; // dafault m/s
				
				if ($uomspeed == '2') // miles/h
				{
					$_speed *= 2.2369362920544025;
				} 
				else if ($uomspeed == '1') // km/h
				{
					$_speed *= 3.6;
				}

				$points_graph_speed .= $_speed.',';
			}
			
			if ($showHr == true)
			{
				$points_graph_hr .= $points->hr[$i].',';
			}
			
			if ($showCad == true)
			{
				$points_graph_cad .= $points->cad[$i].',';
			}
			
		}
			
		if ($showW == true)
		{
			$wpoints = getWayPoints($gpx);
			foreach ($wpoints as $p) {
				$waypoints .= '['.(float)$p[0].','.(float)$p[1].',\''.unescape($p[4]).'\',\''.unescape($p[5]).'\',\''.unescape($p[7]).'\'],';
			}
		}

		$p="/,$/";
		$points_maps = preg_replace($p, "", $points_maps);
	
		$points_graph_dist = preg_replace($p, "", $points_graph_dist);
		$points_graph_ele = preg_replace($p, "", $points_graph_ele);
		$points_graph_speed = preg_replace($p, "", $points_graph_speed);
		$points_graph_hr = preg_replace($p, "", $points_graph_hr);
		$points_graph_cad = preg_replace($p, "", $points_graph_cad);
			
		$waypoints = preg_replace($p, "", $waypoints);
		
		if (preg_match("/^(0,?)+$/", $points_graph_dist)) 
			$points_graph_dist = "";
			
		if (preg_match("/^(0,?)+$/", $points_graph_ele)) 
			$points_graph_ele = "";
			
		if (preg_match("/^(0,?)+$/", $points_graph_speed)) 
			$points_graph_speed = "";
			
		if (preg_match("/^(0,?)+$/", $points_graph_hr)) 
			$points_graph_hr = "";
			
		if (preg_match("/^(0,?)+$/", $points_graph_cad)) 
			$points_graph_cad = "";
		
	}
	
	$ngimgs_data = '';
	if ( $ngGalleries != '' || $ngImages != '' )
	{
		$ngimgs = getNGGalleryImages($ngGalleries, $ngImages, $error);
		$ngimgs_data ='';	
		foreach ($ngimgs as $img) {		
			$data = $img['data'];
			$data = str_replace("\n","",$data);
			$ngimgs_data .= '<span lat="'.$img['lat'].'" lon="'.$img['lon'].'">'.$data.'</span>';
		}
	}
	
	@file_put_contents($gpxcache, 
				   serialize(array( "points_maps" => $points_maps, 
									"points_graph_dist" => $points_graph_dist, 
									"points_graph_ele" => $points_graph_ele, 
									"points_graph_speed" => $points_graph_speed, 
									"points_graph_hr" => $points_graph_hr, 
									"points_graph_cad" => $points_graph_cad,
									"waypoints" => $waypoints)
							), 
				   LOCK_EX);
				   
	@chmod($gpxcache,0755);	
	
	$output = '
		<div id="wpgpxmaps_'.$r.'" class="wpgpxmaps">
			<div id="map_'.$r.'" style="width:'.$w.'; height:'.$mh.'"></div>
			<div id="hchart_'.$r.'" class="plot" style="width:'.$w.'; height:'.$gh.'"></div>
			<div id="ngimages_'.$r.'" class="ngimages" style="display:none">'.$ngimgs_data.'</div>
		</div>
		'. $error .'
		<script type="text/javascript">
		    jQuery(document).ready(function() {
				wpgpxmaps({ targetId    : "'.$r.'",
							mapType     : "'.$mt.'",
							mapData     : ['.$points_maps.'],
							graphDist   : ['.$points_graph_dist.'],
							graphEle    : ['.$points_graph_ele.'],
							graphSpeed  : ['.$points_graph_speed.'],
							graphHr     : ['.$points_graph_hr.'],
							graphCad    : ['.$points_graph_cad.'],
							waypoints   : ['.$waypoints.'],
							unit        : "'.$uom.'",
							unitspeed   : "'.$uomspeed.'",
							color1      : "'.$color_map.'",
							color2      : "'.$color_graph.'",
							color3      : "'.$color_graph_speed.'",
							color4      : "'.$color_graph_hr.'",
							color5      : "'.$color_graph_cad.'",
							chartFrom1  : "'.$chartFrom1.'",
							chartTo1    : "'.$chartTo1.'",
							chartFrom2  : "'.$chartFrom2.'",
							chartTo2    : "'.$chartTo2.'",
							startIcon   : "'.$startIcon.'",
							endIcon     : "'.$endIcon.'",
							currentIcon : "'.$currentIcon.'"
						   });
			});
		</script>';	

	return $output;
}

function downloadRemoteFile($remoteFile)
{
	try
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $remoteFile); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		$resp = curl_exec($ch); 
		curl_close($ch);
		$tmpfname = tempnam ( '/tmp', 'gpx' );
		
		$fp = fopen($tmpfname, "w");
		fwrite($fp, $resp);
		fclose($fp);
		
		return $tmpfname;
	} catch (Exception $e) {
		return '';
	}
}

function unescape($value)
{
	$value = str_replace("'", "\'", $value);
	$value = str_replace(array("\n","\r"), "", $value);
	return $value;
}

function WP_GPX_Maps_install() {
	add_option("wpgpxmaps_width", '100%', '', 'yes');
	add_option("wpgpxmaps_graph_height", '200px', '', 'yes');
	add_option("wpgpxmaps_height", '450px', '', 'yes');
	add_option('wpgpxmaps_map_type','HYBRID','','yes');
	add_option('wpgpxmaps_show_waypoint','','','yes');
	add_option('wpgpxmaps_show_speed','','','yes');
	add_option('wpgpxmaps_pointsoffset','10','','yes');
	add_option('wpgpxmaps_donotreducegpx','true','','yes');
	add_option("wpgpxmaps_unit_of_measure", '0', '', 'yes');
	add_option("wpgpxmaps_unit_of_measure_speed", '0', '', 'yes');
	add_option("wpgpxmaps_graph_line_color", '#3366cc', '', 'yes');
	add_option("wpgpxmaps_graph_line_color_speed", '#ff0000', '', 'yes');
	add_option("wpgpxmaps_map_line_color", '#3366cc', '', 'yes');
	add_option("wpgpxmaps_graph_line_color_cad", '#beecff', '', 'yes');
	add_option("wpgpxmaps_graph_offset_from1", '', '', 'yes');
	add_option("wpgpxmaps_graph_offset_to1", '', '', 'yes');
	add_option("wpgpxmaps_graph_offset_from2", '', '', 'yes');
	add_option("wpgpxmaps_graph_offset_to2", '', '', 'yes');
	add_option("wpgpxmaps_map_start_icon", '', '', 'yes');
	add_option("wpgpxmaps_map_end_icon", '', '', 'yes');
	add_option("wpgpxmaps_map_current_icon", '', '', 'yes');
	add_option("wpgpxmaps_map_nggallery", '', '', 'yes');
	add_option("wpgpxmaps_show_hr", '', '', 'yes');
	add_option("wpgpxmaps_graph_line_color_hr", '#ff77bd', '', 'yes');
	add_option('wpgpxmaps_show_cadence','','','yes');

}

function WP_GPX_Maps_remove() {
	delete_option('wpgpxmaps_width');
	delete_option('wpgpxmaps_graph_height');
	delete_option('wpgpxmaps_height');
	delete_option('wpgpxmaps_map_type');
	delete_option('wpgpxmaps_show_waypoint');
	delete_option('wpgpxmaps_show_speed');
	delete_option('wpgpxmaps_pointsoffset');
	delete_option('wpgpxmaps_donotreducegpx');
	delete_option('wpgpxmaps_unit_of_measure');
	delete_option('wpgpxmaps_unit_of_measure_speed');
	delete_option('wpgpxmaps_graph_line_color');
	delete_option('wpgpxmaps_map_line_color');
	delete_option('wpgpxmaps_graph_line_color_speed');
	delete_option('wpgpxmaps_graph_offset_from1');
	delete_option('wpgpxmaps_graph_offset_to1');
	delete_option('wpgpxmaps_graph_offset_from2');
	delete_option('wpgpxmaps_graph_offset_to2');
	delete_option('wpgpxmaps_map_start_icon');
	delete_option('wpgpxmaps_map_end_icon');
	delete_option('wpgpxmaps_map_current_icon');
	delete_option('wpgpxmaps_map_nggallery');
	delete_option('wpgpxmaps_show_hr');
	delete_option('wpgpxmaps_graph_line_color_hr');
	delete_option('wpgpxmaps_show_cadence');
	delete_option('wpgpxmaps_graph_line_color_cad');
}

?>
