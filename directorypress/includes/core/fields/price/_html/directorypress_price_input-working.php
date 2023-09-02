<div class="directorypress-field directorypress-field-input-block directorypress-field-input-block-<?php echo $field->id; ?>">
	<label class=" directorypress-control-label directorypress-submit-field-title"><?php echo $field->name; ?> <?php echo $field->currency_symbol; ?><?php if ($field->is_this_field_requirable() && $field->is_required): ?><span class="directorypress-red-asterisk">*</span><?php endif; ?></label>
	<div class="">
		<input type="text" name="directorypress-field-input-<?php echo $field->id; ?>" class="directorypress-field-input-price form-control" value="<?php echo esc_attr($field->value['price_start']); ?>" size="4" />
		<input type="text" name="directorypress-field-input-<?php echo $field->id; ?>-end" class="directorypress-field-input-price form-control" value="<?php echo esc_attr($field->value['price_end']); ?>" size="4" />
		
		<?php if ($field->description): ?><p class="description"><?php echo $field->description; ?></p><?php endif; ?>
	</div>
	<?php if (count($field->range_options)): ?>
	<div class="">
		<select name="directorypress-field-input-<?php echo $field->id; ?>-range" class="directorypress-field-input-select form-control directorypress-select2">
			<option value=""><?php printf(__('- Select %s -', 'DIRECTORYPRESS'), $field->name); ?></option>
			<?php foreach ($field->range_options AS $key=>$item): ?>
			<option value="<?php echo esc_attr($key); ?>" <?php selected($field->value['price_range'], $key, true); ?>><?php echo $item; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
<?php endif; ?>
</div>