<div class="directorypress-modal-content wp-clearfix">
<script>
	(function($) {
		"use strict";
	
		$(function() {
			$( '.colorpicker' ).wpColorPicker();
			var max_index = <?php echo ((count(array_keys($field->selection_items)) ? max(array_keys($field->selection_items)) : 1)); ?>;
			$("#add_selection_item").click(function() {
				max_index = max_index+1;
				$("#selection_items_wrapper").append('<div class="selection_item clearfix"><span class="far fa-trash-alt directorypress-delete-selection-item"></span><input name="selection_items['+max_index+']" type="text" class="regular-text" value="" placeholder="text"/><input name="color_codes['+max_index+']" class="colorpicker" value="" placeholder="color"/><span class="fas fa-arrows-alt directorypress-move-label"></span></div>');
				$( '.colorpicker' ).wpColorPicker();
			});
			$(document).on("click", ".directorypress-delete-selection-item", function() {
				//$(this).parent().remove();
				 var targeted_popup_class1 = jQuery(this).attr('data-id');
        $('.id_'+ targeted_popup_class1+'').remove();
				//$('.id_'+data-attr['id']).remove();
			});
		
			$("#selection_items_wrapper").sortable({
				placeholder: "ui-sortable-placeholder",
				helper: function(e, ui) {
					ui.children().each(function() {
						$(this).width($(this).width());
					});
					return ui;
				},
				start: function(e, ui){
					ui.placeholder.height(ui.item.height());
				}
	    	});
			//$('.selection_item').wrap('<div class="selection_item_main"></div>');
			
			//$( '.colorpicker' ).val('test');
		});
	})(jQuery);
</script>
	<form class="config" method="POST" action="">
		<?php wp_nonce_field(DIRECTORYPRESS_PATH, 'directorypress_configure_fields_nonce');?>
		<div class="form-table">
			<?php 
				//do_action('directorypress_select_field_configuration_html', $field); 
				//if($field->type == 'checkbox'){$field->add_configuration_options($field); }
			?>
			<p><label><?php _e('Selection items:', 'directorypress-advanced-fields'); ?></label></p>
					
			<div id="selection_items_wrapper" class="field-holder">
				<?php if (count($field->selection_items)): ?>
					<?php foreach ($field->selection_items AS $key=>$item): ?>
						<div id ="main_<?php echo $key; ?>" class="selection_item id_<?php echo $key; ?> clearfix">
							<span class="far fa-trash-alt directorypress-delete-selection-item" data-id="<?php echo $key; ?>"></span>
							<input name="selection_items[<?php echo $key; ?>]" type="text" class="regular-text" value="<?php echo $item; ?>" />
							<span class="fas fa-arrows-alt directorypress-move-label"></span>
						</div>
						<script>
							(function($) {
								"use strict";
								$(function() {
									$("#sub_<?php echo $key; ?>").appendTo("#main_<?php echo $key; ?>");
								});
							})(jQuery);
						</script>
					<?php endforeach; ?>
				
					<?php if($field->type == 'status'): ?>
						<?php foreach ($field->color_codes AS $key=>$item): ?>
							<div id ="sub_<?php echo $key; ?>" data-id="<?php echo $key; ?>" class="sub_item id_<?php echo $key; ?>">
								<input name="color_codes[<?php echo $key; ?>]" class="colorpicker" value="<?php echo $item; ?>" />
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php else: ?>
					<div class="selection_item id_1">
						<span class="far fa-trash-alt directorypress-delete-selection-item"></span>
						<input name="selection_items[1]" type="text" class="regular-text" value="" />
						<?php if($field->type == 'status'): ?>
							<input name="color_codes[1]" class="colorpicker" value="" />
						<?php endif; ?>
						<span class="fas fa-arrows-alt directorypress-move-label"></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<input type="button" id="add_selection_item" class="button button-primary" value="<?php esc_attr_e('Add New Option', 'directorypress-advanced-fields'); ?>" />
		<div class="id">
			<input type="hidden" name="id" value="">
		</div>
	</form>
</div>