<?php
/*
Plugin Name: WP-GPX-Maps
Plugin URI: http://www.darwinner.it/
Description: Draws a gpx track with altitude graph
Version: 1.0.8
Author: Bastianon Massimo
Author URI: http://www.pedemontanadelgrappa.it/
License: GPL
*/

include 'wp-gpx-maps_Utils.php';
include 'wp-gpx-maps_admin.php';

add_action( 'wp_print_scripts', 'enqueue_WP_GPX_Maps_scripts' );
add_shortcode('sgpx','handle_WP_GPX_Maps_Shortcodes');
register_activation_hook(__FILE__,'WP_GPX_Maps_install'); 
register_deactivation_hook( __FILE__, 'WP_GPX_Maps_remove');	
add_filter('plugin_action_links', 'WP_GPX_Maps_action_links', 10, 2);

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
?>
	<script type='text/javascript' src='https://www.google.com/jsapi?ver=3.2.1'></script>
	<script type='text/javascript'>
		google.load('visualization', '1', {'packages':['corechart']});
		google.load("maps", "3", {other_params: 'sensor=false'});
	</script>
	<script type='text/javascript' src='<?php echo plugins_url('/WP-GPX-Maps.js', __FILE__) ?>'></script>	
<?php
}

 
function handle_WP_GPX_Maps_Shortcodes($attr, $content='')
{
	$gpx = $attr["gpx"];
	$w = $attr["width"];
	$mh = $attr["mheight"];
	$mt = $attr["mtype"];
	$gh = $attr["gheight"];
	$showW = $attr['waypoints'];
	
	if ($w == '')
	{
		$w = get_option("wpgpxmaps_width");
	}
	
	if ($mh == '')
	{
		$mh = get_option("wpgpxmaps_height");
	}
	
	if ($gh == '')
	{
		$gh = get_option("wpgpxmaps_graph_height");
	}
	
	if ($mt == '')
	{
		$mt = get_option("wpgpxmaps_map_type");
	}
	
	if ($gh == '')
	{
		$gh = "200px";
	}
	
	if ($showW == '')
	{
		$showW = get_option("wpgpxmaps_show_waypoint");
	}
	
	$r = rand(1,5000000);
	
	$gpx = trim($gpx);
	
	$points = getPoints($gpx);
	$points_maps = '';
	$points_graph = '';
	$waypoints = '';

	foreach ($points as $p) {
		$points_maps .= "[".(float)$p[0].",".(float)$p[1]."],";
		$points_graph .= "[".(float)$p[3].",".(float)$p[2]."],";
	}		//all the points are [0,0]	$points_graph = preg_replace("/^(\[0,0\],)+$/", "", $points_graph);	
	
	if ($showW == true)
	{
		$wpoints = getWayPoints($gpx);
		foreach ($wpoints as $p) {
			$waypoints .= "[".(float)$p[0].",".(float)$p[1].",'".unescape($p[4])."','".unescape($p[5])."','".unescape($p[7])."'],";
		}
	}
	
	$p="/,$/";
	$points_maps = preg_replace($p, "", $points_maps);
	$points_graph = preg_replace($p, "", $points_graph);			
	$waypoints = preg_replace($p, "", $waypoints);
	
	if (preg_match("/^(\[0,0\],?)+$/", $points_graph)) 
	{		
		$points_graph = "";	
	} 

	$output = '
		<div id="wpgpxmaps_'.$r.'" style="clear:both;">
			<div id="map_'.$r.'" style="width:'.$w.'; height:'.$mh.'"></div>
			<div id="chart_'.$r.'" class="plot" style="width:'.$w.'; height:'.$gh.'"></div>
		</div>
		<script type="text/javascript">
			var m_'.$r.' = ['.$points_maps.'];
			var c_'.$r.' = ['.$points_graph.'];	
			var w_'.$r.' = ['.$waypoints.'];	
			wpgpxmaps("'.$r.'","'.$mt.'",m_'.$r.',c_'.$r.', w_'.$r.');		
		</script>';	
	
	return $output;
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
}

function WP_GPX_Maps_remove() {
	delete_option('wpgpxmaps_width');
	delete_option('wpgpxmaps_graph_height');
	delete_option('wpgpxmaps_height');
	delete_option('wpgpxmaps_map_type');
	delete_option('wpgpxmaps_show_waypoint');
}

?>
