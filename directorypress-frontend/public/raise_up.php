<h3>
	<?php echo apply_filters('directorypress_raiseup_option', sprintf(__('Raise up listing "%s"', 'directorypress-frontend'), $directorypress_object->current_listing->title()), $directorypress_object->current_listing); ?>
</h3>

<p><?php _e('Listing will be raised up to the top of all lists, those ordered by date.', 'directorypress-frontend'); ?></p>
<p><?php _e('Note, that listing will not stick on top, so new listings and other listings, those were raised up later, will place higher.', 'directorypress-frontend'); ?></p>

<?php do_action('directorypress_raise_up_html', $directorypress_object->current_listing); ?>

<?php if ($public_handler->action == 'show'): ?>
<a href="<?php echo directorypress_dashboardUrl(array('directorypress_action' => 'raiseup_listing', 'listing_id' => $directorypress_object->current_listing->post->ID, 'raiseup_action' => 'raiseup', 'referer' => urlencode($public_handler->referer))); ?>" class="btn btn-primary"><?php _e('Raise up', 'directorypress-frontend'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $public_handler->referer; ?>" class="btn btn-primary"><?php _e('Cancel', 'directorypress-frontend'); ?></a>
<?php elseif ($public_handler->action == 'raiseup'): ?>
<a href="<?php echo $public_handler->referer; ?>" class="btn btn-primary"><?php _e('Go back ', 'directorypress-frontend'); ?></a>
<?php endif; ?>