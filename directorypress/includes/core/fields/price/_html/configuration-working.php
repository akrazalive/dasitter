<div class="wrap about-wrap directorypress-admin-wrap">
	<?php DirectoryPress_Admin_Panel::listing_dashboard_header(); ?>
	<div class="directorypress-plugins directorypress-theme-browser-wrap">
		<div class="theme-browser rendered">
			<div class="directorypress-box">
				<div class="directorypress-box-head">
					<?php _e('Configure price field', 'DIRECTORYPRESS'); ?>
				</div>
				<div class="directorypress-box-content wp-clearfix">

<div class="directorypress-configuration-page-wrap">
<script>
	(function($) {
		"use strict";
	
		$(function() {
			$("#add_selection_item").click(function() {
				$("#selection_items_wrapper").append('<div class="selection_item"><input name="range_options[]" type="text" size="40" value="" /><span class="directorypress-delete-selection-item directorypress-icon-remove" title="<?php esc_attr_e('Remove min-max option', 'DIRECTORYPRESS')?>"></span></div>');
			});
			$(document).on("click", ".directorypress-delete-selection-item", function() {
				$(this).parent().remove();
			});
		});
	})(jQuery);
</script>
<form method="POST" action="">
	<?php wp_nonce_field(DIRECTORYPRESS_PATH, 'directorypress_configure_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php _e('Currency symbol', 'DIRECTORYPRESS'); ?><span class="directorypress-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="currency_symbol"
						type="text"
						size="1"
						value="<?php echo esc_attr($field->currency_symbol); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Currency symbol position', 'DIRECTORYPRESS'); ?></label>
				</th>
				<td>
					<select name="symbol_position">
						<option value="1" <?php if($field->symbol_position == '1') echo 'selected'; ?>>$1.00</option>
						<option value="2" <?php if($field->symbol_position == '2') echo 'selected'; ?>>$ 1.00</option>
						<option value="3" <?php if($field->symbol_position == '3') echo 'selected'; ?>>1.00$</option>
						<option value="4" <?php if($field->symbol_position == '4') echo 'selected'; ?>>1.00 $</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Decimal separator', 'DIRECTORYPRESS'); ?></label>
				</th>
				<td>
					<select name="decimal_separator">
						<option value="." <?php if($field->decimal_separator == '.') echo 'selected'; ?>><?php _e('dot', 'DIRECTORYPRESS')?></option>
						<option value="," <?php if($field->decimal_separator == ',') echo 'selected'; ?>><?php _e('comma', 'DIRECTORYPRESS')?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Hide decimals', 'DIRECTORYPRESS'); ?></label>
				</th>
				<td>
					<select name="hide_decimals">
						<option value="0" <?php if($field->hide_decimals == '0') echo 'selected'; ?>><?php _e('no', 'DIRECTORYPRESS')?></option>
						<option value="1" <?php if($field->hide_decimals == '1') echo 'selected'; ?>><?php _e('yes', 'DIRECTORYPRESS')?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Thousands separator', 'DIRECTORYPRESS'); ?></label>
				</th>
				<td>
					<select name="thousands_separator">
						<option value="" <?php if($field->thousands_separator == '') echo 'selected'; ?>><?php _e('no separator', 'DIRECTORYPRESS')?></option>
						<option value="." <?php if($field->thousands_separator == '.') echo 'selected'; ?>><?php _e('dot', 'DIRECTORYPRESS')?></option>
						<option value="," <?php if($field->thousands_separator == ',') echo 'selected'; ?>><?php _e('comma', 'DIRECTORYPRESS')?></option>
						<option value=" " <?php if($field->thousands_separator == ' ') echo 'selected'; ?>><?php _e('space', 'DIRECTORYPRESS')?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Price Range options:', 'DIRECTORYPRESS'); ?>
				</th>
				<td>
					<div id="selection_items_wrapper">
						<?php if (count($field->range_options)): ?>
						<?php foreach ($field->range_options AS $item): ?>
						<div class="selection_item clearfix">
							<input
								name="range_options[]"
								type="text"
								size="40"
								value="<?php echo $item; ?>" />
							<span class="directorypress-delete-selection-item directorypress-icon-remove" title="<?php esc_attr_e('Remove min-max option', 'DIRECTORYPRESS')?>"></span>
						</div>
						<?php endforeach; ?>
						<?php else: ?>
						<div class="selection_item clearfix">
							<input
								name="range_options[]"
								type="text"
								size="40"
								value="" />
							<span class="directorypress-delete-selection-item directorypress-icon-remove" title="<?php esc_attr_e('Remove min-max option', 'DIRECTORYPRESS')?>"></span>
						</div>
						<?php endif; ?>
					</div>
				</td>
			</tr>
		</tbody>
		<input type="button" id="add_selection_item" class="button button-primary" value="<?php esc_attr_e('Add Range option', 'DIRECTORYPRESS'); ?>" />
	</table>
	
	<?php submit_button(__('Save changes', 'DIRECTORYPRESS')); ?>
</form>
</div>

</div>
</div>
</div>
</div>
</div>