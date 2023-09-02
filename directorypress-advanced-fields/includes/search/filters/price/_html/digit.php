<?php 
	global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;
	if ($directorypress_directory_handler = $directorypress_object->directorypress_get_property_of_shortcode(DIRECTORYPRESS_MAIN_SHORTCODE) && $DIRECTORYPRESS_ADIMN_SETTINGS['archive_page_style'] != 2){
		$field_width = $search_field->field->fieldwidth_archive;
	}elseif($directorypress_directory_handler = $directorypress_object->directorypress_get_property_of_shortcode(DIRECTORYPRESS_MAIN_SHORTCODE) && $DIRECTORYPRESS_ADIMN_SETTINGS['archive_page_style'] == 2){
		$field_width = '100';
	}else{
		$field_width = $search_field->field->fieldwidth;
	}

?>
<div class="search-element-col field-id-<?php echo $search_field->field->id; ?> field-form-id-<?php echo $search_form_id; ?> unique-form-field-id-<?php echo $search_field->field->id; ?>_<?php echo $search_form_id; ?> field-type-<?php echo $search_field->field->type; ?> pull-left" style=" width:<?php echo $field_width ?>%; padding:0 <?php echo $gap_in_fields; ?>px;">
	
	<?php if(!$search_field->field->is_hide_name_on_search){ ?>
		<div class="col-md-12">
			<label><?php echo $search_field->field->name; ?> <?php echo $search_field->field->currency_symbol; ?></label>
		</div>
	<?php } ?>
	<div class="col-md-12">
		<input type="text" name="field_<?php echo $search_field->field->slug; ?>" class="form-control" value="<?php echo esc_attr($search_field->value); ?>" />
	</div>
</div>