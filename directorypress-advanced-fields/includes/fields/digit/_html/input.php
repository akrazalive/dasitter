<div class="field-wrap field-input-item field-id-<?php echo $field->id; ?> field-type-<?php echo $field->type; ?> clearfix">
	<p class="directorypress-submit-field-title">
		<?php echo $field->name; ?>
		<?php do_action('directorypress_listing_submit_required_lable', $field); ?>
		<?php do_action('directorypress_listing_submit_user_info', $field->description); ?>
		<?php do_action('directorypress_listing_submit_admin_info', 'listing_field_digit'); ?>
	</p>
	<input type="text" name="directorypress-field-input-<?php echo $field->id; ?>" class="form-control" value="<?php echo esc_attr($field->value); ?>" size="4" />
</div>