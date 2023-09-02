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
<?php if (count($search_field->min_max_options)): ?>
<?php if ($search_field->field->is_integer) $decimals = 0; else $decimals = 2; ?>
<?php $col_md = 12; ?>
<div class="search-element-col field-id-<?php echo $search_field->field->id; ?> field-form-id-<?php echo $search_form_id; ?> unique-form-field-id-<?php echo $search_field->field->id; ?>_<?php echo $search_form_id; ?> field-type-<?php echo $search_field->field->type; ?> pull-left" style=" width:<?php echo $field_width; ?>%; padding:0 <?php echo $gap_in_fields; ?>px;">
	<?php if(!$search_field->field->is_hide_name_on_search){ ?>
			<label><?php echo $search_field->field->name; ?></label>
	<?php } ?>
	<div class="row">
		<div class="col-md-6 col-xs-12">
			<select name="field_<?php echo $search_field->field->slug; ?>_min" class="form-control directorypress-select2">
			<option value=""><?php _e('- Select min -', 'directorypress-advanced-fields'); ?></option>
			<?php foreach ($search_field->min_max_options AS $item): ?>
				<?php if (is_numeric($item)): ?>
				<option value="<?php echo $item; ?>" <?php selected($search_field->min_max_value['min'], $item); ?>><?php echo number_format($item, $decimals, $search_field->field->decimal_separator, $search_field->field->thousands_separator); ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
			</select>
		</div>
		<div class="col-md-6 col-xs-12">
			<select name="field_<?php echo $search_field->field->slug; ?>_max" class="form-control directorypress-select2">
			<option value=""><?php _e('- Select max -', 'directorypress-advanced-fields'); ?></option>
			<?php foreach ($search_field->min_max_options AS $item): ?>
				<?php if (is_numeric($item)): ?>
				<option value="<?php echo $item; ?>" <?php selected($search_field->min_max_value['max'], $item); ?>><?php echo number_format($item, $decimals, $search_field->field->decimal_separator, $search_field->field->thousands_separator); ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
			</select>
		</div>
	</div>
</div>
<?php endif; ?>