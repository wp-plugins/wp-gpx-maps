<?php
/*
Plugin Name: WP-GPX-Maps
Plugin URI: http://www.darwinner.it/
Description: Add a gpx trak with altitude graph
Version: 1.0.0
Author: Bastianon Massimo
Author URI: http://www.pedemontanadelgrappa.it/
License: GPL
*/

include 'wp-gpx-maps_Utils.php';

add_action( 'wp_print_scripts', 'enqueue_WP_GPX_Maps_scripts' );

function enqueue_WP_GPX_Maps_scripts()
{
?>
	<script type='text/javascript' src='https://www.google.com/jsapi?ver=3.2.1'></script>
	<script type="text/javascript">
		google.load("maps", "3", {other_params: "sensor=false"});
		google.load('visualization', '1', {'packages':['corechart']});
	</script>
	<script type='text/javascript' src='<?php echo plugins_url('/wp-gpx-maps.js', __FILE__) ?>'></script>
<?php
}
 
 //add_shortcode('wpgpxmaps','handle_WP_GPX_Maps_Shortcodes');
 
 add_shortcode('sgpx','handle_WP_GPX_Maps_Shortcodes');

 function handle_WP_GPX_Maps_Shortcodes($attr, $content='')
 {
	$gpx = $attr["gpx"];
	$key = get_option("wpgpxmaps_bing_license");
	$w = get_option("wpgpxmaps_width");
	$h = get_option("wpgpxmaps_height");
	$t = get_option("wpgpxmaps_map_type");

	$r = rand(1,5000000);
	
	$points = getPoints($gpx);
	$points_maps = '';
	$points_graph = '';

	foreach ($points as $p) {
		$points_maps .= "[".(float)$p[0].",".(float)$p[1]."],";
		$points_graph .= "[".(float)$p[3].",".(float)$p[2]."],";
	}
	$p="/,$/";
	$points_maps = preg_replace($p, "", $points_maps);
	$points_graph = preg_replace($p, "", $points_graph);
	
	echo 
		'
		<div id="wpgpxmaps_'.$r.'" style="clear:both;">
			<div id="map_'.$r.'" style="width:'.$w.'; height:'.$h.'"></div>
			<div id="chart_'.$r.'" class="plot" style="width:'.$w.'; height:'.(preg_replace("([pxPX%emEM])", "", $h) / 2).'px"></div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function () {
				var m_'.$r.' = ['.$points_maps.'];
				var c_'.$r.' = ['.$points_graph.'];
				wpgpxmaps("wpgpxmaps_'.$r.'",\''.$t.'\',m_'.$r.',c_'.$r.');
			});
			
		</script>';	
 }

register_activation_hook(__FILE__,'WP_GPX_Maps_install'); 
register_deactivation_hook( __FILE__, 'WP_GPX_Maps_remove');

function WP_GPX_Maps_install() {
	add_option("wpgpxmaps_width", '100%', '', 'yes');
	add_option("wpgpxmaps_height", '450px', '', 'yes');
	add_option('wpgpxmaps_map_type','HYBRID','','yes');
}

function WP_GPX_Maps_remove() {
	delete_option('wpgpxmaps_width');
	delete_option('wpgpxmaps_height');
	delete_option('wpgpxmaps_map_type');
}
?>

<?php
if ( is_admin() ){

	add_action('admin_menu', 'wpgpxmaps_admin_menu');

	function wpgpxmaps_admin_menu() {
		add_options_page('WP GPX Maps', 'WP GPX Maps', 'administrator', 'WP-GPX-Maps', 'WP_GPX_Maps_html_page');
	}
}
?>

<?php
function WP_GPX_Maps_html_page() {
?>
<div>
<h2>WP GPX Settings</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table width="100%">
	<tr valign="top">
		<th width="200" scope="row">Maps Width:</th>
		<td>
			<input name="wpgpxmaps_width" type="text" id="wpgpxmaps_width" value="<?php echo get_option('wpgpxmaps_width'); ?>" style="width:50px;" />
		</td>
	</tr>
	<tr valign="top">
		<th width="200" scope="row">Maps Height:</th>
		<td>
			<input name="wpgpxmaps_height" type="text" id="wpgpxmaps_height" value="<?php echo get_option('wpgpxmaps_height'); ?>" style="width:50px;" />
		</td>
	</tr>
	<tr>
		<th width="200" scope="row">Default Map Type:</th>
		<td>
		
			<?php 
				$t = get_option('wpgpxmaps_map_type');
				if (!($t))
					$t = 'HYBRID';
			?>
			
			<input type="radio" name="wpgpxmaps_map_type" value="HYBRID" <?php if ($t == 'HYBRID') echo 'checked'; ?> > This map type displays a transparent layer of major streets on satellite images.<br />
			<input type="radio" name="wpgpxmaps_map_type" value="ROADMAP" <?php if ($t == 'ROADMAP') echo 'checked'; ?>> This map type displays a normal street map.<br />
			<input type="radio" name="wpgpxmaps_map_type" value="SATELLITE" <?php if ($t == 'SATELLITE') echo 'checked'; ?>> This map type displays satellite images.<br />
			<input type="radio" name="wpgpxmaps_map_type" value="TERRAIN" <?php if ($t == 'TERRAIN') echo 'checked'; ?>> This map type displays maps with physical features such as terrain and vegetation.<br />
			
		</td>
	</tr>

</table>

<input type="hidden" name="action" value="update" />

<p>
<input type="submit" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
<?php
}
?>

