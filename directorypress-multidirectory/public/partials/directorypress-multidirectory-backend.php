<div class="wrap about-wrap directorypress-admin-wrap">
	<?php DirectoryPress_Admin_Panel::listing_dashboard_header(); ?>
	<?php directorypress_renderMessages(); ?>
	<div class="directorypress-plugins directorypress-theme-browser-wrap">
		<div class="theme-browser rendered">
			<div class="directorypress-box">
				<div class="directorypress-box-head">
					<h1><?php _e('Listings directorytypes', 'directorypress-multidirectory'); ?><h1>
					<p><?php _e('You can create unlimited Listing Directorytypes', 'directorypress-multidirectory'); ?></p>
					<?php echo '<a class="dp-admin-btn dp-success create" data-toggle="modal" data-target="#create_new_directory" href="#">' . __('Create new directorytype', 'directorypress-multidirectory') . '</a>'; ?>
				</div>
				<div id="directories_list" class="directorypress-box-content wp-clearfix">
					<form method="POST" action="<?php echo admin_url('admin.php?page=directorypress_directorytypes'); ?>">
						<?php 
							echo $directory_list;
						?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="create_new_directory" class="modal fade directorypress-admin-modal" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="topline"></div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary new-directory-action-button"><?php echo esc_html__('Create', 'directorypress-frontend'); ?></button>
				<button type="button" class="btn btn-default cancel-btn" data-dismiss="modal"><?php echo esc_html__('Cancel', 'directorypress-frontend'); ?></button>
			</div>
		</div>
	</div>
</div>
<div id="directory_configure" class="modal fade directorypress-admin-modal" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="topline"></div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default cancel-btn" data-dismiss="modal"><?php echo esc_html__('Cancel', 'directorypress-frontend'); ?></button>
			</div>
		</div>
	</div>
</div>