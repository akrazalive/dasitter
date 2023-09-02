<div class="directorypress-content-wrap">
<?php if (!$static_image): ?>
	<script>
	//alert('dynamic')
		directorypress_map_markers_attrs_array.push(new directorypress_map_markers_attrs('<?php echo $map_id; ?>', eval(<?php echo $locations_options; ?>), <?php echo ($enable_radius_circle) ? 1 : 0; ?>, <?php echo ($enable_clusters) ? 1 : 0; ?>, <?php echo ($show_summary_button) ? 1 : 0; ?>, <?php echo ($show_readmore_button) ? 1 : 0; ?>, <?php echo ($draw_panel) ? 1 : 0; ?>, '<?php echo $map_style; ?>', <?php echo ($enable_full_screen) ? 1 : 0; ?>, <?php echo ($enable_wheel_zoom) ? 1 : 0; ?>, <?php echo ($enable_dragging_touchscreens) ? 1 : 0; ?>, <?php echo ($center_map_onclick) ? 1 : 0; ?>, <?php echo ($show_directions) ? 1 : 0; ?>, <?php echo $map_args; ?>));
	//alert(directorypress_map_markers_attrs_array);
	</script>
	<div id="directorypress-maps-container-<?php echo $map_id; ?>" class="directorypress-maps-container <?php if ($search_form && $search_form->is_categories_or_Keywords_field()) echo 'directorypress-map-search-input-enabled'; ?> <?php if ($has_sticky_scroll):?>directorypress-has-stickyscroll<?php endif; ?>" data-id="<?php echo $map_id; ?>" <?php if ($has_sticky_scroll_toppadding):?>data-toppadding="<?php echo $has_sticky_scroll_toppadding; ?>"<?php endif; ?> data-height="<?php echo $height; ?>">
		<div id="directorypress-maps-canvas-<?php echo $map_id; ?>" class="directorypress-maps-canvas <?php if (!empty($args['search_on_map_open'])) echo 'directorypress-sidebar-open'; ?>" <?php if ($custom_home): ?>data-custom-home="1"<?php endif; ?> data-shortcode-hash="<?php echo $map_id; ?>" style="<?php if ($width) echo 'max-width:' . $width . 'px'; ?> height: <?php if ($height) { if ($height == '100%') echo '100%'; else echo $height.'px'; } else echo '300px'; ?>"></div>
	</div>

	<?php if ($show_directions && directorypress_map_type() == 'google'): ?>
		<?php dpms_display_template('partials/google_directions.php', array('map_id' => $map_id, 'locations_array' => $locations_array))?>
	<?php endif; ?>
<?php else: ?>
	<?php echo $map_object->buildStaticMap(); ?>
<?php endif; ?>
</div>