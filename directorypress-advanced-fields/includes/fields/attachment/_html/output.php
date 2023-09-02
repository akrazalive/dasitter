<?php if ($file): ?>
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
		<a href="<?php echo esc_url(wp_get_attachment_url($file->ID)); ?>" target="_blank"><?php if ($field->value['text'] && $field->use_text) echo $field->value['text']; else echo basename(wp_get_attachment_url($file->ID)); ?></a>
	</span>
</div>
<?php endif; ?>