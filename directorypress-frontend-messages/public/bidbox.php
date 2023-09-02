<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo difp_info_output();

if( ! $total_bids ) {
	echo "<div class='alert alert-danger'>".apply_filters('difp_filter_bixbox_empty', __("No offer found.", 'directorypress-frontend-messages'), $action)."</div>";
	return;
}

do_action('difp_display_before_messagebox', $action);
	  
	  	?>
	  	<form class="difp-message-table form" method="post" action="">
		<div class="difp-table difp-action-table filters-table clearfix">
			<div class="difp-filter pull-right">
				<select onchange="if (this.value) window.location.href=this.value" class="pacz-select2">
					<?php foreach( Difp_Bids::init()->get_table_filters() as $filter => $filter_display ) { ?>
					<option value="<?php echo esc_url( add_query_arg( array('difp-filter' => $filter, 'difppage' => false ) ) ); ?>" <?php selected($g_filter, $filter);?>><?php echo $filter_display; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="bult-action-wrap pull-left clearfix">
				<div class="difp-bulk-action pull-left" style="width:190px">
					<select name="difp-bulk-action"  class="pacz-select2">
						<option value=""><?php _e('Bulk action', 'directorypress-frontend-messages'); ?></option>
						<?php foreach( Difp_Bids::init()->get_table_bulk_actions() as $bulk_action => $bulk_action_display ) { ?>
						<option value="<?php echo $bulk_action; ?>"><?php echo $bulk_action_display; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="pull-left" style="width:75px;">
					<input type="hidden" name="token"  value="<?php echo difp_create_nonce('bulk_action'); ?>"/>
					<button type="submit" class="difp-button" name="difp_action" value="bulk_action"><?php _e('Apply', 'directorypress-frontend-messages'); ?></button>
				</div>
			</div>
			
		</div>
		<?php if( $bids->have_posts() ) { ?>
		<div id="difp-table" class="difp-table difp-odd-even row"><?php
			while ( $bids->have_posts() ) { 
				$bids->the_post(); ?>
					<div class="col-md-6">
						<div id="difp-message-<?php the_ID(); ?>" class="difp-table-row clearfix"><?php
							foreach ( Difp_Bids::init()->get_table_columns() as $column => $display ) { ?>
								<div class="difp-column difp-column-<?php echo $column; ?>"><?php Difp_Bids::init()->get_column_content($column); ?></div>
							<?php } ?>
						</div>
					</div>
				<?php
			} //endwhile
			?></div><?php
			echo difp_pagination();
		} else {
			?><div class="alert alert-danger"><?php _e('No offer found. Try different filter.', 'directorypress-frontend-messages'); ?></div><?php 
		}
		?></form><?php 
	wp_reset_postdata();
