<?php if ($field->value): 
	if(!directorypress_is_listing_page()){
		$tootltip_triger_class = 'directorypress_field_tooltip';
		$tooltip_content_class = 'tooltip-content';
	}else{
		$tootltip_triger_class = '';
		$tooltip_content_class = '';
	}

?>
<div class="directorypress-field-item directorypress-field-type-<?php echo $field->type; ?>">
	<span class="field-label">
			<?php
				if(!directorypress_is_listing_page()){
					if($listing->listing_view == 'show_grid_style'){
						if($field->is_hide_name_on_grid == 'show_only_label'){
							echo '<span class="directorypress-field-title">'.$field->name.':</span>';
						}elseif($field->is_hide_name_on_grid == 'show_icon_label'){
							if ($field->icon_image){
								echo '<span class="directorypress-field-icon '.$field->icon_image.'"></span>';
							}
							echo '<span class="directorypress-field-title">'.$field->name.':</span>';
						}elseif($field->is_hide_name_on_grid == 'show_only_icon'){
							if ($field->icon_image){
								echo '<span class="directorypress-field-icon '.$field->icon_image.'"></span>';
							}
						}
					}elseif($listing->listing_view == 'show_list_style'){
						if($field->is_hide_name_on_list == 'show_only_label'){
							echo '<span class="directorypress-field-title">'.$field->name.':</span>';
						}elseif($field->is_hide_name_on_list == 'show_icon_label'){
							if ($field->icon_image){
								echo '<span class="directorypress-field-icon '.$field->icon_image.'"></span>';
							}
							echo '<span class="directorypress-field-title">'.$field->name.':</span>';
						}elseif($field->is_hide_name_on_list == 'show_only_icon'){
							if ($field->icon_image){
								echo '<span class="directorypress-field-icon '.$field->icon_image.'"></span>';
							}
						}
					}
				}else{
					if ($field->icon_image){
						echo '<span class="directorypress-field-icon '.$field->icon_image.'"></span>';
					}
					if(!$field->is_hide_name){
						echo '<span class="directorypress-field-title">'.$field->name.':</span>';
					}
				}
			?>
		</span>
	<div class="field-content clearfix">
		<?php foreach ($field->value AS $key): ?>
			<?php 
				if(isset($field->color_codes[$key])){
					$icon = '<span style="background:'.$field->color_codes[$key].';width:15px;height:15px;display:inline-block;margin-right:5px;vertical-align:middle;border-radius:50%;"></span>';
				}
			?>
			<?php if (isset($field->selection_items[$key])): ?><span><?php echo $icon; ?><?php echo $field->selection_items[$key]; ?></span><?php endif; ?>
		<?php endforeach; ?>
	
	</div>
</div>
<?php endif; ?>