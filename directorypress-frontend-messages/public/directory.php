<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="difp-directory-search-form-div">
	<form id="difp-directory-search-form" action="">
	<input type="hidden" name="difpaction" value="directory" />
	<input type="search" name="difp-search" class="difp-directory-search-form-field" value="<?php isset( $_GET["difp-search"] ) ? esc_attr_e( $_GET["difp-search"] ): ""; ?>" placeholder="<?php _e("Search Users", "directorypress-frontend-messages"); ?>" />
	<input type="hidden" name="difppage" value="1" />
	</form>
</div>
<?php 
if (! empty( $user_query->results)){ ?>
  
	<div class="difp-table difp-odd-even">
		
     <span class="difp-table-caption"><?php _e("Total Users", "directorypress-frontend-messages"); ?>: (<?php echo number_format_i18n($total); ?>)</span>
		
      <?php foreach( $user_query->results as $u ){ ?>
		  <div class="difp-table-row">
		  <div class="difp-column"><?php echo get_avatar($u->ID, 64); ?></div>
		  <div class="difp-column"><?php esc_html_e( $u->display_name ); ?></div>
		  <div class="difp-column"><a href="<?php echo difp_query_url( "newmessage", array( "difp_to" => $u->user_nicename)); ?>"><?php _e("Send Message", "directorypress-frontend-messages"); ?></a></div>
		</div>
      <?php } ?>
	 </div>
	 <?php echo difp_pagination( $total, difp_get_option('user_page', 50 ) ); ?>

<?php } else { ?>
  <div class='alert alert-danger'><?php _e("No users found.", 'directorypress-frontend-messages'); ?></div>
<?php }
