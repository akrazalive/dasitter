<?php if (count($field->selection_items)): ?>
<div class="field-wrap field-input-item field-id-<?php echo $field->id; ?> field-type-<?php echo $field->type; ?> clearfix">
	<p class="directorypress-submit-field-title">
		<?php echo $field->name; ?>
		<?php do_action('directorypress_listing_submit_required_lable', $field); ?>
		<?php do_action('directorypress_listing_submit_user_info', $field->description); ?>
		<?php do_action('directorypress_listing_submit_admin_info', 'listing_field_checkbox'); ?>
	</p>
	<div class="input-checkbox-wrap clearfix">
		<?php foreach ($field->selection_items AS $key=>$item): ?>
		<div class="input-checkbox">
			<label>
				<input type="checkbox" name="directorypress-field-input-<?php echo $field->id; ?>[]" value="<?php echo esc_attr($key); ?>" <?php if (in_array($key, $field->value)) echo 'checked'; ?> />
				<span class="checkbox-item-name"><?php echo $item; ?></span>
				<span class="input-checkbox-item"></span>
			</label>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>