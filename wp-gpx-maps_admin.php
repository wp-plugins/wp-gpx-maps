<?php

if ( is_admin() ){
	add_action('admin_menu', 'wpgpxmaps_admin_menu');
}

function wpgpxmaps_admin_menu() {
	if ( current_user_can('manage_options') ){
		add_options_page('WP GPX Maps', 'WP GPX Maps', 'manage_options', 'WP-GPX-Maps', 'WP_GPX_Maps_html_page');
	} 
	else if ( current_user_can('publish_posts') ) {
		add_menu_page('WP GPX Maps', 'WP GPX Maps', 'publish_posts', 'WP-GPX-Maps', 'WP_GPX_Maps_html_page');
	}
}

function ilc_admin_tabs( $current  ) {

	if (current_user_can('manage_options'))
	{
		$tabs = array( 'tracks' => 'Tracks', 'settings' => 'Settings', 'help' => "help" );	
	}
	else if ( current_user_can('publish_posts') ) {
		$tabs = array( 'tracks' => 'Tracks', 'help' => "help" );	
	}

    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=WP-GPX-Maps&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}

function WP_GPX_Maps_html_page() {
	$realGpxPath = gpxFolderPath();
	$cacheGpxPath = gpxCacheFolderPath();
	$relativeGpxPath = relativeGpxFolderPath();
	$relativeGpxPath = str_replace("\\","/", $relativeGpxPath);
	
	$tab = $_GET['tab'];
	
	if ($tab == '')
		$tab = 'tracks';
	

?>
	<div id="icon-themes" class="icon32"><br></div>
		<h2>WP GPX Settings</h2>	
<?php

	if(file_exists($realGpxPath) && is_dir($realGpxPath))
	{
		//dir exsist!
	}
	else
	{
		if (!@mkdir($realGpxPath,0755,true)) {
			echo '<div class="error" style="padding:10px">
					Can\'t create <b>'.$realGpxPath.'</b> folder. Please create it and make it writable!<br />
					If not, you will must update the file manually!
				  </div>';
		}
	}
	
	if(file_exists($cacheGpxPath) && is_dir($cacheGpxPath))
	{
		//dir exsist!
	}
	else
	{
		if (!@mkdir($cacheGpxPath,0755,true)) {
			echo '<div class="error" style="padding:10px">
					Can\'t create <b>'.$cacheGpxPath.'</b> folder. Please create it and make it writable!<br />
					If not, cache will not created and your site could be slower!
				  </div>';
		}
	}

	ilc_admin_tabs($tab);	
	
	if ($tab == "tracks")
	{
		include 'wp-gpx-maps_admin_tracks.php';
	}
	else if ($tab == "settings")
	{
		include 'wp-gpx-maps_admin_settings.php';
	}
	else if ($tab == "help")
	{
?>

	<div style="padding:10px;">
		<b>The fastest way to use this plugin:</b><br /> upload the file using the uploader in the first tab, than copy the shortcode from the list and paste it in the pages/posts.
		<p>You can manually set the relative path to your gpx: <b>[sgpx gpx="<?php echo $relativeGpxPath; ?>&lt gpx file name &gt"]</b>.</p>
		<p>You can also use gpx from other sites: <b>[sgpx gpx="http://www.someone.com/somewhere/somefile.gpx"]</b></p>
		<hr />
		<p>
			<i>Full set of attributes:</i> <b>[sgpx gpx="<?php echo $relativeGpxPath; ?>&lt gpx file name &gt" </b>
													&nbsp;&nbsp;&nbsp;<em>&gt&gt read below all the optional attributes &lt&lt</em>&nbsp;&nbsp;&nbsp;
											<b>]</b>

			<ul>
				<li><b>width</b>: width in pixels</li>
				<li><b>mheight</b>: map height</li>
				<li><b>gheight</b>: graph height</li>
				<li><b>mtype</b>: map available types are: HYBRID, ROADMAP, SATELLITE, TERRAIN</li>
				<li><b>waypoints</b>: print the gpx waypoints inside the map (default is FALSE)</li>
				<li><b>donotreducegpx</b>: print all the point without reduce it (default is FALSE)</li>
				<li><b>pointsoffset</b>: skip points closer than XX meters(default is 10)</li>
				<li><b>uom</b>: distance/altitude possible unit of measure are: 0, 1, 2, 3, 4, 5 (0 = meters, 1 = feet/miles, 2 = meters/kilometers, 3 = meters/nautical miles, 4 = meters/miles, 5 = feet/nautical miles)</li>
				<li><b>mlinecolor</b>: map line color (default is #3366cc)</li>
				<li><b>glinecolor</b>: graph line color (default is #3366cc)</li>
				<li><b>glinecolorspeed</b>: speed line color (default is #ff0000)</li>
				<li><b>glinecolorhr</b>: heart rate line color (default is #ff77bd)</li>
				<li><b>glinecolorcad</b>: cadence line color (default is #beecff)</li>
				<li><b>glinecolorgrade</b>: grade line color (default is #beecff)</li>
				<li><b>showspeed</b>: show speed inside the chart (default is FALSE)</li>
				<li><b>showhr</b>: show heart rate inside the chart (default is FALSE)</li>
				<li><b>showcad</b>: show cadence inside the chart (default is FALSE)</li>
				<li><b>showgrade</b>: show grade inside the chart (default is FALSE)</li>
				<li><b>uomspeed</b>: unit of measure for speed are: 0, 1, 2, 3, 4 (0 = m/s, 1 = km/h, 2 = miles/h, 3 = min/km, 4 = min/miles, 5 = Nautical Miles/Hour (Knots))</li>
				<li><b>chartFrom1</b>: minimun value for altitude chart</li>
				<li><b>chartTo1</b>: maxumin value for altitude chart</li>
				<li><b>chartFrom2</b>: minimun value for speed chart</li>
				<li><b>chartTo2</b>: maxumin value for speed chart</li>				
				<li><b>startIcon</b>: Start track icon</li>
				<li><b>waypointicon</b>: waypoint custom icon</li>
				<li><b>endIcon</b>: End track icon</li>
				<li><b>currentIcon</b>: Current position icon (when mouse hover)</li>					
				<li><b>nggalleries</b>: NextGen Gallery id or a list of Galleries id separated by a comma</li>	
				<li><b>ngimages</b>: NextGen Image id or a list of Images id separated by a comma</li>	
				<li><b>dtoffset</b>: the difference (in seconds) between your gpx tool date and your camera date</li>	
				<li><b>zoomonscrollwheel</b>: zoom on map when mouse scroll wheel (default is FALSE)</li>
				<li><b>download</b>: Allow users to download your GPX file (default is FALSE)</li>
				<li><b>summary</b>: Print symmary details of your GPX (default is FALSE)</li>
			</ul>
		
			<p>
				<a href="http://devfarm.it/forums/forum/wp-gpx-maps/">Bugs, problems, thanks and anything else here!</a>
			</p>
			
		</p>
	</div>

<?php
	}

}
?>