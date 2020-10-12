<?php if($recipe->notes){
	$label_class = apply_filters( 'zrdn_label_class',
		'zrdn-recipe-label zrdn-notes-label', 'notes' );

	?>
    <h3 class="<?php echo $label_class?>"><?php echo apply_filters('zrdn_label', __('Notes', 'zip-recipes'), 'notes' )?></h3>
	<p class="zrdn-element_notes">{formatted_notes}</p>
<?php } ?>