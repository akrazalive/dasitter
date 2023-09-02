<?php global $DIRECTORYPRESS_ADIMN_SETTINGS, $directorypress_object; ?>
<?php if (count($search_field->field->selection_items)): ?>
	<?php //$gap_in_fields = $DIRECTORYPRESS_ADIMN_SETTINGS['gap_in_fields']; ?>
	<?php  if ($columns == 1) $col_md = 6; else $col_md = 4; 
	
	if ($directorypress_directory_handler = $directorypress_object->directorypress_get_property_of_shortcode(DIRECTORYPRESS_MAIN_SHORTCODE) && $DIRECTORYPRESS_ADIMN_SETTINGS['archive_page_style'] != 2){
		$field_width = $search_field->field->fieldwidth_archive;
	}elseif($directorypress_directory_handler = $directorypress_object->directorypress_get_property_of_shortcode(DIRECTORYPRESS_MAIN_SHORTCODE) && $DIRECTORYPRESS_ADIMN_SETTINGS['archive_page_style'] == 2){
		$field_width = '100';
	}else{
		$field_width = $search_field->field->fieldwidth;
	}
	?>
	<div class="cz-checkboxes search-element-col directorypress-field-search-block-<?php echo $search_field->field->id; ?> directorypress-field-search-block-<?php echo $search_form_id; ?> directorypress-field-search-block-<?php echo $search_field->field->id; ?>_<?php echo $search_form_id; ?> pull-left clearfix" style=" width:<?php echo $field_width; ?>%; padding:0 <?php echo $gap_in_fields; ?>px;">
		<?php if(!$search_field->field->is_hide_name_on_search): ?>
			<div class="search-content-filed-label">
				<label><?php echo $search_field->field->name; ?></label>
			</div>
		<?php endif; ?>
		<?php
		if ($search_field->search_input_mode == 'checkboxes' || $search_field->search_input_mode =='radiobutton'):
			$i = 1;
			while ($i <= ($columns+1)): ?>
				<?php $j = 1; ?>
				<?php foreach ($search_field->field->selection_items AS $key=>$item): ?>
					<?php if ($i == $j): ?>
						<div class="<?php if ($search_field->search_input_mode =='checkboxes'): ?>search-checkbox<?php elseif ($search_field->search_input_mode =='radiobutton'): ?>input-checkbox<?php endif; ?> ">
							<label> 
								<?php if ($search_field->search_input_mode =='checkboxes'): ?>
								<input type="checkbox" name="field_<?php echo $search_field->field->slug; ?>[]" value="<?php echo esc_attr($key); ?>" <?php if (in_array($key, $search_field->value)) echo 'checked'; ?> class="selectpicker" />
								<?php elseif ($search_field->search_input_mode =='radiobutton'): ?>
								<input type="radio" name="field_<?php echo $search_field->field->slug; ?>" value="<?php echo esc_attr($key); ?>" <?php if (in_array($key, $search_field->value)) echo 'checked'; ?> />
								<?php endif; ?>
								<span class="checkbox-item-name"><?php echo $item; ?><?php if ($search_field->items_count && $key !== ""): if (isset($items_count_array[$key])) echo " (".$items_count_array[$key].")"; else echo " (0)"; endif; ?></span>
								<span class="search-checkbox-item"></span>
							</label>
						</div>
					<?php endif; ?>
					<?php $j++; ?>
					<?php if ($j > ($columns+1)) $j = 1; ?>
				<?php endforeach; ?>
				<?php $i++; ?>
			<?php endwhile; ?>
		<?php elseif ($search_field->search_input_mode == 'selectbox'): ?>
			<select name="field_<?php echo $search_field->field->slug; ?>" class=" cs-select cs-skin-elastic directorypress-select2" style="width: 100%;">
				<option value="" <?php if (!$search_field->value) echo 'disabled selected'; ?>><?php printf(__('- Select %s -', 'directorypress-advanced-fields'), $search_field->field->name); ?></option>
				<?php foreach ($search_field->field->selection_items AS $key=>$item): ?>
					<option value="<?php echo esc_attr($key); ?>" <?php if (in_array((string)$key, $search_field->value, true)) echo 'selected'; ?>><?php echo $item; ?><?php if ($search_field->items_count): if (isset($items_count_array[$key])) echo " (".$items_count_array[$key].")"; else echo " (0)"; endif; ?></option>
				<?php endforeach; ?>
			</select>
		<?php endif; ?>
	</div>
<?php endif; ?>