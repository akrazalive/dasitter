<div class="directorypress-modal-content wp-clearfix">
	<form class="config" method="POST" action="">
		<?php wp_nonce_field(DIRECTORYPRESS_PATH, 'directorypress_configure_fields_nonce');?>
		<div class="field-holder">
			<div><label><?php _e('Allow custom file title', 'directorypress-advanced-fields'); ?></label></div>
			<div>
				<label class="switch">
					<input id="use_text" name="use_text" type="checkbox" value="1" <?php if($field->use_text) echo 'checked'; ?> />
					<span class="slider"></span>
				</label>
			</div>
		</div>
		<div class="field-holder">
			<div><label><?php _e('Default file title', 'directorypress-advanced-fields'); ?></label></div>
			<div>
				<label class="switch">
					<input id="use_default_text" name="use_default_text" type="checkbox" value="1" <?php if($field->use_default_text) echo 'checked'; ?> />
					<span class="slider"></span>
				</label>
			</div>
		</div>
		<div class="field-holder">
			<div><label><?php _e('Default Title',  'directorypress-advanced-fields'); ?></label></div>
			<div>
				<input class="regular-text" name="default_text" type="text" value="<?php echo esc_attr($field->default_text); ?>" />
			</div>
		</div>
		<div class="field-holder">
			<div><label><?php _e('Allowed mime types', 'directorypress-advanced-fields'); ?></label></div>
			<div>
				<?php foreach ($field->get_mime_types() AS $type=>$label): ?>
					<p><?php echo $label['label']; ?> (<?php echo $type; ?>)</p>
					<label class="switch">
						<input name="allowed_mime_types[]" type="checkbox" value="<?php echo $type; ?>" <?php if (in_array($type, $field->allowed_mime_types)) echo 'checked'; ?> />
						<span class="slider"></span>
					</label>
				<?php endforeach; ?>	
			</div>
		</div>
		<div class="id">
			<input type="hidden" name="id" value="">
		</div>
	</form>
</div>