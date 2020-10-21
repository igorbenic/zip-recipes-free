<?php
    namespace ZRDN;
    $selected_recipe_id=false;
    $post_id = isset($_GET['post']) ? intval($_GET['post']) : false;
    $post_type = isset($_GET['post_type']) ? sanitize_title($_GET['post_type']) : 'post';

    if ($post_id){
        //get the attached recipe
        $recipe = new Recipe(false, $post_id);
        $post_type = get_post_type($post_id);
        $recipe->load();
        $selected_recipe_id = $recipe->recipe_id;
    }
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            'use strict';
            var recipe_dropdown;
            var regex_legacy = /(\[amd-zlrecipe-recipe:)[0-9]\d*(.*\])/i;
            var regex = /(\[zrdn-recipe.*id=['|"])[0-9]\d*(.*\])/i;
            var unlinked_recipes = $('#zrdn_active_recipe_unlinked');
            var all_recipes = $('#zrdn_active_recipe');

            /**
             * Toggle linked and unlinked recipes
             */

             $(document).on('click', '#zrdn_selection_type', function(){
                 unlinked_recipes.toggle();
                 all_recipes.toggle();

                 if (all_recipes.is(':visible')){
                     $("#zrdn_unlink_warning").fadeIn();
                 } else {
                     $("#zrdn_unlink_warning").hide();
                 }

             });

            /**
             * enable select button on selection change
             */
            $(document).on('change', '#zrdn_active_recipe_unlinked', zrdn_update_selection);
            $(document).on('change', '#zrdn_active_recipe', zrdn_update_selection);

            function zrdn_update_selection(){
                if (!unlinked_recipes.is(':hidden')){
                    recipe_dropdown = unlinked_recipes;
                } else {
                    recipe_dropdown = all_recipes;
                }

                var recipe_id = recipe_dropdown.val();
                var content = tmce_getContent();
                var oldContent = content;

                var matches = content.match(regex);
                if (matches){
                    content = content.replace(regex, '$1'+recipe_id+'$2');
                } else {
                    content = content+'[zrdn-recipe id="'+recipe_id+'"]';
                }

                var matches_legacy = content.match(regex_legacy);
                if (matches_legacy){
                    content = content.replace(regex_legacy, '$1'+recipe_id+'$2');
                }

                if (oldContent !== content) {
                    tmce_setContent(content);
                    $("#zrdn_update_feedback").fadeIn().delay(3000).fadeOut(800);
                    $("#zrdn-edit-recipe").addClass('disabled');
                    $("#zrdn-create-recipe").addClass('disabled');
                    $("#zrdn-edit-recipe").removeAttr('href');
                    $("#zrdn-create-recipe").removeAttr('href');
                }

            }
            function tmce_getContent(editor_id, textarea_id) {
                if (typeof editor_id == 'undefined') editor_id = wpActiveEditor;
                if (typeof textarea_id == 'undefined') textarea_id = editor_id;

                if (jQuery('#wp-' + editor_id + '-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id)) {
                    return tinyMCE.get(editor_id).getContent();
                } else {
                    return jQuery('#' + textarea_id).val();
                }
            }

            function tmce_setContent(content, editor_id, textarea_id) {
                if (typeof editor_id == 'undefined') editor_id = wpActiveEditor;
                if (typeof textarea_id == 'undefined') textarea_id = editor_id;

                if (jQuery('#wp-' + editor_id + '-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id)) {
                    return tinyMCE.get(editor_id).setContent(content);
                } else {
                    return jQuery('#' + textarea_id).val(content);
                }
            }


        });

    </script>

<div>

    <?php
    /**
     * Several options here:
     *  - An existing recipe is edited.
     *  - Existing recipe is replaced with another existing one, with the "select" button. -> we need to update the recipe link table and the editor contents.
     *  - Existing recipe is replace with a new one -> we need to
     */
    ?>

    <form id="recipe-form" class="entry-wrapper" enctype='multipart/form-data' method='post' action='' name='recipe_form'>
        <?php
        global $wpdb;
        $table = $wpdb->prefix . "amd_zlrecipe_recipes";

        $recipes = $wpdb->get_results("SELECT * FROM $table");
        $recipes = wp_list_pluck($recipes, 'recipe_title', 'recipe_id');

        $preview_post_id = Util::get_preview_post_id(false);
        $unlinked_recipes = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table where post_id = NULL or post_id = 0 or post_id = %s or post_id = %s", $post_id, $preview_post_id));
        $unlinked_recipes = wp_list_pluck($unlinked_recipes, 'recipe_title', 'recipe_id');
        ?>
        <div id="zrdn_update_feedback" style="display:none"><?php zrdn_notice(__("Recipe selection updated. Time to save!","zip-recipes"), 'success', true, true)?></div>
        <div id="zrdn_unlink_warning" style="display:none"><?php zrdn_notice(__("Selecting a recipe that is linked to another post will unlink it from that post!","zip-recipes"), 'warning', true, true)?></div>
        <label><input id="zrdn_selection_type" type="checkbox" value="1" checked><?php _e("Show only recipes without post","zip-recipes")?></label>
        <div>
        <select id="zrdn_active_recipe" style="display:none">
            <option value=""><?php _e('No recipe selected', 'zip-recipes')?></option>
            <?php foreach($recipes as $recipe_id => $title){?>
            <option value="<?php echo $recipe_id?>"  <?php if ($selected_recipe_id==$recipe_id) echo "selected"?>><?php echo $title?> </option>
            <?php } ?>
        </select>
    </div>
        <div>
        <select id="zrdn_active_recipe_unlinked">
            <option value=""><?php _e('No recipe selected', 'zip-recipes')?></option>
            <?php foreach($unlinked_recipes as $recipe_id => $title){?>
                <option value="<?php echo $recipe_id?>"  <?php if ($selected_recipe_id==$recipe_id) echo "selected"?>><?php echo $title?> </option>
            <?php } ?>
        </select>
        </div>

        <?php
        if ($selected_recipe_id){
            $edit_recipe_url = add_query_arg(array('page'=>'zrdn-recipes', 'id'=>$selected_recipe_id), admin_url());
            ?>
            <p class="post-attributes-label-wrapper">
                <a href="<?php echo $edit_recipe_url?>" id="zrdn-edit-recipe" class="button"><?php _e("Edit recipe","zip-recipes")?></a>
            </p>
            <?php
        }

        ?>
        <p class="post-attributes-label-wrapper">
            <a id="zrdn-create-recipe" href="<?php echo add_query_arg(array('page'=>'zrdn-recipes', 'action'=>'new', 'post_id'=>$post_id, 'post_type'=>$post_type), admin_url());?>" class="button" ><?php _e("Create and insert new recipe","zip-recipes")?></a>
        </p>

        <?php if ($selected_recipe_id) echo '<div>'.__('Creating and inserting a new recipe will unlink the current recipe from this post.', 'zip-recipes').'</div>'?>

    </form>
</div>
