<div class="directorypress-submit-listing-wrap row clearfix" style="height:100vh">
	<div class="cz-creat-listing col-md-12 col-sm-12 col-xs-12">
		<div class="cz-creat-listing-inner clearfix">
			<?php
				echo '<div class="alert alert-warning">';
					echo '<p>'. esc_html__('Phone verification is required to submit new listing', 'directorypress-frontend').'</p>';
					
				echo '</div>';
				echo '<a class="btn btn-primary" href="'. directorypress_dashboardUrl(array('directorypress_action' => 'profile')).'">'. esc_html__('Verify Now', 'directorypress-frontend').'</a>';
			?>
		</div>
	</div>
</div>