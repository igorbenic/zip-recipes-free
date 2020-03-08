<?php

namespace ZRDN;
if (!defined('ABSPATH')) exit;

add_action('admin_init', __NAMESPACE__.'\zrdn_check_upgrade');
function zrdn_check_upgrade()
{
    if (!current_user_can('manage_options')) return;

    //when debug is enabled, a timestamp is appended. We strip this for version comparison purposes.
    $prev_version = substr(get_option('zrdn-current-version', '1.0.0'),0, 6);

    /**
     * for previous versions, we want to maintain current settings, the custom author setting.
     */
    if (version_compare($prev_version, '6.0.4', '<')) {
        $authors_list   = get_option( 'zrdn_authors_list', array() );
        if (is_array($authors_list) && count($authors_list)>=1){
	        $zrdn_author = get_option('zrdn_settings_authors', array());
	        $zrdn_author['use_custom_authors'] = true;
	        update_option('zrdn_settings_authors', $zrdn_author);        }
    }

    do_action('zrdn_upgrade_check', $prev_version);

	/**
	 * upgrade old options to new style
	 */

	if ( version_compare($prev_version, '6.3.9', '<') ) {
		error_log("run settings upgrade");

		//this is upgrade, so we don't need to set default add ons
		update_option('zrdn_default_addons_enabled', true);
		$zrdn_general = get_option('zrdn_settings_general', array());
		$zrdn_labels = get_option('zrdn_settings_labels', array());
		$zrdn_print = get_option('zrdn_settings_print', array());
		$zrdn_nutrition = get_option('zrdn_settings_nutrition', array());
		$zrdn_author = get_option('zrdn_settings_authors', array());
		$zrdn_social = get_option('zrdn_settings_social', array());

		/**
		 * upgrade add on settings
		 */
		$plugins = get_option('zrdn__plugins', array());
		$zrdn_plugins = get_option('zrdn_settings_plugins');
		foreach ($plugins as $name => $plugin){
			if ($plugin['active']==1){
				$plugin_name = str_replace("ZRDN\\", '',$name);
				if ($plugin_name === 'Authors'){
					$zrdn_author[$plugin_name] = 1;
				} else if($plugin_name === 'AutomaticNutrition') {
					$zrdn_nutrition[$plugin_name] = 1;
				} else if($plugin_name === 'RecipeActions') {
					$zrdn_social[$plugin_name] = 1;
				} else {
					$zrdn_plugins[$plugin_name] = 1;
				}
			}
		}
		update_option('zrdn_settings_plugins', $zrdn_plugins);

		/**
		 * social
		 */
		$zrdn_social['recipe_action_yummly'] = get_option('zrdn_recipe_action_yummly');
		$zrdn_social['recipe_action_bigoven'] = get_option('zrdn_recipe_action_bigoven');
		$zrdn_social['recipe_action_pinterest'] = get_option('zrdn_recipe_action_pinterest');
		update_option('zrdn_settings_social', $zrdn_social);

		$value = get_option('zrdn_attribution_hide')=='Hide' ? true : false;
		$zrdn_general['hide_attribution'] = $value;

		$value = get_option('zlrecipe_printed_permalink_hide')=='Hide' ? true : false;
		$zrdn_general['hide_permalink'] = $value;

		$value = get_option('zlrecipe_printed_copyright_statement');
		$zrdn_general['copyright_statement'] = $value;

		$value = get_option('zlrecipe_stylesheet')=='zlrecipe-std' ? true : false;
		$zrdn_general['use_zip_css'] = $value;

		$value = get_option('recipe_title_hide')=='Hide' ? true : false;
		$zrdn_general['hide_title'] = $value;

		$value = get_option('zlrecipe_image_hide')=='Hide' ? true : false;
		$zrdn_general['hide_image'] = $value;

		$value = get_option('zlrecipe_ingredient_list_type');
		$zrdn_general['ingredients_list_type'] = $value;

		$value = get_option('zlrecipe_instruction_list_type');
		$zrdn_general['instructions_list_type'] = $value;

		$value = get_option('zlrecipe_image_width');
		$zrdn_general['image_width'] = $value;

		$value = get_option('zlrecipe_outer_border_style');
		$zrdn_general['border_style'] = $value;

		$value = get_option('zlrecipe_custom_print_image');
		$zrdn_general['print_image'] = $value;

		$value = get_option('zlrecipe_hide_on_duplicate_image')=='Hide' ? true : false;
		$zrdn_general['hide_on_duplicate_image'] = $value;



		$value = get_option('zrdn-custom-template-name');
		$zrdn_general['template'] = $value;
		update_option('zrdn_settings_general', $zrdn_general);




		/**
		 * upgrade labels
		 */

		$value = get_option('zlrecipe_prep_time_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_prep_time_label'] = $value;

		$value = get_option('zlrecipe_cook_time_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_cook_time_label'] = $value;

		$value = get_option('zlrecipe_total_time_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_total_time_label'] = $value;

		$value = get_option('zlrecipe_yield_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_yield_label'] = $value;

		$value = get_option('zlrecipe_serving_size_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_serving_size_label'] = $value;

		$value = get_option('zlrecipe_category_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_category_label'] = $value;

		$value = get_option('zlrecipe_ingredient_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_ingredients_label'] = $value;

		$value = get_option('zlrecipe_notes_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_notes_label'] = $value;

		$value = get_option('zlrecipe_instruction_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_instructions_label'] = $value;

		$value = get_option('zlrecipe_cuisine_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_cuisine_label'] = $value;
		update_option('zrdn_settings_labels', $zrdn_general);


		/**
		 * upgrade print settings
		 */

		$value = get_option('zlrecipe_image_hide_print')=='Hide' ? true : false;
		$zrdn_print['hide_print_image'] = $value;
		$value = get_option('zlrecipe_print_link_hide')=='Hide' ? true : false;
		$zrdn_print['hide_print_link'] = $value;
		$zrdn_print['hide_nutrition_label_print'] = get_option('zrdn_print_nutrition_label');
		update_option('zrdn_settings_print', $zrdn_print);

		/**
		 * upgrade nutrition settings
		 */

		//based on one hide label setting, we set a generic hide labels nutrition option
		$zrdn_nutrition['nutrition_label_type'] = get_option('zrdn_label_display_method');
		$zrdn_nutrition['hide_text_nutrition_labels'] = get_option('zlrecipe_nutrition_info_label_hide');
		$zrdn_nutrition['hide_nutrition_label'] = get_option('zrdn_hide_nutrition_label');
		$zrdn_nutrition['show_textual_nutrition_information'] = get_option('zlrecipe_nutrition_info_use_text');
		$zrdn_nutrition['hide_nutrition_label_print'] = get_option('zrdn_settings_nutrition');
		update_option('zrdn_settings_nutrition', $zrdn_nutrition);

		/**
		 *upgrade author
		 */


		$zrdn_author['use_custom_authors'] = get_option('zrdn_use_custom_authors');
		$zrdn_author['custom_authors'] = get_option( 'zrdn_authors_list', array() );
		$zrdn_author['default_author'] = get_option( 'zrdn_authors_default_author', '' );

		update_option('zrdn_settings_authors', $zrdn_author);


	/**
		 * next update: remove old settings
		 */

//		delete_option('zlrecipe_print_link_hide');
//		delete_option('zrdn_attribution_hide');
//		delete_option('zlrecipe_printed_permalink_hide');
//		delete_option('zlrecipe_printed_copyright_statement');
//		delete_option('zlrecipe_stylesheet');
//		delete_option('recipe_title_hide');
//		delete_option('zlrecipe_image_hide');
//		delete_option('zlrecipe_image_hide_print');
//		delete_option('zlrecipe_ingredient_label_hide');
//		delete_option('zlrecipe_ingredient_list_type');
//		delete_option('zlrecipe_instruction_label_hide');
//		delete_option('zlrecipe_instruction_list_type');
//		delete_option('zlrecipe_image_width');
//		delete_option('zlrecipe_outer_border_style');
//		delete_option('zlrecipe_custom_print_image');
//		delete_option('zlrecipe_hide_on_duplicate_image');
//		delete_option('zlrecipe_notes_label_hide');
//		delete_option('zlrecipe_nutrition_info_use_text');
//		delete_option('zlrecipe_prep_time_label_hide');
//		delete_option('zlrecipe_cook_time_label_hide');
//		delete_option('zlrecipe_total_time_label_hide');
//		delete_option('zlrecipe_yield_label_hide');
//		delete_option('zlrecipe_serving_size_label_hide');
//		delete_option('zlrecipe_calories_label_hide');
//		delete_option('zlrecipe_fat_label_hide');
//		delete_option('zlrecipe_carbs_label_hide');
//		delete_option('zlrecipe_protein_label_hide');
//		delete_option('zlrecipe_fiber_label_hide');
//		delete_option('zlrecipe_sugar_label_hide');
//		delete_option('zlrecipe_saturated_fat_label_hide');
//		delete_option('zlrecipe_sodium_label_hide');
//		delete_option('zlrecipe_trans_fat_label_hide');
//		delete_option('zlrecipe_cholesterol_label_hide');
//		delete_option('zlrecipe_category_label_hide');
//		delete_option('zlrecipe_cuisine_label_hide');
//		delete_option('hide_nutrition_label_print');
//		delete_option('zrdn_label_display_method');
//		delete_option('zrdn_authors_default_author');
//		delete_option('zrdn_authors_list');
//		delete_option('zrdn_use_custom_authors');


	}
    update_option('zrdn-current-version', ZRDN_VERSION_NUM);

    if (!get_option('zrdn_checked_for_multiple_recipes')) {
        /**
         * Make sure each post type has only one recipe
         */
        $args = array(
            'post_type' => array('post', 'page'),
            'posts_per_page' => 20,
            'meta_query' => array(
                array(
                    'key' => 'zrdn_verified_for_multiples',
                    'compare' => 'NOT EXISTS' // this should work...
                ),
            )
        );

        global $wpdb;
        $table = $wpdb->prefix . "amd_zlrecipe_recipes";

        $posts = get_posts($args);

        //if nothing is found, disable checking.
        if (count($posts)==0) update_option('zrdn_checked_for_multiple_recipes',true);
        foreach ($posts as $post) {
            //get all recipes with this post_id
            $recipes = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table where post_id = %s ORDER BY recipe_id DESC", $post->ID));
            $index = 0;

            //if there's more than one recipe, there are multiple posts linked
            if (count($recipes) > 1) {

                foreach ($recipes as $recipe) {
                    $index++;
                    //the first one is the highest number recipe_id (order desc) so we skip this row
                    if ($index == 1) continue;

                    //unlink from post
                    $wpdb->update(
                        $table,
                        array(
                            'post_id' => 0,
                        ),
                        array('recipe_id' => $recipe->recipe_id)
                    );
                }
            }

            //mark this post as having been checked for duplicates, so we don't check it twice.
            update_post_meta($post->ID, 'zrdn_verified_for_multiples', true);
        }


    }



}