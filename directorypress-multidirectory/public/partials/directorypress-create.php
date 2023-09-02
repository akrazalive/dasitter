<?php $itab_id = uniqid(); ?>
<div class="directorypress-modal-content wp-clearfix">
	<ul class="nav nav-tabs" id="tabContent">
		
		<li class="active"><a href="#name-<?php echo $itab_id ?>" data-toggle="tab">Name</a></li>
		<li><a href="#slugs-<?php echo $itab_id ?>" data-toggle="tab">Slugs</a></li>
		<li><a href="#resources-<?php echo $itab_id ?>" data-toggle="tab">Resources</a></li>
	</ul>
	<div class="tab-content">
		<form class="add-edit" method="POST" action="">
			<?php wp_nonce_field(DIRECTORYPRESS_PATH, 'directorypress_directorytypes_nonce');?>
			<div class="tab-pane fade active in" id="name-<?php echo $itab_id ?>">
				<div class="row">
					<div class="col-md-12"><label><?php _e('Directory name', 'directorypress-multidirectory'); ?><span class="directorypress-red-asterisk">*</span></label></div>
					<div class="col-md-12"><input name="name" type="text" class="regular-text" value="<?php echo esc_attr($directorytype->name); ?>" /></div>
				</div>
				<div class="row">
					<div class="col-md-12"><label><?php _e('Single form', 'directorypress-multidirectory'); ?><span class="directorypress-red-asterisk">*</span></label></div>
					<div class="col-md-12"><input name="single" type="text" class="regular-text"value="<?php echo esc_attr($directorytype->single); ?>" /></div>
					<div class="col-md-12"><?php directorypress_wpml_translation_notification_string(); ?></div>
				</div>
				<div class="row">
					<div class="col-md-12"><label><?php _e('Plural form', 'directorypress-multidirectory'); ?><span class="directorypress-red-asterisk">*</span></label></div>
					<div class="col-md-12"><input name="plural" type="text" class="regular-text" value="<?php echo esc_attr($directorytype->plural); ?>" /></div>
					<div class="col-md-12"><?php directorypress_wpml_translation_notification_string(); ?></div>
				</div>
			</div>
			<div class="tab-pane fade" id="slugs-<?php echo $itab_id ?>">
				<p><?php _e('Slugs must contain only alpha-numeric characters, underscores or dashes. All slugs must be unique and different.', 'directorypress-multidirectory'); ?></p>
				<div class="row">
					<div class="col-md-12"><label><?php _e('Listing slug', 'directorypress-multidirectory'); ?><span class="directorypress-red-asterisk">*</span></label></div>
					<div class="col-md-12"><input name="listing_slug" type="text" class="regular-text" value="<?php echo esc_attr($directorytype->listing_slug); ?>" /></div>
				</div>
				<div class="row">
					<div class="col-md-12"><label><?php _e('Category slug', 'directorypress-multidirectory'); ?><span class="directorypress-red-asterisk">*</span></label></div>
					<div class="col-md-12"><input name="category_slug" type="text" class="regular-text" value="<?php echo esc_attr($directorytype->category_slug); ?>" /></div>
				</div>
				<div class="row">
					<div class="col-md-12"><label><?php _e('Location slug', 'directorypress-multidirectory'); ?><span class="directorypress-red-asterisk">*</span></label></div>		
					<div class="col-md-12"><input name="location_slug" type="text" class="regular-text" value="<?php echo esc_attr($directorytype->location_slug); ?>" /></div>				
				</div>
				<div class="row">
					<div class="col-md-12"><label><?php _e('Tag slug', 'directorypress-multidirectory'); ?><span class="directorypress-red-asterisk">*</span></label></div>
					<div class="col-md-12"><input name="tag_slug" type="text" class="regular-text" value="<?php echo esc_attr($directorytype->tag_slug); ?>" /></div>			
				</div>
			</div>
			<div class="tab-pane fade" id="resources-<?php echo $itab_id ?>">
				<div class="row">
					<div class="col-md-12"><label><?php _e('Assigned categories', 'directorypress-multidirectory'); ?></label></div>
					<div class="col-md-12"><?php echo directorypress_wpml_supported_settings_description(); ?></div>
					<div class="col-md-12"><?php directorypress_termsSelectList('categories', DIRECTORYPRESS_CATEGORIES_TAX, $directorytype->categories); ?></div>					
				</div>
				<div class="row">					
					<div class="col-md-12"><label><?php _e('Assigned locations', 'directorypress-multidirectory'); ?></label></div>
					<div class="col-md-12"><?php echo directorypress_wpml_supported_settings_description(); ?></div>
					<div class="col-md-12"><?php directorypress_termsSelectList('locations', DIRECTORYPRESS_LOCATIONS_TAX, $directorytype->locations); ?></div>			
				</div>
				<div class="row">
					<div class="col-md-12"><label><?php _e('Listings packages', 'directorypress-multidirectory'); ?></label></div>
					<div class="col-md-12">
						<select multiple="multiple" name="packages[]" class="form-control directorypress-form-group directorypress-select2" style="height: 300px">
							<option value="" <?php if (!$directorytype->packages) echo 'selected'; ?>><?php _e('- Select All -', 'directorypress-multidirectory'); ?></option>
							<?php foreach ($directorypress_object->packages->packages_array AS $package): ?>
								<option value="<?php echo $package->id; ?>" <?php if (in_array($package->id, $directorytype->packages)) echo 'selected'; ?>><?php echo $package->name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>													
				</div>
			</div>
			<div class="id">
				<input type="hidden" name="id" value="">
			</div>
		</form>
	</div>
</div>