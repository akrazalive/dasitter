<?php 

/**
 * Template name: Dashboard Template
 * @package    DirectoryPress
 * @subpackage DirectoryPress/public/single-listing
 * @author     DirectoryPress <team@directorypress.co>
*/



global $post, $ALSP_ADIMN_SETTINGS;

get_header();
?>
<div class="dashboard-page-section">
	<div class="container clearfix">
		<a id="menu-toggle" href="#" class="glyphicon glyphicon-align-justify btn-menu toggle"></a>
		<div class="row dashboard-wrapper active clearfix">
			<?php if ( is_user_logged_in() ) : ?>
				<div class="directorypress-user-panel-sidearea col-md-3">
					<?php do_action('dashboard_panel_html'); ?>
				</div>
				<div class="col-md-9 content-wrapper-main">
					<div id="panel-content-wrapper" class="content-wrapper clearfix">
						<?php do_action('page_add_before_content'); ?>
						<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
							<?php the_content();?>
							<div class="clearboth"></div>
						<?php endwhile; ?>
						<?php do_action('page_add_after_content'); ?>
					</div>
				</div>
			<?php else: ?>
				<div class="col-md-12 content-wrapper-main">
					<div id="panel-content-wrapper" class="content-wrapper clearfix">
						<?php do_action('page_add_before_content'); ?>
						<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
							<?php the_content();?>
							<div class="clearboth"></div>
						<?php endwhile; ?>
						<?php do_action('page_add_after_content'); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>