<?php 
global $wpdb, $pmpro_msg, $pmpro_msgt, $pmpro_packages, $current_user;
if($pmpro_msg)
{
?>
<div class="pmpro_message <?php echo $pmpro_msgt?>"><?php echo $pmpro_msg?></div>
<?php
}
?>
<div class="directorypress-content">
	<div class="directorypress-submit-section-adv ">
		<?php $max_columns_in_row = 3; ?>
		<?php $packages_counter = count($pmpro_packages); ?>
		<?php if ($packages_counter > $max_columns_in_row) $packages_counter = $max_columns_in_row; ?>
		<?php $cols_width = floor(12/$packages_counter); ?>
		<?php $cols_width_percents = (100-1)/$packages_counter; ?>

		<?php $counter = 0; ?>
		<?php $tcounter = 0; ?>
		<?php foreach ($pmpro_packages AS $pmpro_package): ?>
		<?php
			if(isset($current_user->membership_package->ID))
				$current_package = ($current_user->membership_package->ID == $pmpro_package->id);
			else
				$current_package = false;
		?>
		<?php $tcounter++; ?>
		<?php if ($counter == 0): ?>
		<div class="row" style="text-align: center;">
		<?php endif; ?>

			<div class="col-sm-<?php echo $cols_width; ?> directorypress-plan-column" style="width: <?php echo $cols_width_percents; ?>%;">
				<div class="directorypress-panel directorypress-panel-default directorypress-text-center directorypress-choose-plan">
					<div class="directorypress-panel-heading <?php if ($current_package && (pmpro_isLevelRecurring($current_user->membership_package) || empty($current_user->membership_package->enddate))): ?>directorypress-has_featured<?php endif; ?>">
						<h3>
							<?php echo $pmpro_package->name; ?>
						</h3>
						<?php if ($pmpro_package->description): ?><a class="directorypress-hint-icon" href="javascript:void(0);" data-content="<?php echo esc_attr(nl2br($pmpro_package->description)); ?>" data-html="true" rel="popover" data-placement="bottom" data-trigger="hover"></a><?php endif; ?>
					</div>
					<ul class="directorypress-list-group">
						<li class="directorypress-list-group-item">
							<?php
							if(pmpro_isLevelFree($pmpro_package))
								$cost_text = '<span class="directorypress-price directorypress-payments-free">' . __('0', 'directorypress-frontend') . '</span>';
								
							else
								$cost_text = pmpro_getLevelCost($pmpro_package, true, true);
	 
							$expiration_text = pmpro_getLevelExpiration($pmpro_package);
							if(!empty($cost_text) && !empty($expiration_text))
								echo $cost_text . "<br />" . $expiration_text;
							elseif(!empty($cost_text))
								echo $cost_text;
							elseif(!empty($expiration_text))
								echo $expiration_text;
							?>
						</li>
						<?php foreach ($directorypress_object->packages->packages_array AS $directorypress_package): ?>
						<li class="directorypress-list-group-item">
							<?php echo $directorypress_package->name; ?> <?php _e('listings', 'directorypress-frontend'); ?>:
							<strong><?php echo getPMPROlistingsNumberByLevel($pmpro_package->id, $directorypress_package->id); ?></strong>
							<a class="directorypress-hint-icon" href="javascript:void(0);" data-content="<?php echo esc_attr('
								<div class="directorypress-panel directorypress-panel-default directorypress-text-center directorypress-choose-plan">
									<div class="directorypress-panel-heading ' . (($directorypress_package->has_featured) ? 'directorypress-has_featured' : '') . '">
										<h3>' . $directorypress_package->name . '</h3>
									</div>
									<ul class="directorypress-list-group">
									<li class="directorypress-list-group-item">
										'. __('Active period', 'directorypress-frontend') .':
										'. $directorypress_package->get_active_duration_string() . '
									</li>
									' . directorypress_display_template(array(DPFL_TEMPLATES_PATH, 'package_details.tpl.php'), array('args' => array('show_period' => 0,'show_has_sticky' => 1,'show_has_featured' => 1,'show_categories' => 1,'show_locations' => 1,'show_maps' => 1,'show_images' => 1,'show_videos' => 1,'columns_same_height' => 1,), 'package' => $directorypress_package), true) . '
									</ul>
								</div>'); ?>" data-html="true" rel="popover" data-placement="right" data-trigger="hover"></a>
						</li>
						<?php endforeach; ?>
						<li class="directorypress-list-group-item">
							<?php if (empty($current_user->membership_package->ID)): ?>
								<a class="btn btn-primary" href="<?php echo pmpro_url("checkout", "?package=" . $pmpro_package->id, "https")?>"><?php _e('Select', 'directorypress-frontend');?></a>
							<?php elseif (!$current_package): ?>                	
								<a class="btn btn-primary" href="<?php echo pmpro_url("checkout", "?package=" . $pmpro_package->id, "https")?>"><?php _e('Select', 'directorypress-frontend');?></a>
							<?php elseif($current_package): ?>
								<?php
								//if it's a one-time-payment package, offer a link to renew				
								if (!pmpro_isLevelRecurring($current_user->membership_package) && !empty($current_user->membership_package->enddate)): ?>
									<a class="btn btn-primary" href="<?php echo pmpro_url("checkout", "?package=" . $pmpro_package->id, "https")?>"><?php _e('Renew', 'directorypress-frontend');?></a>
								<?php else: ?>
									<a class="btn btn-primary directorypress-disabled" href="<?php echo pmpro_url("account")?>"><?php _e('Your Level', 'directorypress-frontend');?></a>
								<?php endif; ?>
							<?php endif; ?>
						</li>
					</ul>
				</div>          
			</div>

		<?php $counter++; ?>
		<?php if ($counter == $max_columns_in_row || $tcounter == $packages_counter): ?>
		</div>
		<?php endif; ?>
		<?php if ($counter == $max_columns_in_row) $counter = 0; ?>
		<?php endforeach; ?>
	</div>
</div>

<nav id="nav-below" class="navigation" role="navigation">
	<div class="nav-previous alignleft">
		<?php if(!empty($current_user->membership_package->ID)) { ?>
			<a href="<?php echo pmpro_url("account")?>"><?php _e('&larr; Return to Your Account', 'pmpro');?></a>
		<?php } else { ?>
			<a href="<?php echo home_url('/')?>"><?php _e('&larr; Return to Home', 'pmpro');?></a>
		<?php } ?>
	</div>
</nav>
