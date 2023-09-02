<h3>
	<?php echo apply_filters('directorypress_renew_option', sprintf(__('Renew listing "%s"', 'directorypress-frontend'), $directorypress_object->current_listing->title()), $directorypress_object->current_listing); ?>
</h3>

<p><?php _e('Listing will be renewed and raised up to the top of all lists, those ordered by date.', 'directorypress-frontend'); ?></p>

<?php do_action('directorypress_renew_html', $directorypress_object->current_listing); ?>

<?php if ($public_handler->action == 'show'): ?>
<a href="<?php echo directorypress_dashboardUrl(array('directorypress_action' => 'renew_listing', 'listing_id' => $directorypress_object->current_listing->post->ID, 'renew_action' => 'renew', 'referer' => urlencode($public_handler->referer))); ?>" class="btn btn-primary"><?php _e('Renew listing', 'directorypress-frontend'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $public_handler->referer; ?>" class="btn btn-primary"><?php _e('Cancel', 'directorypress-frontend'); ?></a>
<?php elseif ($public_handler->action == 'renew'): ?>
<a href="<?php echo $public_handler->referer; ?>" class="btn btn-primary"><?php _e('Go back ', 'directorypress-frontend'); ?></a>
<?php endif; ?>