<?php 

function WP_GPX_Maps_html_page() {

	$realGpxPath = substr (__FILE__, 0, strrpos(__FILE__,'/wp-content/'))."/wp-content/uploads/gpx";
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
		if (!@mkdir($dir)) {
			echo '<div class="error" style="padding:10px">
					Can\'t create <b>'.$realGpxPath.'</b> folder. Please create it and make it writable!<br />
					If not, you will must update the file manually!
				  </div>';
		}
	}

?>

<div style="padding:10px 10px 30px 10px;">
	<b>The fastest way to use this plugin:</b> upload the file using the uploader below, than put this 
				shotcode: <b>[sgpx gpx="/wp-content/uploads/gpx/&lt gpx file name &gt"]</b> in the pages/posts.
</div>

<form method="post" action="options.php">

	<?php wp_nonce_field('update-options') ?>

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
	<input name="page_options" type="hidden" value="wpgpxmaps_map_type,wpgpxmaps_height,wpgpxmaps_width" />

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

	if ( is_readable ( $realGpxPath ) && $handle = opendir($realGpxPath)) { ?>
	
		<div class="tablenav top">
			<form enctype="multipart/form-data" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
				Choose a file to upload: <input name="uploadedfile" type="file" onchange="this.parentNode.submit()" />
				<?php
					if ( isset($_FILES['uploadedfile']) )
					{
						$target_path = $realGpxPath ."/". basename( $_FILES['uploadedfile']['name']); 
						if (preg_match($gpxRegEx,$target_path ))
						{
							if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
								echo "The file ".  basename( $_FILES['uploadedfile']['name']). " has been uploaded";
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
					<tr class="active" id="akismet">
						<td class="plugin-title">
							<strong><?php echo $entry; ?></strong>
							<div class="row-actions-visible">
								<a href="#" onclick="delgpx('<?php echo $entry ?>'); return false;">Delete</a>
								|	
								<a href="../wp-content/uploads/gpx/<?php echo $entry?>">Download</a>								
							</div>
						</td>
						<td class="column-description desc">
							<div class="plugin-description">
								<p><?php echo date ("F d Y H:i:s.", filemtime( $file ) ) ?></p>
							</div>
						</td>
						<td class="column-description desc">
							<div class="plugin-description">
								<p><?php echo number_format ( filesize( $file ) , 0, '.', ',' ) ?></p>
							</div>
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