<div class="field-wrap field-input-item field-id-<?php echo $field->id; ?> field-type-<?php echo $field->type; ?> clearfix">
	<p class="directorypress-submit-field-title">
		<?php echo $field->name; ?>
		<?php do_action('directorypress_listing_submit_required_lable', $field); ?>
		<?php do_action('directorypress_listing_submit_user_info', $field->description); ?>
		<?php do_action('directorypress_listing_submit_admin_info', 'listing_field_textarea'); ?>
	</p>
	<div class="textarea-field-wrap">
		<?php if ($field->html_editor): ?>
			<?php wp_editor($field->value, 'directorypress-field-input-'.$field->id, array('media_buttons' => false, 'editor_class' => 'directorypress-editor-class')); ?>
		<?php else: ?>
			<textarea name="directorypress-field-input-<?php echo $field->id; ?>" class="form-control" rows="5"><?php echo esc_textarea($field->value); ?></textarea>
		<?php endif; ?>
	</div>
</div>