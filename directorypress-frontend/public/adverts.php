<?php global $DIRECTORYPRESS_ADIMN_SETTINGS, $directorypress_object;  ?>
	<?php


  echo'<div class="row listing-counts-wrap clearfix">';
		echo'<div class="total-listing-count col-lg-3 col-md-6 col-sm-6 col-xs-12">';
			echo'<div class="total-listing-count-item total">';
				echo '<span class="listing-number">'.$public_handler->listings_count.'</span>';
				echo '<span class="listing-conut-text">'.esc_html__('Total Listing', 'directorypress-frontend').'</span>';
			echo'</div>';
		echo'</div>';
		echo'<div class="total-listing-count col-lg-3 col-md-6 col-sm-6 col-xs-12">';
			echo'<div class="total-listing-count-item active">';
				echo '<span class="listing-number">'.$public_handler->listings_count2.'</span>';
				echo '<span class="listing-conut-text">'.esc_html__('Active Listing', 'directorypress-frontend').'</span>';
			echo'</div>';
		echo'</div>';
		echo'<div class="total-listing-count col-lg-3 col-md-6 col-sm-6 col-xs-12">';
			echo'<div class="total-listing-count-item expired">';
				echo '<span class="listing-number">'.$public_handler->listings_count3.'</span>';
				echo '<span class="listing-conut-text">'.esc_html__('Expired Listing', 'directorypress-frontend').'</span>';
			echo'</div>';
		echo'</div>';
		echo'<div class="total-listing-count col-lg-3 col-md-6 col-sm-6 col-xs-12">';
			echo'<div class="total-listing-count-item pending">';
				echo '<span class="listing-number">'.$public_handler->listings_count4.'</span>';
				echo '<span class="listing-conut-text">'.esc_html__('Pending Approval', 'directorypress-frontend').'</span>';
			echo'</div>';
		echo'</div>';
	echo'</div>';

	?>
	
	
	
	<?php if ($public_handler->listings): ?>
		<div class="directorypress-table directorypress-table-striped clearfix">
			<div class="dashboard-listing-header"><?php echo esc_html__('Your Listings', 'directorypress-frontend'); ?></div>
			<div id="panel-listing-ajax-response"></div>
			<div class="row clearfix">
				<?php 
				while ($public_handler->query->have_posts()):
						$public_handler->query->the_post();
							global $directorypress_object;
							$listing = $public_handler->listings[get_the_ID()]; 
							
							if(isset($listing->logo_image) && !empty($listing->logo_image)){
								$image_src_array = wp_get_attachment_image_src($listing->logo_image, 'full');
								$image_src = $image_src_array[0];
							}elseif(isset($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_nologo_url']['url']) && !empty($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_nologo_url']['url'])){
								$image_src_array = $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_nologo_url']['url'];
								$image_src = $image_src_array;
							}else{
								$image_src = DIRECTORYPRESS_RESOURCES_URL.'images/no-thumbnail.jpg';
							}
							
							//$image_src_array = wp_get_attachment_image_src($listing->logo_image, 'full');
							$params = array( 'width' => 60, 'height' => 60, 'crop' => true );
							$params_mobile = array( 'width' => 480, 'height' => 250, 'crop' => false );
							$param = array(
								'width' => 60,
								'height' => 60,
								'crop' => true
							);
				?>
					<div class="dashboard-item-wrap col-md-12">
						<div class="dashboard-listing-wrapper clearfix">	
							<div class="dashboard-listings-thumb desktop"><img src="<?php echo bfi_thumb($image_src, $params); ?>" alt="listing logo" width="60" height="60"/></div>
							<div class="dashboard-listings-thumb mobile"><img src="<?php echo bfi_thumb($image_src, $params_mobile); ?>" alt="listing logo"/></div>
							<div class="dashboard-listings-title">
								<div class="dashboard-listings-status">
									<?php
									if ($listing->post->post_status == 'pending'){
										echo '<span class="label label-default">' . __('Pending Approval', 'directorypress-frontend').'</span>';
									}
									if ($listing->status == 'active'){
										echo '<span class="label label-success">' . __('Active', 'directorypress-frontend') . '</span>';
									}elseif ($listing->status == 'expired'){
										echo '<span class="label label-danger">' . __('Expired', 'directorypress-frontend') . '</span>';
									}elseif ($listing->status == 'unpaid'){
										echo '<span class="label label-warning">' . __('Unpaid', 'directorypress-frontend') .' &#124; <a href="'. get_permalink( get_option('woocommerce_myaccount_page_id') ).'/'. get_option( 'woocommerce_myaccount_orders_endpoint' ) .'">'. __('Pay now', 'directorypress-frontend') . '</a></span>';
										echo '<span class=""></span>';
									}elseif ($listing->status == 'stopped'){
										echo '<span class="label label-danger">' . __('Stopped', 'directorypress-frontend') . '</span>';
									}
									
									do_action('directorypress_listing_status_option', $listing);
										
									
									?>
								</div>
								<h5>
									<?php
										if (directorypress_user_permission_to_edit_listing($listing->post->ID))
											echo '<a href="' . directorypress_edit_post_url($listing->post->ID) . '">' . $listing->title() . '</a>';
										else
											echo $listing->title();
										do_action('directorypress_dashboard_listing_title', $listing);
									?>
								</h5>
							</div>
							<?php do_action('directorypress-dashboard-listing-after-title-html', $listing); ?>
							<div class="dashboard-listings-expiry">
								<div class="expiry-label"><?php echo esc_html__('Expiry', 'directorypress-frontend') ?></div>
								<div class="expiry-value">
									<?php
										if ($listing->package->package_no_expiry){
											_e('No Expiry', 'directorypress-frontend');
										}elseif ($listing->expiration_date > time()){
											echo human_time_diff(time(), $listing->expiration_date) . '&nbsp;' . __('left', 'directorypress-frontend');
										}elseif ($listing->expiration_date < time()){
											_e('Expired', 'directorypress-frontend');
										}
									?>
								</div>
							</div>
							<?php do_action('directorypress-dashboard-listing-after-expiry-html', $listing); ?>
							<div class="dashboard-listings-id">
								<div class="listing-id-label"><?php echo esc_html__('Listing ID', 'directorypress-frontend') ?></div>
								<div class="listing-id"><?php echo $listing->post->ID; ?></div>
							</div>
							<?php do_action('directorypress-dashboard-listing-after-id-html', $listing); ?>
							<div class="dashboard-listing-settings clearfix">
								<div class="listing-setting-label"><?php echo esc_html__('Settings', 'directorypress-frontend') ?></div>
								<div class="listing-setting">
									<?php if (directorypress_user_permission_to_edit_listing($listing->post->ID)): ?>
										<div class="dropdown show">
											<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<?php echo esc_html__('Configure', 'directorypress-frontend'); ?>
											</a>
											<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
												<a href="<?php echo directorypress_edit_post_url($listing->post->ID); ?>" class=""><span class="fas fa-edit"></span><?php esc_attr_e('Edit Listing', 'directorypress-frontend'); ?></a>
												<?php
													if ($listing->status == 'expired') {
														echo '<a class="listing_setting_action_link" data-modal-button-text="'.esc_attr__('Renew', 'directorypress-frontend').'" data-modal-class="listing_renew_modal" data-listing-id="'.$listing->post->ID.'" data-toggle="modal" data-target="#listing_action_modal" href="#"><span class="fas fa-sync"></span>' . esc_html__('Renew', 'directorypress-frontend') . '</a>';
													}
												?>
												<?php 
													if ($listing->package->is_upgradable()){
														echo '<a href="#" class="listing_setting_action_link" data-modal-button-text="'.esc_attr__('Change', 'directorypress-frontend').'" data-modal-title="'.esc_attr__('Change Listing Package', 'directorypress-frontend').'" data-modal-class="listing_change_package_modal" data-listing-id="'. $listing->post->ID .'" data-toggle="modal" data-target="#listing_action_modal"><span class="fas fa-file-upload"></span>'. esc_html__('Upgrade', 'directorypress-frontend').'</a>';
													}
												?>
												<?php
													if ($listing->package->can_be_bumpup && $listing->status == 'active' && $listing->post->post_status == 'publish') {
														echo '<a class="listing_setting_action_link" data-modal-button-text="'.esc_attr__('Boost', 'directorypress-frontend').'" data-modal-title="'.esc_attr__('Boost Your Listing', 'directorypress-frontend').'" data-modal-class="listing_bumpup_modal" data-listing-id="'.$listing->post->ID.'" data-toggle="modal" data-target="#listing_action_modal" href="#"><span class="fab fa-bootstrap"></span>' . esc_html__('BumpUp To Top', 'directorypress-frontend') . '</a>';
													}
												?>
												<?php
													if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_stats']) {
														echo '<a href="#" class="listing_setting_action_link" data-modal-button-text="'.esc_attr__('Change', 'directorypress-frontend').'" data-modal-title="'.esc_attr__('Listing Performance Status', 'directorypress-frontend').'" data-modal-class="listing_performance_modal" data-listing-id="'.$listing->post->ID.'" data-toggle="modal" data-target="#listing_action_modal"><span class="fas fa-signal"></span>' . esc_html__('Performance', 'directorypress-frontend') . '</a>';
													}
												?>
												<?php
													if ($listing->status == 'active' && $listing->post->post_status == 'publish') {
														echo '<a href="' . get_permalink($listing->post->ID) . '"><span class="fas fa-eye"></span>' . esc_attr__('Preview', 'directorypress-frontend') . '</a>';
													}
												?>
												<?php 
													$status_btn = (get_post_status ($listing->post->ID) == 'publish')? esc_html__('Make Private', 'directorypress-frontend'): esc_attr__('Publish', 'directorypress-frontend');
													echo '<a href="#" class="listing_setting_action_link" data-modal-button-text="'.$status_btn.'" data-modal-title="'.esc_attr__('Make Listing Publish or Private', 'directorypress-frontend').'" data-modal-class="change_listing_status_modal" data-listing-id="'.$listing->post->ID.'" data-toggle="modal" data-target="#listing_action_modal"><span class="fab fa-adversal"></span>'. esc_html__('Publish/Private', 'directorypress-frontend').'</a>';
												?>
												<?php
													if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_note_to_admin']) {
														echo '<a href="#" class="listing_setting_action_link" data-modal-button-text="'.esc_attr__('Send', 'directorypress-frontend').'" data-modal-title="'.esc_attr__('Send A Note To Admin', 'directorypress-frontend').'" data-modal-class="listing_admin_notice_modal" data-listing-id="'. $listing->post->ID .'" data-toggle="modal" data-target="#listing_action_modal"><span class="fas fa-clipboard"></span>'. esc_html__('Note To Admin', 'directorypress-frontend').'</a>';
													}
												?>
												<?php
													global $sitepress;
													if (function_exists('wpml_object_id_filter') && $sitepress && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_frontend_translations'] && ($languages = $sitepress->get_active_languages()) && count($languages) > 1){
														echo '<a href="#" data-modal-button-text="'.esc_attr__('Change', 'directorypress-frontend').'" data-modal-title="'.esc_attr__('Translation', 'directorypress-frontend').'" class="listing_setting_action_link" data-modal-class="listing_translation_modal" data-listing-id="'. $listing->post->ID .'" data-toggle="modal" data-target="#listing_action_modal"></i><span class="fas fa-globe-europe"></span>'. esc_html__('Translation', 'directorypress-frontend').'</a>';
													}
												?>
												<?php do_action('directorypress_dashboard_listing_options', $listing); ?>
												<a href="#" class="listing_setting_action_link" data-modal-button-text="<?php echo esc_attr__('Delete', 'directorypress-frontend'); ?>" data-modal-title="<?php echo esc_attr__('Are You Sure?', 'directorypress-frontend'); ?>" data-modal-class="listing_delete_modal" data-listing-id="<?php echo $listing->post->ID; ?>" data-toggle="modal" data-target="#listing_action_modal"><span class="fas fa-trash-alt"></span><?php esc_attr_e('Delete', 'directorypress-frontend'); ?></a>
											</div>
										</div>
									<?php endif; ?>
								</div>
							</div>
							<?php do_action('directorypress-dashboard-listing-after-configure-html', $listing); ?>
						</div>
					</div>
				<?php endwhile; ?>
				
			</div>
		</div>
		<?php directorypress_pagination_display($public_handler->query, '', false); ?>
	<?php endif; ?>