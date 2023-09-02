<div class="directorypress-field-item directorypress-field-type-<?php echo $field->type; ?>">
	<span class="field-label">
		<?php
		if(!directorypress_is_listing_page()){
			if($listing->listing_view == 'show_grid_style'){
				if($field->is_hide_name_on_grid == 'show_only_label'){
					echo '<span class="directorypress-field-title">'.$field->name.':</span>';
				}elseif($field->is_hide_name_on_grid == 'show_icon_label'){
					if ($field->icon_image){
						echo '<span class="directorypress-field-icon fa fa-lg '.$field->icon_image.'"></span>';
					}
					echo '<span class="directorypress-field-title">'.$field->name.':</span>';
				}elseif($field->is_hide_name_on_grid == 'show_only_icon'){
					if ($field->icon_image){
						echo '<span class="directorypress-field-icon fa fa-lg '.$field->icon_image.'"></span>';
					}
				}
			}elseif($listing->listing_view == 'show_list_style'){
				if($field->is_hide_name_on_list == 'show_only_label'){
					echo '<span class="directorypress-field-title">'.$field->name.':</span>';
				}elseif($field->is_hide_name_on_list == 'show_icon_label'){
					if ($field->icon_image){
						echo '<span class="directorypress-field-icon fa fa-lg '.$field->icon_image.'"></span>';
					}
					echo '<span class="directorypress-field-title">'.$field->name.':</span>';
				}elseif($field->is_hide_name_on_list == 'show_only_icon'){
					if ($field->icon_image){
						echo '<span class="directorypress-field-icon fa fa-lg '.$field->icon_image.'"></span>';
					}
				}
			}
		}else{
			if ($field->icon_image){
				echo '<span class="directorypress-field-icon fa fa-lg '.$field->icon_image.'"></span>';
			}
			if(!$field->is_hide_name){
				echo '<span class="directorypress-field-title">'.$field->name.':</span>';
			}
		}
		?>
	</span>
	<span class="field-content">
	<?php echo $formatted_number; ?>
	</span>
</div>