
<script type="text/javascript" src="http://meta100.github.com/mColorPicker/javascripts/mColorPicker_min.js" charset="UTF-8"></script>

<?php
	
	$po = get_option('wpgpxmaps_pointsoffset');
	$showW = get_option("wpgpxmaps_show_waypoint");	
	$donotreducegpx = get_option("wpgpxmaps_donotreducegpx");
	$t = get_option('wpgpxmaps_map_type');
	$uom = get_option('wpgpxmaps_unit_of_measure');
	
	if (!($t))
		$t = 'HYBRID';
	if (!($po))
		$po = 10;
		
?>

<form method="post" action="options.php">

	<?php wp_nonce_field('update-options') ?>
	
	<table width="100%">
		<tr>
			<th width="150" scope="row">Map and Graph size:</th>
			<td>
				<i>Width:</i> <input name="wpgpxmaps_width" type="text" id="wpgpxmaps_width" value="<?php echo get_option('wpgpxmaps_width'); ?>" style="width:50px;" />, 
				<i>Map Height:</i> <input name="wpgpxmaps_height" type="text" id="wpgpxmaps_height" value="<?php echo get_option('wpgpxmaps_height'); ?>" style="width:50px;" />, 
				<i>Graph Height:</i> <input name="wpgpxmaps_graph_height" type="text" id="wpgpxmaps_graph_height" value="<?php echo get_option('wpgpxmaps_graph_height'); ?>" style="width:50px;" />
			</td>
		</tr>
		<tr><td></td><td></td></tr>
		<tr>
			<th scope="row">Waypoints Support:</th>
			<td>
				<input name="wpgpxmaps_show_waypoint" type="checkbox" value="true" <?php if($showW == true){echo('checked');} ?> onchange="this.value = (this.checked)"  /><i>Show Waypoints</i>
			</td>
		</tr>
		<tr><td></td><td></td></tr>
		<tr>
			<th scope="row">Default Map Type:</th>
			<td>
				<input type="radio" name="wpgpxmaps_map_type" value="HYBRID" <?php if ($t == 'HYBRID') echo 'checked'; ?> > HYBRID: transparent layer of major streets on satellite images.<br />
				<input type="radio" name="wpgpxmaps_map_type" value="ROADMAP" <?php if ($t == 'ROADMAP') echo 'checked'; ?>> ROADMAP: normal street map.<br />
				<input type="radio" name="wpgpxmaps_map_type" value="SATELLITE" <?php if ($t == 'SATELLITE') echo 'checked'; ?>> SATELLITE: satellite images.<br />
				<input type="radio" name="wpgpxmaps_map_type" value="TERRAIN" <?php if ($t == 'TERRAIN') echo 'checked'; ?>> TERRAIN: maps with physical features such as terrain and vegetation.<br />
			</td>
		</tr>
		<tr><td></td><td></td></tr>
		<tr>
			<th scope="row">Unit of measure:</th>
			<td>
				<select name='wpgpxmaps_unit_of_measure'>
					<option value="0" <?php if ($uom == '0') echo 'selected'; ?>>metres</option>
					<option value="1" <?php if ($uom == '1') echo 'selected'; ?>>miles/feet</option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">Map line color:</th>
			<td>
				<input name="wpgpxmaps_map_line_color" type="color" data-hex="true" value="<?php echo get_option('wpgpxmaps_map_line_color'); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">Graph line color:</th>
			<td>
				<input name="wpgpxmaps_graph_line_color" type="color" data-hex="true" value="<?php echo get_option('wpgpxmaps_graph_line_color'); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">Advanced options:</th>
			<td>
				<br />
				<b>Do not edit if you don't know what you are doing!</b><br />				
				<i>Skip points closer than </i> <input name="wpgpxmaps_pointsoffset" type="text" id="wpgpxmaps_pointsoffset" value="<?php echo $po ?>" style="width:50px;" /><i>metres</i>.
				<input name="wpgpxmaps_donotreducegpx" type="checkbox" value="true" <?php if($donotreducegpx == true){echo('checked');} ?> onchange="this.value = (this.checked)"  /><i>Do not reduce gpx</i>.
			</td>
		</tr>		
	</table>

	<input type="hidden" name="action" value="update" />
	<input name="page_options" type="hidden" value="wpgpxmaps_map_type,wpgpxmaps_height,wpgpxmaps_graph_height,wpgpxmaps_width,wpgpxmaps_show_waypoint,wpgpxmaps_pointsoffset,wpgpxmaps_donotreducegpx,wpgpxmaps_unit_of_measure,wpgpxmaps_map_line_color,wpgpxmaps_graph_line_color" />

	<p>
		<input type="submit" value="<?php _e('Save Changes') ?>" />
	</p>

</form>