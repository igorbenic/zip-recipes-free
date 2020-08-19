<style>
    <?php
       if (isset($settings['background_color'])) {
           echo "#zrdn-recipe-container {background-color:".esc_html($settings['background_color']).";}";
       }
       if (isset($settings['border_color'])) {
           echo "#zrdn-recipe-container {border-color:".esc_html($settings['border_color']).";}";
       }
       if (isset($settings['text_color'])) {
           echo "#zrdn-recipe-container, #zrdn-recipe-container h2, #zrdn-recipe-container h3, #zrdn-recipe-container h4 {color:".esc_html($settings['text_color']).";}";
       }?>
       <?php if (isset($settings['primary_color'])) {
           $primary_color = esc_html($settings['primary_color']);
           ?>
            #zrdn-recipe-container ol.zrdn-bordered li:before,
            #zrdn-recipe-container ul.zrdn-bordered li:before{
                border: 2px solid <?php echo $primary_color?>;
                color: <?php echo $primary_color?>;
            }
            #zrdn-recipe-container ol.zrdn-solid li:before,
            #zrdn-recipe-container ul.zrdn-solid li:before{
                background-color: <?php echo $primary_color?>;
            }
            #zrdn-recipe-container ul.bullets li:before,
            #zrdn-recipe-container ol.zrdn-counter li:before,
            #zrdn-recipe-container ul.zrdn-counter li:before {
                color: <?php echo $primary_color?>;
            }
            #zrdn-recipe-container .zrdn-tag-item a, #zrdn-recipe-container .zrdn-tag-item{
                color:<?php echo $primary_color?>;
            }
       <?php }
       if (isset($settings['box_shadow']) && $settings['box_shadow']) {
           ?>
            #zrdn-recipe-container {
                box-shadow: 0 1px 1px rgba(0,0,0,0.12), 0 2px 2px rgba(0,0,0,0.12), 0 4px 4px rgba(0,0,0,0.12), 0 8px 8px rgba(0,0,0,0.12), 0 16px 16px rgba(0,0,0,0.12);
            }
       <?php }

        if (isset($settings['border_style'])) {
           echo "#zrdn-recipe-container {border-style:".esc_html($settings['border_style']).";}";
        }
        if (isset($settings['link_color'])) {
           echo "#zrdn-recipe-container a {color:".esc_html($settings['link_color']).";}";
        }
        if (isset($settings['border_width'])) {
            echo "#zrdn-recipe-container {border-width:".esc_html($settings['border_width'])."px;}";
        }
        if (isset($settings['border_color'])) {
            echo "#zrdn-recipe-container {border-color:".esc_html($settings['border_color']).";}";
        }
        if (isset($settings['border_radius'])) {
            echo "#zrdn-recipe-container {border-radius:".esc_html($settings['border_radius'])."px;}";
        }

        if($settings['hide_notes_label']){
            echo ".zrdn-notes-label {display:none;}";
        }

        if($settings['hide_instructions_label']){
            echo ".zrdn-instructions-label {display:none;}";
        }
        if($settings['hide_ingredients_label']){
            echo ".zrdn-ingredients-label {display:none;}";
        }
        if($settings['hide_tags_label']){
            echo ".zrdn-tags-label {display:none;}";
        }
        if($settings['hide_social_label']){
            echo ".zrdn-social-sharing-label {display:none;}";
        }

    ?>

</style>
<?php  do_action('zrdn_before_recipe', $settings, $recipe);?>
<div id="zrdn-recipe-container" class="<?php echo $settings['template']?> zrdn-recipe-{recipe_id} zrdn-jump-to-link" >
    {blocks}
</div>