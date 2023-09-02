	<?php global $DIRECTORYPRESS_ADIMN_SETTINGS; ?>
	<div class="directorypress-row directorypress-form-group">
		<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_directions_functionality'] == 'builtin'): ?>
		<div class="directorypress-col-md-12">
			<label class="directorypress-control-label"><?php _e('Get directions from:', 'directorypress-maps'); ?></label>
			<div class="has-feedback">
				<input type="text" id="from_direction_<?php echo $map_id; ?>" class="form-control <?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_address_autocomplete']): ?>directorypress-listing-field-autocomplete<?php endif; ?>" placeholder="<?php esc_attr_e('Enter address or zip code', 'directorypress-maps'); ?>" />
				<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_address_geocode']): ?>
				<span class="directorypress-mylocation directorypress-form-control-feedback glyphicon glyphicon-screenshot"></span>
				<?php endif; ?>
			</div>
		</div>
		<div class="directorypress-col-md-12">
			<?php $i = 1; ?>
			<?php foreach ($locations_array AS $location): ?>
			<div class="directorypress-radio">
				<label>
					<input type="radio" name="select_direction" class="select_direction_<?php echo $map_id; ?>" <?php checked($i, 1); ?> value="<?php esc_attr_e($location->map_coords_1.' '.$location->map_coords_2); ?>" />
					<?php 
					if ($address = $location->get_full_address(false))
						echo $address;
					else 
						echo $location->map_coords_1.' '.$location->map_coords_2;
					?>
				</label>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="directorypress-col-md-12">
			<input type="button" class="direction_button front-btn btn btn-primary" id="get_direction_button_<?php echo $map_id; ?>" value="<?php esc_attr_e('Get directions', 'directorypress-maps'); ?>">
		</div>
		<div class="directorypress-col-md-12">
			<div id="route_<?php echo $map_id; ?>" class="directorypress-maps-direction-route"></div>
		</div>
		<?php elseif ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_directions_functionality'] == 'google'): ?>
		<label class="directorypress-col-md-12 directorypress-control-label"><?php _e('directions to:', 'directorypress-maps'); ?></label>
		<form action="//maps.google.com" target="_blank">
			<input type="hidden" name="saddr" value="Current Location" />
			<div class="directorypress-col-md-12">
				<?php $i = 1; ?>
				<?php foreach ($locations_array AS $location): ?>
				<div class="directorypress-radio">
					<label>
						<input type="radio" name="daddr" class="select_direction_<?php echo $map_id; ?>" <?php checked($i, 1); ?> value="<?php esc_attr_e($location->map_coords_1.','.$location->map_coords_2); ?>" />
						<?php 
						if ($address = $location->get_full_address(false))
							echo $address;
						else 
							echo $location->map_coords_1.' '.$location->map_coords_2;
						?>
					</label>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="directorypress-col-md-12">
				<input class="btn btn-primary" type="submit" value="<?php esc_attr_e('Get directions', 'directorypress-maps'); ?>" />
			</div>
		</form>
		<?php endif; ?>
	</div>