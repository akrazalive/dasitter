<?php if ($listing->locations): ?>
<div class="directorypress-field-item directorypress-field-type-<?php echo $field->type; ?>">
	<?php if ($field->icon_image || !$field->is_hide_name): ?>
	<span class="field-label">
		<?php if ($field->icon_image): ?>
		<span class="directorypress-field-icon <?php echo $field->icon_image; ?>"></span>
		<?php endif; ?>
		<?php if (!$field->is_hide_name): ?>
		<span class="directorypress-field-title"><?php echo $field->name?>:</span>
		<?php endif; ?>
	</span>
	<?php endif; ?>
	<span class="field-content">
	<?php foreach ($listing->locations AS $location): ?>
		<span class="directorypress-location" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
			<?php if ($location->map_coords_1 && $location->map_coords_2): ?><span class="directorypress-show-on-map" data-location-id="<?php echo $location->id; ?>"><?php endif; ?>
			<?php echo $location->get_full_address(); ?>
			<?php if ($location->map_coords_1 && $location->map_coords_2): ?></span><?php endif; ?>
		</span>
		<?php //endif; ?>
	<?php endforeach; ?>
	</span>
</div>
<?php endif; ?>