<?php

if ( is_admin() ){

	add_action('admin_menu', 'wpgpxmaps_admin_menu');

	function wpgpxmaps_admin_menu() {
		add_options_page('WP GPX Maps', 'WP GPX Maps', 'administrator', 'WP-GPX-Maps', 'WP_GPX_Maps_html_page');
	}
}

function WP_GPX_Maps_html_page() {
	$realGpxPath = gpxFolderPath();
	$relativeGpxPath = relativeGpxFolderPath();
	$relativeGpxPath = str_replace("\\","/", $relativeGpxPath);
	$gpxRegEx = '/.gpx$/';
?>

<div>
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
	$po = get_option('wpgpxmaps_pointsoffset');
	$showW = get_option("wpgpxmaps_show_waypoint");	
	$donotreducegpx = get_option("wpgpxmaps_donotreducegpx");
	$t = get_option('wpgpxmaps_map_type');
	if (!($t))
		$t = 'HYBRID';
	if (!($po))
		$po = 10;
		
?>

<div style="padding:10px;">
	<b>The fastest way to use this plugin:</b> upload the file using the uploader below, than put this 
				shotcode: <b>[sgpx gpx="<?php echo $relativeGpxPath; ?>&lt gpx file name &gt"]</b> in the pages/posts.
	<p>
		<i>Full set of attributes:</i> <b>[sgpx gpx="<?php echo $relativeGpxPath; ?>&lt gpx file name &gt" width=100% mheight=450px gheight=200px mtype=SATELLITE waypoints=true donotreducegpx=false pointsoffset=10]</b>
	</p>
</div>

<form method="post" action="options.php">

	<?php wp_nonce_field('update-options') ?>
	
	<table width="100%">
		<tr>
			<th width="150" scope="row">Default Options:</th>
			<td>
				<i>Width:</i> <input name="wpgpxmaps_width" type="text" id="wpgpxmaps_width" value="<?php echo get_option('wpgpxmaps_width'); ?>" style="width:50px;" />, 
				<i>Maps Height:</i> <input name="wpgpxmaps_height" type="text" id="wpgpxmaps_height" value="<?php echo get_option('wpgpxmaps_height'); ?>" style="width:50px;" />, 
				<i>Graph Height:</i> <input name="wpgpxmaps_graph_height" type="text" id="wpgpxmaps_graph_height" value="<?php echo get_option('wpgpxmaps_graph_height'); ?>" style="width:50px;" />,
				<input name="wpgpxmaps_show_waypoint" type="checkbox" value="true" <?php if($showW == true){echo('checked');} ?> onchange="this.value = (this.checked)"  /><i>Show Waypoints</i>
			</td>
		</tr>
		<tr>
			<th scope="row">Default Map Type:</th>
			<td>
				<br />
				<input type="radio" name="wpgpxmaps_map_type" value="HYBRID" <?php if ($t == 'HYBRID') echo 'checked'; ?> > HYBRID: transparent layer of major streets on satellite images.<br />
				<input type="radio" name="wpgpxmaps_map_type" value="ROADMAP" <?php if ($t == 'ROADMAP') echo 'checked'; ?>> ROADMAP: normal street map.<br />
				<input type="radio" name="wpgpxmaps_map_type" value="SATELLITE" <?php if ($t == 'SATELLITE') echo 'checked'; ?>> SATELLITE: satellite images.<br />
				<input type="radio" name="wpgpxmaps_map_type" value="TERRAIN" <?php if ($t == 'TERRAIN') echo 'checked'; ?>> TERRAIN: maps with physical features such as terrain and vegetation.<br />
			</td>
		</tr>
		<tr>
			<th scope="row">Advanced options:</th>
			<td>
				<br />
				<b>Do not edit if you don't know what you are doing!</b><br />				
				<i>Skip points closer than </i> <input name="wpgpxmaps_pointsoffset" type="text" id="wpgpxmaps_pointsoffset" value="<?php echo $po ?>" style="width:50px;" /><i>meters</i>.
				<input name="wpgpxmaps_donotreducegpx" type="checkbox" value="true" <?php if($donotreducegpx == true){echo('checked');} ?> onchange="this.value = (this.checked)"  /><i>Do not reduce gpx</i>.
			</td>
		</tr>
	</table>

	<input type="hidden" name="action" value="update" />
	<input name="page_options" type="hidden" value="wpgpxmaps_map_type,wpgpxmaps_height,wpgpxmaps_graph_height,wpgpxmaps_width,wpgpxmaps_show_waypoint,wpgpxmaps_pointsoffset,wpgpxmaps_donotreducegpx" />

	<p>
		<input type="submit" value="<?php _e('Save Changes') ?>" />
	</p>

</form>

<?php

	if ( isset($_POST['delete']) )
	{
		$del = $_POST['delete'];
		if (preg_match($gpxRegEx, $del ) && file_exists($realGpxPath ."/". $del))
		{
			unlink($realGpxPath ."/". $del);
		}
	}

	if ( is_writable ( $realGpxPath ) ){
	
	?>
	
		<div class="tablenav top">
			<form enctype="multipart/form-data" method="POST">
				Choose a file to upload: <input name="uploadedfile" type="file" onchange="submitgpx(this);" />
				<?php
					if ( isset($_FILES['uploadedfile']) )									
					{						
						$target_path = $realGpxPath ."/". basename( $_FILES['uploadedfile']['name']); 						
						if (preg_match($gpxRegEx, $target_path))
						{				
							if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
								echo "File <b>".  basename( $_FILES['uploadedfile']['name']). "</b> has been uploaded";
							} else{
								echo "There was an error uploading the file, please try again!";
							}		
						}
						else
						{
							echo "file not supported!";
						}
					}
				?>
			</form>
		</div>	
	
	<?php
	
	}
	
	if ( is_readable ( $realGpxPath ) && $handle = opendir($realGpxPath)) { 		
		
		?>

		<table cellspacing="0" class="wp-list-table widefat plugins">
			<thead>
				<tr>
					<th style="" class="manage-column" id="name" scope="col">File</th>
					<th style="" class="manage-column" id="name" scope="col">Last modified</th>
					<th style="" class="manage-column" id="name" scope="col">File size (Byte)</th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<th style="" class="manage-column" id="name" scope="col">File</th>
					<th style="" class="manage-column" id="name" scope="col">Last modified</th>
					<th style="" class="manage-column" id="name" scope="col">File size (Byte)</th>
				</tr>
			</tfoot>

			<tbody id="the-list">
			
			<?php
			while (false !== ($entry = readdir($handle))) {
				if (preg_match($gpxRegEx,$entry ))
				{
				$file = $realGpxPath . "/" . $entry;
				?>
				<tr>
					<td style="border:none; padding-bottom:0;">
						<strong><?php echo $entry; ?></strong>
					</td>
					<td style="border:none; padding-bottom:0;">
						<?php echo date ("F d Y H:i:s.", filemtime( $file ) ) ?>
					</td>
					<td style="border:none; padding-bottom:0;">
						<?php echo number_format ( filesize( $file ) , 0, '.', ',' ) ?>
					</td>
				</tr>	
				<tr>
					<td colspan=3 style="padding: 0px 7px 7px 7px;">
							<a href="#" onclick="delgpx('<?php echo $entry ?>'); return false;">Delete</a>
							|	
							<a href="../wp-content/uploads/gpx/<?php echo $entry?>">Download</a>
							|
							Shortcode: [sgpx gpx="<?php echo  $relativeGpxPath . $entry; ?>"]
					</td>
				</tr>
				<?php
				}
			}
			?>
			</tbody>
		</table>

<?php 
	closedir($handle);
	} ?>

</div>
<script type="text/javascript">

	function submitgpx(el)
	{
		 var newEl = document.createElement('span'); 
		 newEl.innerHTML = 'Uploading file...';
		 el.parentNode.insertBefore(newEl,el.nextSibling);  
		 el.parentNode.submit()
	}

	function delgpx(file)
	{
		if (confirm('Delte this file: ' + file + '?'))
		{
			document.formdelgpx.delete.value = file;	
			document.formdelgpx.submit();	
		}
	}

</script>
<form method="post" name="formdelgpx" style="display:none;">
	<input type="hidden" name="delete" />
</form>	
<?php
}
?>