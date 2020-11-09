<?php
if( !$recipe->is_featured_post_image ) {?>
	<?php if( $recipe->recipe_image_id !==FALSE || $recipe->preview ) {
	    ?>
        <div class="zrdn-recipe-image <?php if($settings['hide_print_image']) echo "zrdn-hide-print"?> zrdn-element_recipe_image">
            <?php
                if ( $recipe->preview || $recipe->recipe_image_id === 0 ) {
                    echo '<img src="'.$recipe->recipe_image.'">';
                } else {
	                $html = wp_get_attachment_image( $recipe->recipe_image_id, 'zrdn_recipe_image_main' );
	                //make sure we have an alt image
	                $image_alt = get_post_meta( $recipe->recipe_image_id, '_wp_attachment_image_alt', true);
	                if (strlen($image_alt) == 0) {
		                $image_alt = $recipe->recipe_title;
		                $html = preg_replace( '/(alt=")(.*?)(")/i', '$1'.esc_attr( $image_alt ).'$3', $html );
	                }
	                echo $html;
                }
                ?>
	    </div>
	<?php } ?>
<?php } ?>
