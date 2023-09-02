<h3><?php echo sprintf(__('Clicks statistics of listing "%s"', 'directorypress-frontend'), $directorypress_object->current_listing->title()); ?></h3>

<h4><?php echo sprintf(__('Total clicks: %d', 'directorypress-frontend'), (get_post_meta($directorypress_object->current_listing->post->ID, '_total_clicks', true) ? get_post_meta($directorypress_object->current_listing->post->ID, '_total_clicks', true) : 0)); ?></h4>

<?php 
$months_names = array(
	1 => __('January', 'directorypress-frontend'),	
	2 => __('February', 'directorypress-frontend'),	
	3 => __('March', 'directorypress-frontend'),	
	4 => __('April', 'directorypress-frontend'),	
	5 => __('May', 'directorypress-frontend'),	
	6 => __('June', 'directorypress-frontend'),	
	7 => __('July', 'directorypress-frontend'),	
	8 => __('August', 'directorypress-frontend'),	
	9 => __('September', 'directorypress-frontend'),	
	10 => __('October', 'directorypress-frontend'),	
	11 => __('November', 'directorypress-frontend'),	
	12 => __('December', 'directorypress-frontend'),	
);
if ($clicks_data = get_post_meta($directorypress_object->current_listing->post->ID, '_clicks_data', true)) {
	foreach ($clicks_data AS $month_year=>$count) {
		$month_year = explode('-', $month_year);
		$data[$month_year[1]][$month_year[0]] = $count;
	}
	ksort($data);
}
?>

<?php if (isset($data)): ?>
<div>
	<?php foreach ($data AS $year=>$months_counts): ?>
	<h4><?php echo $year; ?></h4>

	<div>
		<canvas id="canvas-<?php echo esc_attr($year); ?>" style="height: 450px;"></canvas>
		<script>
		var chartData_<?php echo esc_attr($year); ?> = {
			labels : ["<?php echo implode('","', $months_names); ?>"],
			datasets : [
				{
					fillColor : "rgba(151,187,205,0.2)",
					strokeColor : "rgba(151,187,205,1)",
					type: 'line',
					<?php
					foreach ($months_names AS $month_num=>$name)
						if (!isset($months_counts[$month_num]))
							$months_counts[$month_num] = 0;
					ksort($months_counts);
					?>
					data : [<?php echo implode(',', $months_counts); ?>]
				}
			]
		};
	
		(function($) {
			"use strict";

			$(function() {
				var ctx_<?php echo esc_attr($year); ?> = document.getElementById("canvas-<?php echo esc_attr($year); ?>").getContext("2d");
				window.myLine_<?php echo esc_attr($year); ?> = new Chart(ctx_<?php echo esc_attr($year); ?>).Line(chartData_<?php echo esc_attr($year); ?>, {
					responsive: true
				});
			});
		})(jQuery);
		</script>
	</div>
	<hr />
	<?php endforeach; ?>
</div>
<?php endif; ?>

<a href="<?php echo $public_handler->referer; ?>" class="btn btn-primary"><?php _e('Go back ', 'directorypress-frontend'); ?></a>