
<script type="text/javascript" src="http://meta100.github.com/mColorPicker/javascripts/mColorPicker_min.js" charset="UTF-8"></script>

<?php
	
	$po = get_option('wpgpxmaps_pointsoffset');
	$showW = get_option("wpgpxmaps_show_waypoint");	
	$donotreducegpx = get_option("wpgpxmaps_donotreducegpx");
	$t = get_option('wpgpxmaps_map_type');
	$uom = get_option('wpgpxmaps_unit_of_measure');
	$uomSpeed = get_option('wpgpxmaps_unit_of_measure_speed');
	$showSpeed = get_option('wpgpxmaps_show_speed');
	
	if (!($t))
		$t = 'HYBRID';
	if (!($po))
		$po = 10;
		
?>

<form method="post" action="options.php">

	<?php wp_nonce_field('update-options') ?>
	
	<h3 class="title">Map and Chart size</h3>
	
	<table class="form-table">
		<tr>
			<th scope="row">Width:</th>
			<td>
				<input name="wpgpxmaps_width" type="text" id="wpgpxmaps_width" value="<?php echo get_option('wpgpxmaps_width'); ?>" style="width:50px;" />
			</td>
		</tr>
		<tr>
			<th scope="row">Map Height:</th>
			<td>
				<input name="wpgpxmaps_height" type="text" id="wpgpxmaps_height" value="<?php echo get_option('wpgpxmaps_height'); ?>" style="width:50px;" />
			</td>
		</tr>
		<tr>
			<th scope="row">Graph Height:</th>
			<td>
				<input name="wpgpxmaps_graph_height" type="text" id="wpgpxmaps_graph_height" value="<?php echo get_option('wpgpxmaps_graph_height'); ?>" style="width:50px;" />
			</td>
		</tr>
	</table>
	
	<p class="submit">
		<input type="hidden" name="action" value="update" />
    	<input name="page_options" type="hidden" value="wpgpxmaps_height,wpgpxmaps_graph_height,wpgpxmaps_width" />
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

</form>
	<hr />
<form method="post" action="options.php">

	<?php wp_nonce_field('update-options') ?>
	
	<h3 class="title">Maps</h3>
	
	<table class="form-table">
		<tr>
			<th scope="row">Waypoints Support:</th>
			<td>
				<input name="wpgpxmaps_show_waypoint" type="checkbox" value="true" <?php if($showW == true){echo('checked');} ?> onchange="this.value = (this.checked)"  /><i>Show Waypoints</i>
			</td>
		</tr>
		<tr>
			<th scope="row">Map line color:</th>
			<td>
				<input name="wpgpxmaps_map_line_color" type="color" data-hex="true" value="<?php echo get_option('wpgpxmaps_map_line_color'); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">Default Map Type:</th>
			<td>
				<input type="radio" name="wpgpxmaps_map_type" value="HYBRID" <?php if ($t == 'HYBRID') echo 'checked'; ?> > HYBRID: transparent layer of major streets on satellite images.<br />
				<input type="radio" name="wpgpxmaps_map_type" value="ROADMAP" <?php if ($t == 'ROADMAP') echo 'checked'; ?>> ROADMAP: normal street map.<br />
				<input type="radio" name="wpgpxmaps_map_type" value="SATELLITE" <?php if ($t == 'SATELLITE') echo 'checked'; ?>> SATELLITE: satellite images.<br />
				<input type="radio" name="wpgpxmaps_map_type" value="TERRAIN" <?php if ($t == 'TERRAIN') echo 'checked'; ?>> TERRAIN: maps with physical features such as terrain and vegetation.<br />
			</td>
		</tr>
		
		<tr>
			<th scope="row">Start Icon:</th>
			<td>
				<input name="wpgpxmaps_map_start_icon" value="<?php echo get_option('wpgpxmaps_map_start_icon'); ?>" style="width:400px" /> <em>(Url to image) Leave empty to hide</em>
			</td>
		</tr>
		
		<tr>
			<th scope="row">End Icon:</th>
			<td>
				<input name="wpgpxmaps_map_end_icon" value="<?php echo get_option('wpgpxmaps_map_end_icon'); ?>" style="width:400px" /> <em>(Url to image) Leave empty to hide</em>
			</td>
		</tr>
		
		<tr>
			<th scope="row">Current Position Icon:</th>
			<td>
				<input name="wpgpxmaps_map_current_icon" value="<?php echo get_option('wpgpxmaps_map_current_icon'); ?>" style="width:400px" /> <em>(Url to image) Leave empty for default</em>
			</td>
		</tr>
		
	</table>

	<p class="submit">
		<input type="hidden" name="action" value="update" />
    	<input name="page_options" type="hidden" value="wpgpxmaps_show_waypoint,wpgpxmaps_map_line_color,wpgpxmaps_map_type,wpgpxmaps_map_start_icon,wpgpxmaps_map_end_icon,wpgpxmaps_map_current_icon" />
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

