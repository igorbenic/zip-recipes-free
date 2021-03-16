<?php
namespace ZRDN;
do_action('zrdn_enqueue_scripts');
$recipe_id = false;
if (isset($_GET['id'])) {
    $recipe_id = intval($_GET['id']);
}
$zrdn_popup=false;
if (isset($_GET['popup']) && $_GET['popup']) {
    $zrdn_popup = true;
}


/**
 * If a post_id is passed, we will link this recipe to this post id.
 *
 * If the passed post_id does not exist yet, we will create it, and link it.
 *
 */

$link_to_post_id = false;
if (isset($_GET['post_id'])) {
    $link_to_post_id = intval($_GET['post_id']);
    $post = get_post($link_to_post_id);
    if (!$post && isset($_GET['post_type'])) {
        $post_type = sanitize_title($_GET['post_type']);

        //post does not exist yet. Create it, so we can link to it.
        //we don't do this if it's a popup (post_type not set). It's not needed, and might cause issues.
        $args = array(
            'post_type' => $post_type,
        );
        $link_to_post_id = wp_insert_post($args);
    }
}

?>

<div class="wrap edit-recipe" id="zip-recipes">
	<?php //this header is a placeholder to ensure notices do not end up in the middle of our code ?>
    <h1 class="zrdn-notice-hook-element"></h1>

	<?php Util::settings_header(apply_filters('zrdn_tabs', array()), false)?>

    <?php

    //load the recipe
    if (isset($_GET['recipe_id'])) $recipe_id = intval($_GET['recipe_id']);
    if (isset($_POST['zrdn_recipe_id'])) $recipe_id = intval($_POST['zrdn_recipe_id']);

    $recipe = new Recipe($recipe_id);
    if (strlen($recipe->recipe_title)==0) {
        $recipe->recipe_title = __("New recipe", "zip-recipes");

        //when empty, we grab recipe title from post
        if ($link_to_post_id){
            $post = get_post($link_to_post_id);
            if ($post && strlen($post->post_title)>0) $recipe->recipe_title = $post->post_title;
        }
    }

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
        <?php
        if (isset($_POST['zrdn_save_recipe'])) {
            zrdn_notice(__("Settings saved!", "zip-recipes"), 'success', true, false, true);
        }
        ?>
        <div id="zrdn-show-field-error" class="zrdn-hidden">
            <?php zrdn_notice(__("Not all required fields are filled out!", "zip-recipes"), 'warning', true, false, false); ?>
        </div>

        <div class="zrdn-container">
            <div class="zrdn-column">
                <div class="zrdn-tab">
                    <button class="zrdn-tablinks <?php if ($active_tab=='general') echo 'active'?>" type="button"
                            data-tab="general"><?php _e("General", 'zip-recipes') ?></button>

                    <button class="zrdn-tablinks <?php if ($active_tab=='nutrition') echo 'active'?>" type="button" data-tab="nutrition">
			            <?php _e("Nutrition", 'zip-recipes') ?>
                    </button>
                    <button class="zrdn-tablinks <?php if ($active_tab=='snippets') echo 'active'?>" type="button" data-tab="snippets">
			            <?php _e("Rich Snippets", 'zip-recipes') ?>
                    </button>
                    <button class="zrdn-tablinks <?php if ($active_tab=='misc') echo 'active'?>" type="button" data-tab="misc">
			            <?php _e("Misc", 'zip-recipes') ?>
                    </button>
                </div>

                <!-- Tab content -->
                <div class="zrdn-recipe-save-button">
                    <button type="submit" class="button button-primary save"><?php _e('Save', 'zip-recipes') ?></button>
                    <input type="submit" class="button button-primary exit" value="<?php _e('Save and close', 'zip-recipes') ?>">
                </div>
                <div id="general" class="zrdn-tabcontent <?php if ($active_tab=='general') echo 'active'?>">
                    <?php
                    $preview_post_id = get_option('zrdn_preview_post_id');
                    if ( !$zrdn_popup && $recipe->post_id && $recipe->post_id !== $preview_post_id ){
                                if ( get_post_type($recipe->post_id) === 'trash') {
                                    zrdn_notice(__("This recipe is linked to a post, but this post has been trashed. You can untrash the post, or link the recipe to another post or page", "zip-recipes"), 'warning');
                                } else {
                                    ?>
                                    <a class="button button-default"
                                       href="<?php echo add_query_arg(array('post' => $recipe->post_id, 'action' => 'edit'), admin_url('post.php')) ?>"><?php _e("Edit linked post", "zip-recipes") ?></a>
                                    <a class="button button-default"
                                       href="<?php echo add_query_arg(array('page' => 'zrdn-recipes', 'id' => $recipe->recipe_id, 'action' => 'unlink'), admin_url()) ?>"><?php _e("Unlink from post", "zip-recipes") ?></a>
                                   <?php if (get_post_status($recipe->post_id)==='publish') { ?>
                                    <a class="button button-default" target="_blank"
                                       href="<?php echo get_permalink($recipe->post_id) ?>"><?php _e("View", "zip-recipes") ?></a>

                                    <?php }
                                }
                        } ?>
                    <?php
                    if ($recipe->is_featured_post_image && Util::get_option('hide_on_duplicate_image') ){
                        zrdn_notice(__("Your recipe image is the same as your post image. The image will be hidden on the front end.", "zip-recipes") );
                    }
                    $tags = wp_get_post_tags( $recipe->post_id );
                    if ($recipe->post_id && !$tags){
                        zrdn_notice(
                                sprintf(__("You haven't added any tags to your post yet. In your post you can %sadd%s some tags relevant to this recipe. These will get added as keywords to your recipes microdata.", "zip-recipes"),
                            '<a href="'.add_query_arg(array('post' => $recipe->post_id, 'action' => 'edit'), admin_url('post.php')).'">','</a>')
                                , 'notice', true, false, false);
                    }

                    $fields = array(
                        array(
                            'type'                  => 'upload',
                            'fieldname'             => 'recipe_image',
                            'low_resolution_notice' => __( "Low resolution, please upload a better quality image.",
                                'zip-recipes' ),
                            'size'                  => 'zrdn_recipe_image',
                            'value'                 => $recipe->recipe_image,
                            'thumbnail_id'          => $recipe->recipe_image_id,
                            'label'                 => __( "Recipe image",
                                'zip-recipes' ),
                        ),
                        array(
                            'type' => 'text',
                            'fieldname' => 'recipe_title',
                            'value' => $recipe->recipe_title,
                            'label' => __("Title", 'zip-recipes'),
                            'placeholder' => __('My recipe','zip-recipes'),
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
		                    'type' => 'time',
		                    'fieldname' => 'wait_time',
		                    'value' => $recipe->wait_time,
		                    'label' => __("Wait time", 'zip-recipes'),
	                    ),

                        array(
                            'type' => 'text',
                            'fieldname' => 'serving_size',
                            'value' => $recipe->serving_size,
                            'label' => __("Serving size", 'zip-recipes'),
                            'placeholder' => __('1 slice','zip-recipes'),
                        ),

                        array(
                            'type' => 'text',
                            'fieldname' => 'yield',
                            'value' => $recipe->yield,
                            'label' => __("Servings", 'zip-recipes'),
                            'placeholder' => __('4 persons','zip-recipes'),
                            'help' => __("How many people this recipe serves", 'zip-recipes'),

                        ),

                        array(
                            'type' => 'textarea',
                            'required' => true,
                            'fieldname' => 'ingredients',
                            'value' => $recipe->ingredients,
                            'label' => __("Ingredients", 'zip-recipes'),
                            'comment' =>sprintf(__("Put each item on a separate line. There is no need to use bullets for your ingredients. You can also create labels, [hyperlinks|domain.com], *bold*, _italic_ effects and even add images! %sRead more%s", 'zip-recipes'),'<a target="_blank" href="https://ziprecipes.net/knowledge-base/formatting/">','</a>'),
                        ),
                        array(
                            'type' => 'textarea',
                            'fieldname' => 'instructions',
                            'value' => $recipe->instructions,
                            'label' => __("Instructions", 'zip-recipes'),
                            'comment' =>sprintf(__("Put each item on a separate line. There is no need to use bullets for your instructions. You can also create labels, [hyperlinks|domain.com], *bold*, _italic_ effects and even add images! %sRead more%s", 'zip-recipes'),'<a target="_blank" href="https://ziprecipes.net/knowledge-base/formatting/">','</a>'),
                        ),

                        array(
                            'type' => 'text',
                            'fieldname' => 'video_url',
                            'value' => $recipe->video_url,
                            'label' => __("Instruction video", 'zip-recipes'),
                            'comment' => __("A video is a great way to improve your ranking and will get picked up by Google's rich snippets.", 'zip-recipes'),
                        ),

                        'categoryField'=>array(
                            'type' => 'text',
                            'fieldname' => 'category',
                            'value' => $recipe->category,
                            'label' => __("Category", 'zip-recipes'),
                            'placeholder' => __('Bread','zip-recipes'),
                        ),

                        array(
                            'type' => 'text',
                            'fieldname' => 'cuisine',
                            'value' => $recipe->cuisine,
                            'label' => __("Cuisine", 'zip-recipes'),
                            'placeholder' => __('French','zip-recipes'),
                        ),

                        array(
                            'type' => 'editor',
                            'fieldname' => 'notes',
                            'value' => $recipe->notes,
                            'label' => __("Notes", 'zip-recipes'),
                            'media' => false,
                        ),

                        array(
                            'type' => 'editor',
                            'fieldname' => 'summary',
                            'value' => $recipe->summary,
                            'label' => __("Summary", 'zip-recipes'),
                            'media' => false,
                        ),

                        'author_promo' => array(
                            'type' => 'notice',
                            'fieldname' => 'author_upgrade',
                            'label' => sprintf(__('Need to set a custom author instead of a default WordPress editor? Custom authors is a feature available in %sZip Recipes Premium%s','zip-recipes'),'<a target="_blank" href="https://ziprecipes.net/prevent-author-warning-by-google-by-adding-an-author-to-your-recipe/">','</a>'),
                            'media' => false,
                        ),
                    );

                    /**
                     * Category saved in recipe is deprecated, we move to wordpress categories
                     */
                    if (strlen($recipe->category)==0){
                        $fields['categoryField']=array(
                            'type' => 'notice',
                            'fieldname' => 'categoryDeprecated',
                            'label' => sprintf(__('The recipe category has been moved to the WordPress categories. You can now assign a category to your post in the WordPress post editor','zip-recipes'),'<a target="_blank" href="https://ziprecipes.net/prevent-author-warning-by-google-by-adding-an-author-to-your-recipe/">','</a>'),
                            'media' => false,
                            'callback' => 'cmplzSelectedCategories'
                        );
                    }

                    $fields = apply_filters('zrdn_edit_fields', $fields, $recipe);
                    foreach ($fields as $field_args) {
                        $field->get_field_html($field_args);
                    }
                    ?>

                    <?php do_action('zrdn_after_fields', $recipe) ?>
                </div>

                <!-- Tab content -->
                <div id="nutrition" class="zrdn-tabcontent <?php if ($active_tab=='nutrition') echo 'active'?>">
                    <?php zrdn_notice(__("If you enter the fields below, a HTML and CSS Google friendly nutrition label will be shown below your recipe.", "zip-recipes"), 'notice', true, false, false);
                    ?>

                    <?php do_action('zrdn_nutrition_fields', $recipe) ?>

                    <?php $nutrition_fields = array(
                        'nutrition_promo' => array(
                            'type' => 'notice',
                            'fieldname' => 'nutrition_upgrade',
                            'label' => sprintf(__('Tired of looking up all nutrition data? You can generate the nutrition data automatically with %sZip Recipes Premium%s','zip-recipes'),'<a target="_blank" href="https://ziprecipes.net/automatic-nutrition-for-your-recipes/">','</a>'),
                        ),
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


                <!-- Tab content -->
                <div id="snippets" class="zrdn-tabcontent <?php if ($active_tab=='snippets') echo 'active'?>">
                    <?php zrdn_notice(__("Google prefers three images in the ratio's 1x1, 4x3 and 16x9. These are generated automatically by Zip Recipes, but you can change the selected images here. Use a high resolution image. If you reset an image, it will default to the generated image based on the main recipe image, or if there is no recipe image, the linked post image.", "zip-recipes"), 'notice', true, false, false);
                    ?>

                    <?php $snippet_fields = array(
                        array(
                            'type' => 'upload',
                            'low_resolution_notice' =>__("Image resolution too low, or image size not generated. You should use an image of at least 250x250 pixels", "zip-recipes"),
                            'fieldname' => 'json_image_1x1',
                            'size' => 'zrdn_recipe_image_json_1x1',
                            'value' => $recipe->json_image_1x1,
                            'thumbnail_id' => $recipe->json_image_1x1_id,
                            'label' => __("1x1 snippet image", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'upload',
                            'low_resolution_notice' =>__("Image resolution too low, or image size not generated. You should use an image of at least 250x250 pixels", "zip-recipes"),
                            'fieldname' => 'json_image_4x3',
                            'size' => 'zrdn_recipe_image_json_4x3',
                            'value' => $recipe->json_image_4x3,
                            'thumbnail_id' => $recipe->json_image_4x3_id,
                            'label' => __("4x3 snippet image", 'zip-recipes'),
                        ),
                        array(
                            'type' => 'upload',
                            'low_resolution_notice' =>__("Image resolution too low, or image size not generated. You should use an image of at least 250x250 pixels", "zip-recipes"),
                            'fieldname' => 'json_image_16x9',
                            'size' => 'zrdn_recipe_image_json_16x9',
                            'value' => $recipe->json_image_16x9,
                            'thumbnail_id' => $recipe->json_image_16x9_id,
                            'label' => __("16x9 snippet image", 'zip-recipes'),
                        ),
                    );

                    foreach ($snippet_fields as $field_args) {
                        $field->get_field_html($field_args);
                    }
                    ?>
                </div><!--tab content -->

                <!-- Tab content -->
                <div id="misc" class="zrdn-tabcontent <?php if ($active_tab=='misc') echo 'active'?>">

                    <?php $misc_fields = array(
                        array(
                            'type' => 'checkbox',
                            'fieldname' => 'non_food',
                            'value' => $recipe->non_food,
                            'label' => __("Mark recipe as non food", 'zip-recipes'),
                        ),
                    );

                    $misc_fields = apply_filters('zrdn_edit_misc_fields', $misc_fields, $recipe);
                    foreach ($misc_fields as $field_args) {
                        $field->get_field_html($field_args);
                    }
                    $list_style_ingredients = Util::get_option('ingredients_list_type');
                    $list_style_instructions = Util::get_option('instructions_list_type');
                    $list_type_ingredients  = \ZRDN\Util::get_list_type( $list_style_ingredients );
                    $list_type_instructions = \ZRDN\Util::get_list_type($list_style_instructions);
                    ?>
                    <input type="hidden" name="zrdn_ingredients_list_type" value = "<?php echo $list_type_ingredients?>">
                    <input type="hidden" name="zrdn_instructions_list_type" value = "<?php echo $list_type_instructions?>">

                    <?php
                    if ( $recipe->post_id && get_post_type($recipe->post_id) !== 'trash' ){
                        $post_permalink = get_permalink($recipe->post_id);
                    } else {
                        //check if we have our default private post
                        if ( !$recipe_id ) $recipe_id = Util::get_demo_recipe_id();
                        $preview_post_id = Util::get_preview_post_id( $recipe_id );
                        $post_permalink = get_permalink( $preview_post_id );
                    }?>
                    <input type="hidden" name="zrdn_post_permalink" value = "<?php echo $post_permalink?>">
                </div><!--tab content -->
            </div>

            <div class="zrdn-column preview-column">
                <div id="zrdn-preview">
                    <?php
                    /**
                     * Test if we have access to the post url, or if it is blocked by mod security or similar
                     */

                    if (!$post_permalink) {
                        echo __('The preview requires the recipe to be connected to a published post', "zip-recipes");
                    } ?>
                    <div id="zrdn-skeleton">
                        <div class="lines">
                            <div class="thumb pulse"></div>
                            <div class="line pulse"></div>
                            <div class="line pulse"></div>
                            <div class="line pulse"></div>
                            <div class="line pulse"></div>
                        </div>
                    </div>
                </div>

            </div>


        </div><!-- container -->
    </form>
</div>