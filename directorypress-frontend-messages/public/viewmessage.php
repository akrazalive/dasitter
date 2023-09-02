<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$i = 0;

if( $messages->have_posts() ) {
	wp_enqueue_script( 'difp-replies-show-hide' );
	$hide_read = apply_filters( 'difp_filter_hide_message_initially_if_read', true );
	?>
	<div class="difp-message"><?php
		while ( $messages->have_posts() ) {
			$i++;
			
			$messages->the_post();
			$read_class = ( $hide_read && difp_is_read() ) ? '' : '';
			difp_make_read(); 
			difp_make_read( true ); ?>
			
			<?php if( $i === 1 ){

					$participants = difp_get_participants( get_the_ID() );
					$par = array();
					foreach( $participants as $participant ) {
						$par[] = difp_get_userdata( $participant, 'display_name', 'id' );
					} ?>
					<div class="difp-message-title-heading">
					<?php 
					$image = get_post_meta(get_the_ID(), '_listing_id', true);
			$image2 = get_post_meta($image, '_thumbnail_id', true);
			$width= 80;
			$height= 80;
						
			$image_src_array = wp_get_attachment_image_src($image2, 'full');
			$param = array(
				'width' => $width,
				'height' => $height,
				'crop' => true
						);
						$src  = $image_src_array[0];
						
						?>
				
				<img alt="<?php the_title(); ?>" src="<?php echo bfi_thumb($src, $param); ?>" width="<?php echo $width ?>" height="<?php echo $height ?>" />
				<?php the_title(); ?>
				<?php 
				$bid = get_post_meta(get_the_ID(), '_listing_bid', true);
				?>
				</div>
			<div class="difp-message-title-heading participants"><?php _e("Participants", 'directorypress-frontend-messages'); ?>: <?php echo apply_filters( 'difp_filter_display_participants', implode( ', ', $par ), $par, $participants ); ?><?php if(isset($bid) && (!empty($bid))){ ?><span class="bid_amount"><?php  echo esc_html__('Bid Amount', 'directorypress-frontend-messages').': '.$bid; ?></span><?php } ?></div>
					<!--<div class="difp-message-toggle-all difp-align-right"><?php //_e("Toggle Messages", 'directorypress-frontend-messages'); ?></div>-->
				<?php } ?>
			
			<?php 
				$author_id = get_the_author_meta('ID');
				$current_user = wp_get_current_user();
				$current_user_id = $current_user->ID;
					if($author_id != $current_user_id){
						$author_class = 'second_user';
					}else if($author_id == $current_user_id){
						$author_class = 'current_user';
					}
				?>
			<div class="difp-per-message <?php echo $author_class; ?> clearfix">
			<?php 
				$authorID = get_the_author_meta('ID');
			//$author_name = get_the_author_meta('display_name', $authorID);
					$output = '';
					//$author_img_url = get_user_meta($authorID, "pacz_author_avatar_url", true); 
					$avatar_id = get_user_meta( $authorID, 'avatar_id', true );
					
					//$author_img_url = get_the_author_meta('pacz_author_avatar_url', $authorID, true); 
					$output .='<div class="author-thumbnail">';
					if(!empty($avatar_id) && is_numeric($avatar_id)) {
						$author_avatar_url = wp_get_attachment_image_src( $avatar_id, 'full' ); 
						$src = $author_avatar_url[0];
						$params = array( 'width' => 60, 'height' => 60, 'crop' => true );
						$output .= "<img src='" . bfi_thumb( $src, $params ) . "' alt='' />";
					} else { 
						$avatar_url = get_avatar_url($authorID, ['size' => '60']);
						$output .='<img src="'.$avatar_url.'" alt="author" />';
										}
					$output .='</div>';
					echo $output;
			?>
				<div class="difp-per-message-inner">
					<div class="difp-message-title difp-message-title-<?php the_ID(); ?> clearfix">
						<span class="author"><?php the_author_meta('display_name'); ?></span>
						<span class="date"><?php the_time(); ?></span>
					</div>
					<div class="difp-message-content difp-message-content-<?php the_ID(); ?><?php echo $read_class; ?>">
						<?php the_content(); ?>
						
						<?php if( $i === 1 ){
							do_action ( 'difp_display_after_parent_message' );
						} else {
							do_action ( 'difp_display_after_reply_message' );
						} ?>
						<?php do_action ( 'difp_display_after_message', $i ); ?>
					</div>
				</div>
			</div><?php
		} ?>
	</div><?php
	wp_reset_postdata();
	
	include( difp_locate_template( 'reply_form.php') );
	
} else {
	echo "<div class='alert alert-danger'>".__("You do not have permission to view this message!", 'directorypress-frontend-messages')."</div>";
}