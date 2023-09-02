<?php if (count($field->selection_items)): ?>
<div class="field-wrap field-input-item field-id-<?php echo $field->id; ?> field-type-<?php echo $field->type; ?> clearfix">
	<p class="directorypress-submit-field-title">
		<?php echo $field->name; ?>
		<?php do_action('directorypress_listing_submit_required_lable', $field); ?>
		<?php do_action('directorypress_listing_submit_user_info', $field->description); ?>
		<?php do_action('directorypress_listing_submit_admin_info', 'listing_field_color'); ?>
	</p>
	<div class="input-checkbox-wrap clearfix">
		<?php foreach ($field->selection_items AS $key=>$item): ?>
		<div class="input-checkbox">
			<label>
				<input type="checkbox" name="directorypress-field-input-<?php echo $field->id; ?>[]" class="directorypress-field-input-checkbox" value="<?php echo esc_attr($key); ?>" <?php if (in_array($key, $field->value)) echo 'checked'; ?> />
				<span class="checkbox-item-name">
					<span style=" background:<?php echo $field->color_codes[$key]; ?>;width:15px;height:15px;display:inline-block;margin-right:5px;vertical-align:middle;border-radius:50%;"></span>
					<?php echo $item; ?>
				</span>
				<span class="input-checkbox-item"></span>
			</label>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>