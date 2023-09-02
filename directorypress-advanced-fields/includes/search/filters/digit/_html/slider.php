<?php global $DIRECTORYPRESS_ADIMN_SETTINGS; ?>
<?php $index = $search_field->field->id . '_' . $search_form_id; ?>
<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
<?php $min_value = (($search_field->min_max_value['min']) ? $search_field->min_max_value['min'] : __('min', 'directorypress-advanced-fields')); ?>
<?php $max_value = (($search_field->min_max_value['max']) ? $search_field->min_max_value['max'] : __('max', 'directorypress-advanced-fields')); ?>
<?php elseif ($search_field->mode == 'range_slider'): ?>
<?php $min_value = (($search_field->min_max_value['min']) ? $search_field->min_max_value['min'] : __('min', 'directorypress-advanced-fields')); ?>
<?php $max_value = (($search_field->min_max_value['max']) ? $search_field->min_max_value['max'] : __('max', 'directorypress-advanced-fields')); ?>
<?php endif; ?>
<?php 
	global $directorypress_object;
	if ($directorypress_directory_handler = $directorypress_object->directorypress_get_property_of_shortcode(DIRECTORYPRESS_MAIN_SHORTCODE) && $DIRECTORYPRESS_ADIMN_SETTINGS['archive_page_style'] != 2){
		$field_width = $search_field->field->fieldwidth_archive;
	}elseif($directorypress_directory_handler = $directorypress_object->directorypress_get_property_of_shortcode(DIRECTORYPRESS_MAIN_SHORTCODE) && $DIRECTORYPRESS_ADIMN_SETTINGS['archive_page_style'] == 2){
		$field_width = '100';
	}else{
		$field_width = $search_field->field->fieldwidth;
	}

?>

<div class="cznumber-slider search-element-col field-id-<?php echo $search_field->field->id; ?> field-type-<?php echo $search_field->field->type; ?> field-form-id-<?php echo $search_form_id; ?> unique-form-field-id-<?php echo $index; ?> pull-left" style=" width:<?php echo $field_width; ?>%; padding:0 <?php echo $gap_in_fields; ?>px;">
	<?php if(!$search_field->field->is_hide_name_on_search){ ?>
		<div class="">
			<label><?php echo $search_field->field->name; ?> <span id="slider_label_<?php echo $index; ?>"><?php echo $min_value . ' - ' . $max_value; ?></span></label>
		</div>
	<?php } ?>
	<script>
		(function($) {
			"use strict";
		
			$(function() {
				<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
				var slider_params_<?php echo $index; ?> = ['<?php _e('min', 'directorypress-advanced-fields'); ?>', <?php echo implode(',', $search_field->min_max_options); ?>, '<?php _e('max', 'directorypress-advanced-fields'); ?>'];
				var slider_min = 0;
				var slider_max = slider_params_<?php echo $index; ?>.length-1;
				<?php elseif ($search_field->mode == 'range_slider'): ?>
				var slider_min = <?php echo $search_field->slider_step_1_min-1; ?>;
				var slider_max = <?php echo $search_field->slider_step_1_max+1; ?>;
				<?php endif; ?>
				$('#range_slider_<?php echo $index; ?>').slider({
					<?php if (function_exists('is_rtl') && is_rtl()): ?>
					isRTL: true,
					<?php endif; ?>
					min: slider_min,
					max: slider_max,
					range: true,
					<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
					values: [<?php echo ((($min = array_search($search_field->min_max_value['min'], $search_field->min_max_options)) !== false) ? $min+1 : 0); ?>, <?php echo ((($max = array_search($search_field->min_max_value['max'], $search_field->min_max_options)) !== false) ? $max+1 : count($search_field->min_max_options)+1); ?>],
					<?php elseif ($search_field->mode == 'range_slider'): ?>
					values: [<?php echo (($search_field->min_max_value['min']) ? $search_field->min_max_value['min']+1 : $search_field->slider_step_1_min-1); ?>, <?php echo (($search_field->min_max_value['max']) ? $search_field->min_max_value['max']+1 : $search_field->slider_step_1_max+1); ?>],
					<?php endif; ?>
					slide: function(event, ui) {
						<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
						$('#slider_label_<?php echo $index; ?>').html(slider_params_<?php echo $index; ?>[ui.values[0]] + ' - ' + slider_params_<?php echo $index; ?>[ui.values[1]]);
						if (slider_params_<?php echo $index; ?>[ui.values[0]] == '<?php _e('min', 'directorypress-advanced-fields'); ?>')
							$('#field_<?php echo $index; ?>_min').val('');
						else
							$('#field_<?php echo $index; ?>_min').val(slider_params_<?php echo $index; ?>[ui.values[0]]);
						if (slider_params_<?php echo $index; ?>[ui.values[1]] == '<?php _e('max', 'directorypress-advanced-fields'); ?>')
							$('#field_<?php echo $index; ?>_max').val('');
						else
							$('#field_<?php echo $index; ?>_max').val(slider_params_<?php echo $index; ?>[ui.values[1]]);
						<?php elseif ($search_field->mode == 'range_slider'): ?>
						if (ui.values[0] == <?php echo $search_field->slider_step_1_min-1; ?>) {
							var min = '<?php _e('min', 'directorypress-advanced-fields'); ?>';
							$('#field_<?php echo $index; ?>_min').val('');
						} else {
							var min = ui.values[0];
							$('#field_<?php echo $index; ?>_min').val(ui.values[0]);
						}
						if (ui.values[1] == <?php echo $search_field->slider_step_1_max+1; ?>) {
							var max = '<?php _e('max', 'directorypress-advanced-fields'); ?>';
							$('#field_<?php echo $index; ?>_max').val('');
						} else {
							var max = ui.values[1];
							$('#field_<?php echo $index; ?>_max').val(ui.values[1]);
						}
		
						$('#slider_label_<?php echo $index; ?>').html(min + ' - ' + max);
						<?php endif; ?>
					}
				});
			});
		})(jQuery);
	</script>
	<div class="form-group directorypress-jquery-ui-slider">
		<div id="range_slider_<?php echo $index; ?>"></div>
		<input type="hidden" id="field_<?php echo $index; ?>_min" name="field_<?php echo $search_field->field->slug; ?>_min" value="<?php echo (($min_value == __('min', 'directorypress-advanced-fields')) ? '' : $min_value); ?>" />
		<input type="hidden" id="field_<?php echo $index; ?>_max" name="field_<?php echo $search_field->field->slug; ?>_max" value="<?php echo (($max_value == __('max', 'directorypress-advanced-fields')) ? '' : $max_value); ?>" />
	</div>

</div>
