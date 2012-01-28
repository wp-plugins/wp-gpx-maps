<?php

if ( is_admin() ){

	add_action('admin_menu', 'wpgpxmaps_admin_menu');

	function wpgpxmaps_admin_menu() {
		add_options_page('WP GPX Maps', 'WP GPX Maps', 'administrator', 'WP-GPX-Maps', 'WP_GPX_Maps_html_page');
	}
}

function ilc_admin_tabs( $current  ) {
    $tabs = array( 'tracks' => 'Tracks', 'settings' => 'Settings', 'help' => "help" );
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=WP-GPX-Maps&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}

function WP_GPX_Maps_html_page() {
	$realGpxPath = gpxFolderPath();
	$relativeGpxPath = relativeGpxFolderPath();
	$relativeGpxPath = str_replace("\\","/", $relativeGpxPath);
	$gpxRegEx = '/.gpx$/';
	
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
		if (!@mkdir($realGpxPath,755,true)) {
			echo '<div class="error" style="padding:10px">
					Can\'t create <b>'.$realGpxPath.'</b> folder. Please create it and make it writable!<br />
					If not, you will must update the file manually!
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
			<i>Full set of attributes:</i> <b>[sgpx 
													gpx="<?php echo $relativeGpxPath; ?>&lt gpx file name &gt" 
													width=100% 
													mheight=450px 
													gheight=200px 
													mtype=SATELLITE 
													waypoints=true 
													donotreducegpx=false 
													pointsoffset=10
													uom=0
													mlinecolor=#3366cc
													glinecolor=#3366cc]</b>

			<ul>
				<li><b>gpx</b>: relative path to gpx</li>
				<li><b>width</b>: width in pixels</li>
				<li><b>mheight</b>: map height</li>
				<li><b>gheight</b>: graph height</li>
				<li><b>mtype</b>: map aviable types are: HYBRID, ROADMAP, SATELLITE, TERRAIN</li>
				<li><b>waypoints</b>: print the gpx waypoints inside the map (default is FALSE)</li>
				<li><b>donotreducegpx</b>: Print all the point without reduce it (default is FALSE)</li>
				<li><b>pointsoffset</b>: Skip points closer than XX meters(default is 10)</li>
				<li><b>uom</b>: the unit of measure values are: 0, 1 (0 = meters, 1 = miles/feet)</li>
				<li><b>mlinecolor</b>: map line color (default is #3366cc)</li>
				<li><b>glinecolor</b>: graph line color (default is #3366cc)</li>
			</ul>

			<p>
				<a href="http://www.darwinner.it/featured/wp-gpx-maps/">Bugs, problems, thanks and anything else here!</a>
			</p>
			
		</p>
	</div>

<?php
	}

}
?>