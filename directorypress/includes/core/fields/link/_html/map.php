<?php if ($field->value['url']): ?>
<a
	href="<?php echo esc_url($field->value['url']); ?>"
	<?php if ($field->is_blank) echo 'target="_blank"'; ?>
	<?php if ($field->is_nofollow) echo 'rel="nofollow"'; ?>
><?php if ($field->value['text'] && $field->use_link_text) echo $field->value['text']; else echo $field->value['url']; ?></a>
<?php endif; ?>