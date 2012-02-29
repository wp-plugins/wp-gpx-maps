<?php

	if ( isset($_POST['delete']) )
	{
		$del = $_POST['delete'];
		if (preg_match($gpxRegEx, $del ) && file_exists($realGpxPath ."/". $del))
		{
			unlink($realGpxPath ."/". $del);
		}
	}
	
	if ( isset($_POST['clearcache']) )
	{
		echo "Cache is now empty!";
		recursive_remove_directory($cacheGpxPath,true);
	}

	if ( is_writable ( $realGpxPath ) ){
	
	?>
	
		<div class="tablenav top">
			<form enctype="multipart/form-data" method="POST" style="float:left; margin:5px 20px 0 0">
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
			
			<form method="POST" style="float:left; margin:5px 20px 0 0">
				<input type="submit" name="clearcache" value="Clear Cache" />
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
		if (confirm('Delete this file: ' + file + '?'))
		{
			document.formdelgpx.delete.value = file;	
			document.formdelgpx.submit();	
		}
	}

</script>
<form method="post" name="formdelgpx" style="display:none;">
	<input type="hidden" name="delete" />
</form>	