</form>
	<hr />
<form method="post" action="options.php">

	<?php wp_nonce_field('update-options') ?>
	
	<h3 class="title">Chart</h3>
	
	<table class="form-table">
		<tr>
			<tr>
				<th scope="row">Altitude line color:</th>
				<td>
					<input name="wpgpxmaps_graph_line_color" type="color" data-hex="true" value="<?php echo get_option('wpgpxmaps_graph_line_color'); ?>" />
				</td>
			</tr>		
			<th scope="row">Unit of measure:</th>
			<td>
				<select name='wpgpxmaps_unit_of_measure'>
					<option value="0" <?php if ($uom == '0') echo 'selected'; ?>>meters/meters</option>
					<option value="1" <?php if ($uom == '1') echo 'selected'; ?>>feet/miles</option>
					<option value="2" <?php if ($uom == '2') echo 'selected'; ?>>meters/kilometers</option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">Altitude display offset:</th>
			<td>
				From
				<input name="wpgpxmaps_graph_offset_from1" value="<?php echo get_option('wpgpxmaps_graph_offset_from1'); ?>" style="width:50px;" />
				To
				<input name="wpgpxmaps_graph_offset_to1" value="<?php echo get_option('wpgpxmaps_graph_offset_to1'); ?>" style="width:50px;" />
				<em>(leave empty for auto scale)</em>
			</td>
		</tr>
		<tr>
			<th scope="row">Show speed:</th>
			<td>
				<input name="wpgpxmaps_show_speed" type="checkbox" value="true" <?php if($showSpeed == true){echo('checked');} ?> onchange="this.value = (this.checked)"  /><i>Show Speed</i>
			</td>
		</tr>		
		<tr>
			<th scope="row">Speed line color:</th>
			<td>
				<input name="wpgpxmaps_graph_line_color_speed" type="color" data-hex="true" value="<?php echo get_option('wpgpxmaps_graph_line_color_speed'); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">Speed unit of measure:</th>
			<td>
				<select name='wpgpxmaps_unit_of_measure_speed'>
					<option value="0" <?php if ($uomSpeed == '0') echo 'selected'; ?>>m/s</option>
					<option value="1" <?php if ($uomSpeed == '1') echo 'selected'; ?>>km/h</option>
					<option value="2" <?php if ($uomSpeed == '2') echo 'selected'; ?>>miles/h</option>
				</select>
			</td>
		</tr>		
		<tr>
			<th scope="row">Speed display offset:</th>
			<td>
				From
				<input name="wpgpxmaps_graph_offset_from2" value="<?php echo get_option('wpgpxmaps_graph_offset_from2'); ?>" style="width:50px;" />
				To
				<input name="wpgpxmaps_graph_offset_to2" value="<?php echo get_option('wpgpxmaps_graph_offset_to2'); ?>" style="width:50px;" />
				<em>(leave empty for auto scale)</em>
			</td>
		</tr>
	</table>
	

	<p class="submit">
		<input type="hidden" name="action" value="update" />
    	<input name="page_options" type="hidden" value="wpgpxmaps_unit_of_measure,wpgpxmaps_graph_line_color,wpgpxmaps_show_speed,wpgpxmaps_graph_line_color_speed,wpgpxmaps_unit_of_measure_speed,wpgpxmaps_graph_offset_from1,wpgpxmaps_graph_offset_to1,wpgpxmaps_graph_offset_from2,wpgpxmaps_graph_offset_to2" />
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

</form>
	<hr />
<form method="post" action="options.php">

	<?php wp_nonce_field('update-options') ?>
	
	<h3 class="title">Advanced options	<small>(Do not edit if you don't know what you are doing!)</small></h3>

	
	<table class="form-table">
		<tr>
			<th scope="row"></th>
			<td>
				<i>Skip points closer than </i> <input name="wpgpxmaps_pointsoffset" type="text" id="wpgpxmaps_pointsoffset" value="<?php echo $po ?>" style="width:50px;" /><i>meters</i>.
			</td>
		</tr>		
		<tr>
			<th scope="row"></th>
			<td>
				<input name="wpgpxmaps_donotreducegpx" type="checkbox" value="true" <?php if($donotreducegpx == true){echo('checked');} ?> onchange="this.value = (this.checked)"  /><i>Do not reduce gpx</i>.
			</td>
		</tr>	
	</table>

	<input type="hidden" name="action" value="update" />
	<input name="page_options" type="hidden" value="wpgpxmaps_pointsoffset,wpgpxmaps_donotreducegpx" />

	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

</form>
<hr />