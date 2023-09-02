<div class="directorypress-modal-content wp-clearfix">
	<form class="config" method="POST" action="">
		<?php wp_nonce_field(DIRECTORYPRESS_PATH, 'directorypress_configure_fields_nonce'); ?>
		<div class="field-holder">
			<div><label for="is_multiselect"><?php _e('Is Multi Select Field?',  'DIRECTORYPRESS'); ?></label></div>
			<div>
				<label class="switch">
					<input id="is_multiselect" name="is_multiselect" type="checkbox" value="1" <?php checked(1, $field->is_multiselect); ?> />
					<span class="slider"></span>
				</label>
				<p class="description"><?php _e("If turned On => submit listing form would have multiple category selection option", 'DIRECTORYPRESS'); ?></p>
			</div>
		</div>
		<div class="id">
			<input type="hidden" name="id" value="">
		</div>
	</form>
</div>