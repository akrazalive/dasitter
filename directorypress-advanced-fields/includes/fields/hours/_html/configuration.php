<div class="directorypress-modal-content wp-clearfix">
	<form class="config" method="POST" action="">
		<?php wp_nonce_field(DIRECTORYPRESS_PATH, 'directorypress_configure_fields_nonce');?>
		<div class="field-holder">
			<div><label><?php _e('Time Interval', 'directorypress-advanced-fields'); ?></label></div>
			<div>
				<select name="time_interval">
					<?php for ($i = 5; $i <= 60; $i+=5): ?>
							
						<option value="<?php echo esc_attr($i); ?>" <?php if($field->time_interval == $i): echo 'selected'; endif; ?>><?php echo esc_attr($i); ?> Minutes</option>
					<?php endfor; ?>
				</select>
			</div>
		</div>
		<div class="id">
			<input type="hidden" name="id" value="">
		</div>
	</form>
</div>