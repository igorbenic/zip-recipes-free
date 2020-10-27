<?php if ( empty($recipe->nested_ingredients) && !$recipe->preview ) return;?>
<?php $label_class = apply_filters('zrdn_label_class','zrdn-recipe-label zrdn-instructions-label', 'instructions'); ?>
    <h3 class="<?php echo $label_class?>">
        <?php echo apply_filters('zrdn_label', __('Instructions', 'zip-recipes'), 'instructions')?>
    </h3>
<?php

$list_style = $settings['instructions_list_type'];
$list_type  = \ZRDN\Util::get_list_type( $list_style );
$list_class = \ZRDN\Util::get_list_class( $list_style );
if ( $recipe->preview ) {
        $recipe->nested_instructions[] = array(
	        array(
            'type' => 'default',
            'content' => '{instructions_value}'
        )
    );
}

echo '<' . $list_type . ' class="zrdn-list zrdn-instructions-list '.$list_class.'  zrdn-element_instructions">';
foreach($recipe->nested_instructions as $instruction_lines) {
	foreach ($instruction_lines as $instruction) { ?>

		<?php if ( $instruction['type'] == 'image' ) { ?>
            <img class="<?php if ( $settings['hide_print_image'] )
				echo "zrdn-hide-print" ?>"
                 src="<?php echo $instruction['attributes']['url']; ?>"
				<?php if ( $instruction['attributes']['srcset'] ) { ?> srcset="<?php echo $instruction['attributes']['srcset'] ?>"<?php } ?>
				<?php if ( $instruction['attributes']['sizes'] ) { ?> sizes="<?php echo $instruction['attributes']['sizes'] ?>"<?php } ?>
				<?php if ( $instruction['attributes']['title'] ) { ?> alt="<?php echo $instruction['attributes']['title'] ?>" <?php } ?>
            />
		<?php } elseif ( $instruction['type'] == 'subtitle' ) { ?>
			<?php echo '</' . $list_type . '>'; ?>
            <h4 class="zrdn-subtitle"> <?php echo $instruction['content'] ?></h4>
			<?php echo '<' . $list_type
			           . ' class="zrdn-list zrdn-instructions-list '
			           . $list_class . '"">'; ?>
		<?php } else { ?>
            <li><?php echo $instruction['content']; ?></li>
		<?php }
	}
}
echo '</'.$list_type.'>';

