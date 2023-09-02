<?php if ($field->value): ?>
	<ul class="field-content">
	<?php foreach ($field->value AS $key): ?>
		<li><?php echo $field->selection_items[$key]; ?></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>