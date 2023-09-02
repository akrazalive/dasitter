<?php if (has_term('', DIRECTORYPRESS_TAGS_TAX, $listing->post->ID)): ?>
<div class="directorypress-field-item directorypress-field-type-<?php echo $field->type; ?>">
	<?php if ($field->icon_image || !$field->is_hide_name): ?>
	<span class="field-label">
		<?php if ($field->icon_image): ?>
		<span class="directorypress-field-icon fa fa-lg <?php echo $field->icon_image; ?>"></span>
		<?php endif; ?>
		<?php if (!$field->is_hide_name): ?>
		<span class="directorypress-field-title"><?php echo $field->name?>:</span>
		<?php endif; ?>
	</span>
	<?php endif; ?>
	<span class="field-content">
	<?php //echo get_the_term_list($listing->post->ID, DIRECTORYPRESS_TAGS_TAX, '', ', ', ''); ?>
		<?php
		$terms = get_the_terms($listing->post->ID, DIRECTORYPRESS_TAGS_TAX);
		foreach ($terms as $term): ?>
			<span class="directorypress-label directorypress-label-primary"><a href="<?php echo get_term_link($term, DIRECTORYPRESS_TAGS_TAX); ?>" rel="tag"><?php echo $term->name; ?></a>&nbsp;&nbsp;<span class="glyphicon glyphicon-tag"></span></span>
		<?php endforeach; ?>
	</span>
</div>
<?php endif; ?>