<h3>
	<?php printf(__('Approve or decline claim of listing "%s"', 'directorypress-frontend'), $directorypress_object->current_listing->title()); ?>
</h3>

<?php if ($public_handler->action == 'show'): ?>
<p><?php printf(__('User "%s" had claimed this listing.', 'directorypress-frontend'), $directorypress_object->current_listing->claim->claimer->display_name); ?></p>
<?php if ($directorypress_object->current_listing->claim->claimer_message): ?>
<p><?php _e('Message from claimer:', 'directorypress-frontend'); ?><br /><i><?php echo $directorypress_object->current_listing->claim->claimer_message; ?></i></p>
<?php endif; ?>
<p><?php _e('In case of approval new owner will receive email notification.', 'directorypress-frontend'); ?></p>

<a href="<?php echo directorypress_dashboardUrl(array('directorypress_action' => 'process_claim', 'listing_id' => $directorypress_object->current_listing->post->ID, 'claim_action' => 'approve', 'referer' => urlencode($public_handler->referer))); ?>" class="btn btn-primary"><?php _e('Approve', 'directorypress-frontend'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo directorypress_dashboardUrl(array('directorypress_action' => 'process_claim', 'listing_id' => $directorypress_object->current_listing->post->ID, 'claim_action' => 'decline', 'referer' => urlencode($public_handler->referer))); ?>" class="btn btn-primary"><?php _e('Decline', 'directorypress-frontend'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $public_handler->referer; ?>" class="btn btn-primary"><?php _e('Cancel', 'directorypress-frontend'); ?></a>
<?php elseif ($public_handler->action == 'processed'): ?>
<a href="<?php echo $public_handler->referer; ?>" class="btn btn-primary"><?php _e('Go back ', 'directorypress-frontend'); ?></a>
<?php endif; ?>