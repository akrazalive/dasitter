<?php 
	global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;
	if ($directorypress_directory_handler = $directorypress_object->directorypress_get_property_of_shortcode(DIRECTORYPRESS_MAIN_SHORTCODE) && $DIRECTORYPRESS_ADIMN_SETTINGS['archive_page_style'] != 2){
		$field_width = $search_field->field->fieldwidth_archive;
	}elseif($directorypress_directory_handler = $directorypress_object->directorypress_get_property_of_shortcode(DIRECTORYPRESS_MAIN_SHORTCODE) && $DIRECTORYPRESS_ADIMN_SETTINGS['archive_page_style'] == 2){
		$field_width = '100';
	}else{
		$field_width = $search_field->field->fieldwidth;
	}
	$decimal = ($search_field->field->hide_decimals)? 0 : 2;
?>
<?php if (count($search_field->min_max_options)): ?>
<div class="search-element-col field-id-<?php echo $search_field->field->id; ?> field-form-id-<?php echo $search_form_id; ?> unique-form-field-id-<?php echo $search_field->field->id; ?>_<?php echo $search_form_id; ?> field-type-<?php echo $search_field->field->type; ?> pull-left" style=" width:<?php echo $field_width ?>%; padding:0 <?php echo $gap_in_fields; ?>px;">
	<?php if(!$search_field->field->is_hide_name_on_search){ ?>
		<label><?php echo $search_field->field->name; ?> <?php echo $search_field->field->currency_symbol; ?></label>
	<?php } ?>
	<div class="row" style="margin:0 -<?php echo $gap_in_fields; ?>px;">
		<div class="price-min col-md-6 col-xs-12" style="padding:0 <?php echo $gap_in_fields; ?>px;">
			<select name="field_<?php echo $search_field->field->slug; ?>_min" class="directorypress-select2" style="width:100%;">
			<option value=""><?php _e('- Select min -', 'directorypress-advanced-fields'); ?></option>
			<?php foreach ($search_field->min_max_options AS $item): ?>
				<?php if (is_numeric($item)): ?>
				<option value="<?php echo $item; ?>" <?php selected($search_field->min_max_value['min'], $item); ?>><?php echo number_format($item, $decimal, $search_field->field->decimal_separator, $search_field->field->thousands_separator); ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
			</select>
		</div>
		<div class="price-max col-md-6 col-xs-12" style="padding:0 <?php echo $gap_in_fields; ?>px;">
			<select name="field_<?php echo $search_field->field->slug; ?>_max" class="directorypress-select2" style="width:100%;">
			<option value=""><?php _e('- Select max -', 'directorypress-advanced-fields'); ?></option>
			<?php foreach ($search_field->min_max_options AS $item): ?>
				<?php if (is_numeric($item)): ?>
				<option value="<?php echo $item; ?>" <?php selected($search_field->min_max_value['max'], $item); ?>><?php echo number_format($item, $decimal, $search_field->field->decimal_separator, $search_field->field->thousands_separator); ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
			</select>
		</div>
	</div>
</div>
<?php endif; ?>