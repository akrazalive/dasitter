<?php if ($file): ?>
<a href="<?php echo esc_url($file->guid); ?>" target="_blank"><?php if ($field->value['text'] && $field->use_text) echo $field->value['text']; else echo basename($file->guid); ?></a>
<?php endif; ?>