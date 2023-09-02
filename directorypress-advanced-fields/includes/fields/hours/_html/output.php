<?php if (array_filter($field->value)):
	global $post; 
	$toogle_id =  $field->id.'-'.$post->ID; 
	if(!directorypress_is_listing_page()){
		$toogle_attr = '';
		$collapse_class = '';
	}else{
		$toogle_attr = '';
		$collapse_class = '';
	}
?>
<div class="directorypress-field-item directorypress-field-type-<?php echo $field->type; ?>">
	<span class="field-label">
		<?php
			if(!directorypress_is_listing_page()){
				if($listing->listing_view == 'show_grid_style'){
					if($field->is_hide_name_on_grid == 'show_only_label'){
						echo '<span class="directorypress-field-title" '.$toogle_attr.'>'.$field->name.':</span>';
					}elseif($field->is_hide_name_on_grid == 'show_icon_label'){
						if ($field->icon_image){
							echo '<span class="directorypress-field-icon '.$field->icon_image.'" '.$toogle_attr.'></span>';
						}
						echo '<span class="directorypress-field-title" '.$toogle_attr.'>'.$field->name.':</span>';
					}elseif($field->is_hide_name_on_grid == 'show_only_icon'){
						if ($field->icon_image){
							echo '<span class="directorypress-field-icon '.$field->icon_image.'" '.$toogle_attr.'></span>';
						}
					}
				}elseif($listing->listing_view == 'show_list_style'){
					if($field->is_hide_name_on_list == 'show_only_label'){
						echo '<span class="directorypress-field-title" '.$toogle_attr.'>'.$field->name.':</span>';
					}elseif($field->is_hide_name_on_list == 'show_icon_label'){
						if ($field->icon_image){
							echo '<span class="directorypress-field-icon '.$field->icon_image.'" '.$toogle_attr.'></span>';
						}
						echo '<span class="directorypress-field-title" '.$toogle_attr.'>'.$field->name.':</span>';
					}elseif($field->is_hide_name_on_list == 'show_only_icon'){
						if ($field->icon_image){
							echo '<span class="directorypress-field-icon '.$field->icon_image.'" '.$toogle_attr.'></span>';
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
	<?php if ($strings = $field->processStrings()): ?>
	<div class="field-content clearfix">
		<ul class=" clearfix">
			<?php //echo $field->status(); ?>
			<?php foreach ($strings AS $string): ?>
			<li><?php echo $string; ?></li>
			<?php endforeach; ?>
		</ul>
	
	</div>
	<?php endif; ?>
</div><?php endif; ?>