<?php 
/**
 * @since      3.0.0
 * @package    DirectoryPress
 * @subpackage DirectoryPress/includes/core/fields/email/_html
 * @author     DirectoryPress <team@directorypress.co>
 */
?>
<div class="field-wrap field-input-item field-id-<?php echo $field->id; ?> field-type-<?php echo $field->type; ?>">
	<p class="directorypress-submit-field-title">
		<?php echo $field->name; ?>
		<?php do_action('directorypress_listing_submit_required_lable', $field); ?>
		<?php do_action('directorypress_listing_submit_user_info', $field->description); ?>
		<?php do_action('directorypress_listing_submit_admin_info', 'listing_field_email'); ?>
	</p>
	<div class="input-group">
		<span class="input-group-addon"><i class="fas fa-at"></i></span>
		<input type="text" name="directorypress-field-input-<?php echo $field->id; ?>" class="form-control" placeholder="<?php _e('Provide Email Address', 'DIRECTORYPRESS'); ?>" value="<?php echo esc_attr($field->value); ?>" />
	</div>
</div>