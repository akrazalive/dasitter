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
<?php if (count($search_field->min_max_options)): ?>
<?php if ($columns == 1) $col_md = 12; else $col_md = 6; ?>
<div class="search-element-col field-id-<?php echo $search_field->field->id; ?> field-form-id-<?php echo $search_form_id; ?> unique-form-field-id-<?php echo $search_field->field->id; ?>_<?php echo $search_form_id; ?> field-type-<?php echo $search_field->field->type; ?> pull-left" style=" width:<?php echo $field_width ?>%; padding:0 <?php echo $gap_in_fields; ?>px;">
	<?php if(!$search_field->field->is_hide_name_on_search){ ?>
		<div class="col-md-12">
			<label><?php echo $search_field->field->name; ?> <?php echo $search_field->field->currency_symbol; ?></label>
		</div>
	<?php } ?>
	
	<select name="field_<?php echo $search_field->field->slug; ?>" class="form-control directorypress-select2">
		<option value=""><?php printf(__('%s Range Options', 'directorypress-advanced-fields'), $search_field->field->name); ?></option>
		<?php foreach ($search_field->field->range_options AS $key=>$item): ?>
			<option value="<?php echo esc_attr($key); ?>"><?php echo $item; ?></option>
		<?php endforeach; ?>
	</select>
</div>
<?php endif; ?>