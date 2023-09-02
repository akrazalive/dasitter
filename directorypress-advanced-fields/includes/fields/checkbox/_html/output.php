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
	<ul class="field-content clearfix">
	<?php if ($field->how_display_items == 'all'): ?>
	
	<?php foreach ($field->selection_items AS $key=>$item): ?>
		<?php 
			if(in_array($key, $field->value)){
				$icon = '<span class="far fa-check-circle"></span>';
			}else{
				$icon = '<span class="far fa-times-circle"></span>';
			}
		?>
		<li><?php echo $icon; ?><?php echo $item; ?></li>
	<?php endforeach; ?>
	<?php elseif ($field->how_display_items == 'checked'): ?>
	<?php foreach ($field->value AS $key): ?>
	<?php 
	if(isset($field->icon_selection_items[$key]) && $field->check_icon_type == 'custom_icon'){
		$icon = '<span class="'.$field->icon_selection_items[$key].'"></span>';
	}elseif(isset($field->icon_selection_items[$key]) && $field->check_icon_type == 'default'){
		$icon = '<span class="far fa-check-circle"></span>';
	}else{
		$icon = '<span class="far fa-check-circle"></span>';
	}
	?>
		<?php if (isset($field->selection_items[$key])): ?><li><?php echo $icon; ?><?php echo $field->selection_items[$key]; ?></li><?php endif; ?>
	<?php endforeach; ?>
	<?php endif; ?>
	</ul>
</div>
<?php endif; ?>