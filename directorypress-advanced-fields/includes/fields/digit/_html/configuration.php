<div class="directorypress-modal-content wp-clearfix">
	<form class="config" method="POST" action="">
		<?php wp_nonce_field(DIRECTORYPRESS_PATH, 'directorypress_configure_fields_nonce');?>
		<div class="field-holder">
			<div><label><?php _e('Select Value Type', 'directorypress-advanced-fields'); ?></label></div>
			<div>
				<p><?php _e('integer', 'directorypress-advanced-fields')?></p>
				<label class="switch">
				<input name="is_integer" type="radio" value="1" <?php if($field->is_integer) echo 'checked'; ?> />
					<span class="slider"></span>
				</label>
				<p><?php _e('decimal', 'directorypress-advanced-fields')?></p>
				<label class="switch">
				<input name="is_integer" type="radio" value="0" <?php if(!$field->is_integer) echo 'checked'; ?> />
					<span class="slider"></span>
				</label>
			</div>
		</div>
		<div class="field-holder">
			<div><label><?php _e('Decimal separator', 'directorypress-advanced-fields'); ?></label></div>
			<div>
				<select name="decimal_separator">
					<option value="." <?php if($field->decimal_separator == '.') echo 'selected'; ?>><?php _e('dot', 'directorypress-advanced-fields')?></option>
					<option value="," <?php if($field->decimal_separator == ',') echo 'selected'; ?>><?php _e('comma', 'directorypress-advanced-fields')?></option>
				</select>
			</div>
		</div>
		<div class="field-holder">
			<div><label><?php _e('Thousands separator', 'directorypress-advanced-fields'); ?></label></div>
			<div>
				<select name="thousands_separator">
					<option value="" <?php if($field->thousands_separator == '') echo 'selected'; ?>><?php _e('no separator', 'directorypress-advanced-fields')?></option>
					<option value="." <?php if($field->thousands_separator == '.') echo 'selected'; ?>><?php _e('dot', 'directorypress-advanced-fields')?></option>
					<option value="," <?php if($field->thousands_separator == ',') echo 'selected'; ?>><?php _e('comma', 'directorypress-advanced-fields')?></option>
					<option value=" " <?php if($field->thousands_separator == ' ') echo 'selected'; ?>><?php _e('space', 'directorypress-advanced-fields')?></option>
				</select>
			</div>
		</div>
		<div class="field-holder">
			<div><label><?php _e('Minimum Length (optional)', 'directorypress-advanced-fields'); ?></label></div>
			<div>
				<input name="min" type="text" size="2" value="<?php echo esc_attr($field->min); ?>" />
			</div>
		</div>
		<div class="field-holder">
			<div><label><?php _e('Maximum Length (optional)', 'directorypress-advanced-fields'); ?></label></div>
			<div>
				<input name="max" type="text" size="2" value="<?php echo esc_attr($field->max); ?>" />
			</div>
		</div>
		<div class="id">
			<input type="hidden" name="id" value="">
		</div>
	</form>
</div>