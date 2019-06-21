<?php
namespace ZRDN;

//if (!current_user_can('edit_posts')) wp_die("You do not have permission to do this");

$recipe_id = false;
if (isset($_GET['id'])) {
    $recipe_id = intval($_GET['id']);
}

/**
 * If a post_id is passed, we will link this recipe to this post id.
 *
 * If the passed post_id does not exist yet, we will create it, and link it.
 *
 */

$link_to_post_id = false;
if (isset($_GET['post_id']) && isset($_GET['post_type'])) {
    $post_type = sanitize_title($_GET['post_type']);
    $link_to_post_id = intval($_GET['post_id']);
    $post = get_post($link_to_post_id);
    if (!$post) {
        //post does not exist yet. Create it, so we can link to it.
        $args = array(
            'post_type' => $post_type,
        );
        $link_to_post_id = wp_insert_post($args);


    }
}




/**
 * - nutrition data generatie testen met !, *, _
 * - Test review metabox ivm enqueue_assets hook changes

 * - discount code 24 hours in free
 * - upsell author
 * - upsell nutrition data
 */

?>
<div class="wrap">
    <?php
    //load the recipe
    if (isset($_GET['recipe_id'])) $recipe_id = intval($_GET['recipe_id']);
    if (isset($_POST['zrdn_recipe_id'])) $recipe_id = intval($_POST['zrdn_recipe_id']);

    $recipe = new Recipe($recipe_id);
    if (strlen($recipe->recipe_title)==0) $recipe->recipe_title = __("New recipe", "zip-recipes");
    $field = ZipRecipes::$field;

    ?>
    <?php echo apply_filters('zrdn__editpage_promo', ''); ?>

    <form id='recipe-settings' action="" method="post">
        <?php wp_nonce_field('zrdn_save_recipe', 'zrdn_save_recipe'); ?>

        <?php if (!$recipe_id) { ?>
            <input type="hidden" value="1" name="zrdn_add_new">
        <?php } ?>

        <input type="hidden" value="<?php echo $recipe_id?>" name="zrdn_recipe_id">

        <?php if ($link_to_post_id) { ?>
            <input type="hidden" value="<?php echo $link_to_post_id ?>" name="post_id">
        <?php } ?>


        <?php
        $active_tab =  isset($_POST['zrdn_active_tab']) ? sanitize_title($_POST['zrdn_active_tab']) : 'general';
        ?>
        <input type="hidden" value="<?php echo $active_tab?>" name="zrdn_active_tab">

        <div class="zrdn-tab">
            <button class="zrdn-tablinks <?php if ($active_tab=='general') echo 'active'?>" type="button"
                    data-tab="general"><?php _e("General", 'zip-recipes') ?></button>

            <button class="zrdn-tablinks <?php if ($active_tab=='nutrition') echo 'active'?>" type="button" data-tab="nutrition">
                <?php _e("Nutrition", 'zip-recipes') ?>
            </button>
        </div>

        <div class="zrdn-container">
            <div class="zrdn-column">
                <!-- Tab content -->
                <div class="zrdn-recipe-save-button">
                    <input type="submit" class="button button-primary" value="<?php _e('Save', 'zip-recipes') ?>">
                </div>
                <div id="general" class="zrdn-tabcontent <?php if ($active_tab=='general') echo 'active'?>">

                    <h3><?php _e("General", 'zip-recipes') ?></h3>
                    <?php //offer option to go to post if post_id is linked.?>
                    <?php if ($recipe->post_id) {
                            //$post = get_post($recipe->post_id);
                            //if ($post) {
                                //little hack to get the preview working.
//                                $_POST['post_ID'] = $recipe->post_id;
//                                $_POST['post_type'] = get_post_type($recipe->post_id);
//                                //$_POST['post_type'] = $post->post_type;
//                                $preview_url = post_preview();

                                if ( get_post_type($recipe->post_id) === 'trash') {
                                    notice(__("This recipe is linked to a post, but this post has been trashed. You can untrash the post, or link the recipe to another post or page", "zip-recipes"), 'warning');
                                } else {
                                    ?>
                                    <a class="button button-default"
                                       href="<?php echo add_query_arg(array('post' => $recipe->post_id, 'action' => 'edit'), admin_url('post.php')) ?>"><?php _e("Edit linked post", "zip-recipes") ?></a>
                                    <a class="button button-default"
                                       href="<?php echo add_query_arg(array('page' => 'zrdn-recipes', 'id' => $recipe->recipe_id, 'action' => 'unlink'), admin_url()) ?>"><?php _e("Unlink from post", "zip-recipes") ?></a>
                                   <?php /*
                                    <a class="button button-default" target="_blank"
                                       href="<?php echo $preview_url ?>"><?php _e("Preview", "zip-recipes") ?></a>
                                         */?>
                                    <?php
                                }
                                //}
                        } ?>
                    <?php
                    if ($recipe->is_featured_post_image){
                        notice(__("Your recipe image is the same as your post image. The image will be hidden on the front end.", "zip-recipes"), 'warning');
                    }

                    $fields = array(
                        array(
                            'type' => 'hidden',
                            'fieldname' => 'recipe_image',
                            'value'=> $recipe->recipe_image,
                        ),

                        array(
                            'type' => 'hidden',
                            'fieldname' => 'recipe_image_id',
                            'value' => $recipe->recipe_image_id,
                        ),

                        array(
                            'type' => 'text',
                            'fieldname' => 'recipe_title',
                            'value' => $recipe->recipe_title,
                            'label' => __("Title", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'textarea',
                            'required' => true,
                            'fieldname' => 'ingredients',
                            'value' => $recipe->ingredients,
                            'label' => __("Ingredients", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'textarea',
                            'fieldname' => 'instructions',
                            'value' => $recipe->instructions,
                            'label' => __("Instructions", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'category',
                            'value' => $recipe->category,
                            'label' => __("Category", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'cuisine',
                            'value' => $recipe->cuisine,
                            'label' => __("Cuisine", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'editor',
                            'fieldname' => 'summary',
                            'value' => $recipe->summary,
                            'label' => __("Description", 'zip-recipes'),
                            'media' => false,
                        ),
                        array(
                            'type' => 'time',
                            'fieldname' => 'prep_time',
                            'value' => $recipe->prep_time,
                            'label' => __("Prep time", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'time',
                            'fieldname' => 'cook_time',
                            'value' => $recipe->cook_time,
                            'label' => __("Cook time", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'editor',
                            'fieldname' => 'notes',
                            'value' => $recipe->notes,
                            'label' => __("Notes", 'zip-recipes'),
                            'media' => false,
                        ),
                        array(
                            'type' => 'number',
                            //'required' => true,
                            'fieldname' => 'yield',
                            'value' => $recipe->yield,
                            'label' => __("Yields", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'serving_size',
                            'value' => $recipe->serving_size,
                            'label' => __("Serving size", 'zip-recipes'),
                        ),
                    );
                    $fields = apply_filters('zrdn_edit_fields', $fields, $recipe);
                    foreach ($fields as $field_args) {
                        $field->get_field_html($field_args);
                    }
                    ?>

                    <?php do_action('zrdn_after_fields', $recipe) ?>
                </div>

                <!-- Tab content -->
                <div id="nutrition" class="zrdn-tabcontent <?php if ($active_tab=='nutrition') echo 'active'?>">

                    <h3><?php _e("Nutrition", 'zip-recipes') ?></h3>

                    <?php do_action('zrdn_nutrition_fields', $recipe) ?>

                    <?php $nutrition_fields = array(
                        array(
                            'type' => 'text',
                            'fieldname' => 'calories',
                            'value' => $recipe->calories,
                            'label' => __("Calories", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'carbs',
                            'value' => $recipe->carbs,
                            'label' => __("Carbs", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'protein',
                            'value' => $recipe->protein,
                            'label' => __("Protein", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'fiber',
                            'value' => $recipe->fiber,
                            'label' => __("Fiber", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'sugar',
                            'value' => $recipe->sugar,
                            'label' => __("Sugar", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'sodium',
                            'value' => $recipe->sodium,
                            'label' => __("Sodium", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'fat',
                            'value' => $recipe->fat,
                            'label' => __("Fat", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'saturated_fat',
                            'value' => $recipe->saturated_fat,
                            'label' => __("Saturated fat", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'trans_fat',
                            'value' => $recipe->trans_fat,
                            'label' => __("Trans fat", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'cholesterol',
                            'value' => $recipe->cholesterol,
                            'label' => __("Cholesterol", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'vitamin_c',
                            'value' => $recipe->vitamin_c,
                            'label' => __("Vitamin C", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'vitamin_a',
                            'value' => $recipe->vitamin_a,
                            'label' => __("Vitamin A", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'iron',
                            'value' => $recipe->iron,
                            'label' => __("Iron", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'calcium',
                            'value' => $recipe->calcium,
                            'label' => __("Calcium", 'zip-recipes'),
                        ),

                        array(
                            'type' => 'hidden',
                            'fieldname' => 'nutrition_label',
                            'value' => $recipe->nutrition_label,
                            //'label' => __("Nutrition label", 'zip-recipes'),
                        )
                    );

                    $nutrition_fields = apply_filters('zrdn_edit_nutrition_fields', $nutrition_fields, $recipe);
                    foreach ($nutrition_fields as $field_args) {
                        $field->get_field_html($field_args);
                    }
                    ?>


                </div><!--tab content -->
            </div>

            <div class="zrdn-column">
                <div id="zrdn-preview">
                    <?php
                    $empty_recipe = new Recipe();
                    $empty_recipe->load_placeholders();
                    echo ZipRecipes::zrdn_format_recipe($empty_recipe);
                    ?>
                </div>
            </div>


        </div><!-- container -->




    </form>
</div>