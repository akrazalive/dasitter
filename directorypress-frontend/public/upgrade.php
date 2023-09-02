<h3>
	<?php echo sprintf(__('Change package of listing "%s"', 'directorypress-frontend'), $directorypress_object->current_listing->title()); ?>
</h3>

<p><?php _e('The package of listing will be changed. You may upgrade or downgrade the package. If new package has an option of limited active period - expiration date of listing will be reassigned automatically.', 'directorypress-frontend'); ?></p>

<form action="<?php echo directorypress_dashboardUrl(array('directorypress_action' => 'upgrade_listing', 'listing_id' => $directorypress_object->current_listing->post->ID, 'upgrade_action' => 'upgrade', 'referer' => urlencode($public_handler->referer))); ?>" method="POST">
	<?php if ($public_handler->action == 'show'): ?>
	<h3><?php _e('Choose new package', 'directorypress-frontend'); ?></h3>
	<?php foreach ($directorypress_object->packages->packages_array AS $package): ?>
	<?php if ($directorypress_object->current_listing->package->id != $package->id && (!isset($directorypress_object->current_listing->package->upgrade_meta[$package->id]) || !$directorypress_object->current_listing->package->upgrade_meta[$package->id]['disabled'])): ?>
	<p>
		<label><input type="radio" name="new_package_id" value="<?php echo $package->id; ?>" /> <?php echo apply_filters('directorypress_package_upgrade_option', $package->name, $directorypress_object->current_listing->package, $package); ?></label>
	</p>
	<?php endif; ?>
	<?php endforeach; ?>
	
	<br />
	<br />
	<input type="submit" value="<?php esc_attr_e('Change package', 'directorypress-frontend'); ?>" class="btn btn-primary" id="submit" name="submit">
	&nbsp;&nbsp;&nbsp;
	<a href="<?php echo $public_handler->referer; ?>" class="btn btn-primary"><?php _e('Cancel', 'directorypress-frontend'); ?></a>
	<?php elseif ($public_handler->action == 'upgrade'): ?>
	<a href="<?php echo $public_handler->referer; ?>" class="btn btn-primary"><?php _e('Go back ', 'directorypress-frontend'); ?></a>
	<?php endif; ?>
</form>