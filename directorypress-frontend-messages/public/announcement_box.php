<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo difp_info_output();

if( ! $total_announcements ) {
	echo '<div class="alert alert-warning alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="'.esc_attr__("close", "directorypress-frontend-messages").'">&times;</a>'.apply_filters('difp_filter_announcement_empty', esc_html__("No announcements found.", 'directorypress-frontend-messages') ).'</div>';
	return;
}

do_action('difp_display_before_announcementbox');
	  
	  	?><form class="difp-message-table form announcement" method="post" action="">
		<div class="difp-table difp-action-table clearfix">
				<div class="difp-bulk-action clearfix">
					<select class="pacz-select2" name="difp-bulk-action">
						<option value=""><?php _e('Bulk action', 'directorypress-frontend-messages'); ?></option>
						<?php foreach( Difp_Announcement::init()->get_table_bulk_actions() as $bulk_action => $bulk_action_display ) { ?>
						<option value="<?php echo $bulk_action; ?>"><?php echo $bulk_action_display; ?></option>
						<?php } ?>
					</select>
					<button type="submit" class="difp-button" name="difp_action" value="announcement_bulk_action"><?php _e('Apply', 'directorypress-frontend-messages'); ?></button>
					<input type="hidden" name="token"  value="<?php echo difp_create_nonce('announcement_bulk_action'); ?>"/>
				</div>
				<div class="difp-filter clearfix">
					<select class="pacz-select2" onchange="if (this.value) window.location.href=this.value">
						<?php foreach( Difp_Announcement::init()->get_table_filters() as $filter => $filter_display ) { ?>
						<option value="<?php echo esc_url( add_query_arg( array('difp-filter' => $filter, 'difppage' => false ) ) ); ?>" <?php selected($g_filter, $filter);?>><?php echo $filter_display; ?></option>
						<?php } ?>
					</select>
				</div>
			<div class="difp-loading-gif-div"></div>
		</div>
		<?php if( $announcements->have_posts() ) { ?>
		<div id="difp-table" class="difp-table difp-odd-even"><?php
			while ( $announcements->have_posts() ) { 
				$announcements->the_post(); ?>
					<div id="difp-message-<?php echo get_the_ID(); ?>" class="difp-table-row clearfix"><?php
						foreach ( Difp_Announcement::init()->get_table_columns() as $column => $display ) { ?>
							<div class="difp-column difp-column-<?php echo $column; ?>"><?php Difp_Announcement::init()->get_column_content($column); ?></div>
						<?php } ?>
					</div>
				<?php
			} //endwhile
			?></div><?php
			echo difp_pagination( Difp_Announcement::init()->get_user_announcement_count( empty($g_filter) ? 'total' : $g_filter ), difp_get_option('announcements_page', 15) );
		} else {
			?><div class="alert alert-warning"><?php _e('No announcements found. Try different filter.', 'directorypress-frontend-messages'); ?></div><?php 
		}
		?></form><?php 
		wp_reset_postdata();