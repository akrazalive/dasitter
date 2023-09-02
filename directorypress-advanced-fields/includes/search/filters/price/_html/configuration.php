<script>
	(function($) {
		"use strict";
	
		$(function() {
			$("#add_selection_item").click(function() {
				$("#selection_items_wrapper").append('<div class="selection_item"><input name="min_max_options[]" type="text" size="9" value="" /><i class="delete_section_item far fa-trash-alt"></i></div>');
			});
			$(document).on("click", ".delete_section_item", function() {
				$(this).parent().remove();
			});
		});
	})(jQuery);
</script>
<div class="directorypress-modal-content wp-clearfix">
	<form class="search-config" method="POST" action="">
		<?php wp_nonce_field(DIRECTORYPRESS_PATH, 'directorypress_configure_fields_nonce');?>
		<div class="field-holder">
			<div><label><?php _e('Search mode', 'directorypress-advanced-fields'); ?><span class="directorypress-red-asterisk">*</span></label></div>
			<div>
				<p><?php _e('Exact Number', 'directorypress-advanced-fields'); ?></p>
				<label class="switch">
					<input name="mode" type="radio" value="exact_number" <?php checked($search_field->mode, 'exact_number'); ?> />
					<span class="slider"></span>
				</label>
				<p><?php _e('Range Options', 'directorypress-advanced-fields'); ?></p>
				<label class="switch">
					<input name="mode" type="radio" value="range" <?php checked($search_field->mode, 'range'); ?> />
					<span class="slider"></span>
				</label>
				<p><?php _e('Min-Max', 'directorypress-advanced-fields'); ?></p>
				<label class="switch">
					<input name="mode" type="radio" value="min_max" <?php checked($search_field->mode, 'min_max'); ?> />
					<span class="slider"></span>
				</label>
				<p><?php _e('Min-Max Slider', 'directorypress-advanced-fields'); ?></p>
				<label class="switch">
					<input name="mode" type="radio" value="min_max_slider" <?php checked($search_field->mode, 'min_max_slider'); ?> />
					<span class="slider"></span>
				</label>
				<p><?php _e('Range Slider', 'directorypress-advanced-fields'); ?></p>
				<label class="switch">
					<input name="mode" type="radio" value="range_slider" <?php checked($search_field->mode, 'range_slider'); ?> />
					<span class="slider"></span>
				</label>
				<div>
					<?php _e('From:', 'directorypress-advanced-fields'); ?><input type="text" name="slider_step_1_min" size=4 value="<?php echo $search_field->slider_step_1_min; ?>" /> <?php _e('To:', 'directorypress-advanced-fields'); ?><input type="text" name="slider_step_1_max" size=4 value="<?php echo $search_field->slider_step_1_max; ?>" />
				</div>
			</div>
		</div>
		<div class="field-holder">
			<div><label><?php _e('Min-Max options', 'directorypress-advanced-fields'); ?><span class="directorypress-red-asterisk">*</span></label></div>
			<div id="selection_items_wrapper">
				<?php if (count($search_field->min_max_options)): ?>
					<?php foreach ($search_field->min_max_options AS $item): ?>
						<div class="selection_item">
							<input name="min_max_options[]" type="text" size="9" value="<?php echo $item; ?>" />
							<i class="delete_section_item far fa-trash-alt"></i>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="selection_item">
						<input name="min_max_options[]" type="text" size="9"  value="" />
						<i class="delete_section_item far fa-trash-alt"></i>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<input type="button" id="add_selection_item" class="button button-primary" value="<?php esc_attr_e('Add min-max option', 'directorypress-advanced-fields'); ?>" />
		<div class="id">
			<input type="hidden" name="id" value="">
		</div>
	</form>
</div>