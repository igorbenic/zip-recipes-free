<?php
namespace ZRDN;
?>
<form action="" method="POST" id="zrdn-save-template-settings">

    <?php


    $field  = ZipRecipes::$field;
    $fields = Util::get_fields('template');

    foreach ( $fields as $fieldname => $field_args ) {
	    $field->get_field_html( $field_args , $fieldname);
    }
    ?>
    <?php wp_nonce_field('zrdn_edit_template', 'zrdn_edit_template_nonce')?>
    <input type="hidden" value="0" name="zrdn-reset-template">

    <div class="zrdn-row-bottom">
        <div class="zrdn-button-container">
            <button type="button" class="button button-primary zrdn-save-template-settings"><?php _e( "Save", "zip-recipes" ) ?></button>
            <a href="#" class="zrdn-reset-template-settings"><?php _e( "Reset", "zip-recipes" ) ?></a>
        </div>
    </div>

</form>
