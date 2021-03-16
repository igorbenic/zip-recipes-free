<?php

namespace ZRDN;
use Cassandra\Custom;

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
	        $zrdn_author['Authors'] = true;
	        update_option('zrdn_settings_authors', $zrdn_author);        }
    }

    do_action('zrdn_upgrade_check', $prev_version);

	/**
	 * upgrade old options to new style
	 */
	if ( $prev_version && version_compare($prev_version, '6.4.0', '<') ) {
		//this is upgrade, so we don't need to set default add ons
		update_option('zrdn_default_addons_enabled', true);
		$zrdn_general = get_option('zrdn_settings_general', array());
		$zrdn_labels = get_option('zrdn_settings_labels', array());
		$zrdn_print = get_option('zrdn_settings_print', array());
		$zrdn_nutrition = get_option('zrdn_settings_nutrition', array());
		$zrdn_author = get_option('zrdn_settings_authors', array());
		$zrdn_social = get_option('zrdn_settings_social', array());
		$zrdn_advanced = get_option('zrdn_settings_advanced', array());
		$zrdn_image = get_option('zrdn_settings_image', array());

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

		$value = get_option('zlrecipe_printed_copyright_statement');
		$zrdn_general['copyright_statement'] = $value;

		$value = get_option('recipe_title_hide')=='Hide' ? true : false;
		$zrdn_general['hide_title'] = $value;

		$value = get_option('zlrecipe_ingredient_list_type');
		$zrdn_general['ingredients_list_type'] = $value;

		$value = get_option('zlrecipe_instruction_list_type');
		$zrdn_general['instructions_list_type'] = $value;

		$value = get_option('zlrecipe_outer_border_style');
		switch($value){
			case "None":
				$value = '0px';
				break;
			case "Solid":
				$value = '1px solid';
				break;
			case "Dotted":
				$value = '1px dotted';
				break;
			case "Dashed":
				$value = '1px dashed';
				break;
			case "Thick Solid":
				$value = '2px solid';
				break;
			case "Double":
				$value = 'double';
				break;
		}

		$zrdn_general['border_style'] = $value;

		$value = get_option('zrdn-custom-template-name');
		$zrdn_general['template'] = $value;
		update_option('zrdn_settings_general', $zrdn_general);

		/**
		 * upgrade image settings
		 */

		$value = get_option('zlrecipe_image_hide')=='Hide' ? true : false;
		$zrdn_image['hide_image'] = $value;
		$value = get_option('zlrecipe_image_width');
		$zrdn_image['set_image_width'] = $value == '' ? false : true;
		$zrdn_image['image_width'] = $value;
		$value = get_option('zlrecipe_hide_on_duplicate_image')=='Hide' ? true : false;
		$zrdn_image['hide_on_duplicate_image'] = $value;
		update_option('zrdn_settings_image', $zrdn_image);

		/**
		 * upgrade labels
		 */

		$value = get_option('zlrecipe_ingredient_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_ingredients_label'] = $value;

		$value = get_option('zlrecipe_notes_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_notes_label'] = $value;

		$value = get_option('zlrecipe_instruction_label_hide')=='Hide' ? true : false;
		$zrdn_labels['hide_instructions_label'] = $value;

		update_option('zrdn_settings_labels', $zrdn_labels);


		/**
		 * upgrade print settings
		 */

		$value = get_option('zlrecipe_image_hide_print')=='Hide' ? true : false;
		$zrdn_print['hide_print_image'] = $value;
		$zrdn_print['print_image'] = get_option('zlrecipe_custom_print_image');
		$value = get_option('zlrecipe_print_link_hide')=='Hide' ? true : false;
		$zrdn_print['hide_print_link'] = $value;
		$zrdn_print['hide_print_nutrition_label'] = get_option('zrdn_print_nutrition_label');
		$value = get_option('zlrecipe_printed_permalink_hide')=='Hide' ? true : false;
		$zrdn_print['hide_permalink'] = $value;
		update_option('zrdn_settings_print', $zrdn_print);

		/**
		 * upgrade nutrition settings
		 */

		//based on one hide label setting, we set a generic hide labels nutrition option
		$zrdn_nutrition['nutrition_label_type'] = get_option('zrdn_label_display_method');
		$zrdn_nutrition['hide_nutrition_label'] = get_option('zrdn_hide_nutrition_label');
		$zrdn_nutrition['hide_print_nutrition_label'] = !get_option('zrdn_print_nutrition_label');
		update_option('zrdn_settings_nutrition', $zrdn_nutrition);

		/**
		 *upgrade author
		 */

		$zrdn_author['use_custom_authors'] = get_option('zrdn_use_custom_authors');
		$zrdn_author['custom_authors'] = get_option( 'zrdn_authors_list', array() );
		$zrdn_author['default_author'] = get_option( 'zrdn_authors_default_author', '' );
		update_option('zrdn_settings_authors', $zrdn_author);


		/**
		 * upgrade advanced settings
		 */
		$value = get_option('zlrecipe_stylesheet')=='zlrecipe-std' ? true : false;
		$zrdn_advanced['use_zip_css'] = $value;


		update_option('zrdn_settings_advanced', $zrdn_advanced);

		/**
		 * next update: remove old settings
		 */

//		delete_option('zlrecipe_print_link_hide');
		delete_option('zrdn_attribution_hide');
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
//		delete_option('zlrecipe_hide_prep_time_label');
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
//		delete_option('zrdn_print_nutrition_label');
//		delete_option('zrdn_label_display_method');
//		delete_option('zrdn_authors_default_author');
//		delete_option('zrdn_authors_list');
//		delete_option('zrdn_use_custom_authors');
	}

	if ( $prev_version && version_compare($prev_version, '6.4.11', '<') ) {
		$zrdn_labels = get_option('zrdn_settings_labels', array());
		$value_old = get_option('zlrecipe_ingredient_label_hide')=='Hide' ? true : false;
		$value_new = Util::get_option('hide_ingredient_label');
		$value = $value_old;
		if ($value_new) $value = $value_new;
		$zrdn_labels['hide_ingredients_label'] = $value;
		update_option('zrdn_settings_labels', $zrdn_labels);
	}

	if ( $prev_version && version_compare($prev_version, '7.0.0', '<') ) {
		$zrdn_settings = get_option( 'zrdn_settings_general' );

		if (isset($zrdn_settings['ingredients_list_type'])) {
			$ingredients_settings = get_option('zrdn_settings_template');

			$value = $zrdn_settings['ingredients_list_type'];
			$value = $value === 'ol' ? 'numbered' : 'nobullets';
			switch ( $value ) {
				case 'l':
				case 'p':
				case 'div':
					$value = 'nobullets';
					break;
				case 'ol':
					$value = 'numbers';
					break;
				case 'ul':
					$value = 'bullets';
					break;
				default :
					$value = 'nobullets';
			}
			$ingredients_settings['ingredients_list_type'] = $value;
			unset($zrdn_settings['ingredients_list_type']);
			update_option('zrdn_settings_ingredients', $ingredients_settings);
			update_option( 'zrdn_settings_general',$zrdn_settings );
		}

		if (isset($zrdn_settings['instructions_list_type'])) {
			$instructions_settings = get_option('zrdn_settings_template');

			$value = $zrdn_settings['instructions_list_type'];
			switch ( $value ) {
				case 'l':
				case 'p':
				case 'div':
					$value = 'nobullets';
					break;
				case 'ol':
					$value = 'numbers';
					break;
				case 'ul':
					$value = 'bullets';
					break;
				default :
					$value = 'nobullets';
			}
			$instructions_settings['instructions_list_type'] = $value;
			unset($zrdn_settings['instructions_list_type']);
			update_option('zrdn_settings_instructions', $instructions_settings);
			update_option( 'zrdn_settings_general',$zrdn_settings );
		}

		//move template settings to template array
		if (isset($zrdn_settings['template'])) {
			$template_name = $zrdn_settings['template'];
			$template_settings = get_option('zrdn_settings_template');
			$template_settings['template'] = $zrdn_settings['template'];
			update_option('zrdn_settings_template', $template_settings);

			//upgrade to template structure based on settings
			update_option( 'zrdn_settings_general', $zrdn_settings );

			ZipRecipes::set_defaults_for_template($template_name);
			$default_recipe_blocks = ZipRecipes::default_recipe_blocks($template_name);
			update_option('zrdn_recipe_blocks_layout', $default_recipe_blocks);
			update_option('zrdn_reload_template_settings', true);
		}

		if (isset($zrdn_settings['border_style'])) {
			$current_border_style = $zrdn_settings['border_style'];
			$template_settings    = get_option( 'zrdn_settings_template' );

			if ( strpos( $current_border_style, '1px' ) !== false ) {
				$template_settings['border_width'] = 1;
			} elseif ( strpos( $current_border_style, '2px' ) !== false ) {
				$template_settings['border_width'] = 2;
			} else {
				$template_settings['border_width'] = 0;
			}

			if ( strpos( $current_border_style, 'solid' ) !== false ) {
				$template_settings['border_style'] = 'solid';
			} elseif ( strpos( $current_border_style, 'dotted' ) !== false ) {
				$template_settings['border_style'] = 'dotted';

			} elseif ( strpos( $current_border_style, 'dashed' ) !== false ) {
				$template_settings['border_style'] = 'dashed';
			} elseif ( strpos( $current_border_style, 'double' ) !== false ) {
				$template_settings['border_style'] = 'double';
			} else {
				$template_settings['border_style'] = 'none';
			}

			$template_settings['border_radius'] = 0;
			update_option( 'zrdn_settings_template' , $template_settings);
			update_option( 'zrdn_settings_general',$zrdn_settings );
		}

		Util::migrate_setting('authors', 'authors', 'use_custom_authors', 'Authors');
		Util::migrate_setting('labels', 'ingredients', 'hide_ingredients_label');
		Util::migrate_setting('labels', 'instructions', 'hide_instructions_label');
		Util::migrate_setting('labels', 'notes', 'hide_notes_label');
		Util::migrate_setting('labels', 'tags', 'hide_tags_label');
		Util::migrate_setting('labels', 'social', 'hide_social_label');
		Util::migrate_setting('general', 'copyright', 'copyright_statement');
		Util::migrate_setting('image', 'recipe_image', 'hide_on_duplicate_image');
		Util::migrate_setting('print', 'actions', 'hide_print_link', 'add_print_button', true );
		Util::migrate_setting('print', 'recipe_image', 'hide_print_image');
		Util::migrate_setting('print', 'general', 'hide_permalink');
		Util::migrate_setting('print', 'nutrition_label', 'hide_print_nutrition_label');
		Util::migrate_setting('print', 'general', 'print_image');
		Util::migrate_setting('social', 'actions', 'recipe_action_yummly');
		Util::migrate_setting('social', 'actions', 'recipe_action_bigoven');
		Util::migrate_setting('social', 'actions', 'recipe_action_pinterest');
		Util::migrate_setting('social', 'plugins', 'RecipeActions');
		
		$blocks = get_option( 'zrdn_recipe_blocks_layout', array() );

		$copyright = Util::get_old_setting('copyright_statement', 'general');
		if (!$copyright || strlen($copyright) === 0 ) {
			$blocks = Util::remove_block_from_array($blocks, 'copyright');
		}

		$hide_title = Util::get_old_setting('hide_title', 'general');
		if ( $hide_title ) {
			$blocks = Util::remove_block_from_array($blocks, 'recipe_title' );
		}

		$hide_image = Util::get_old_setting('hide_image', 'image');
		if ( $hide_image ) {
			$blocks = Util::remove_block_from_array($blocks, 'recipe_image' );
		}

		$show_text_nutrition = Util::get_old_setting('show_textual_nutrition_information', 'nutrition');
		if ( !$show_text_nutrition ) {
			$blocks = Util::remove_block_from_array($blocks, 'nutrition_text' );
		}

		$add_print = Util::get_option('add_print_button');
		$add_yummly = Util::get_option('recipe_action_yummly');
		$add_bigoven = Util::get_option('recipe_action_bigoven');
		$add_pinterest = Util::get_option('recipe_action_pinterest');
		if ( !$add_print && !$add_yummly && !$add_bigoven && !$add_pinterest ) {
			$blocks = Util::remove_block_from_array($blocks, 'actions' );
		}

		//remove social, because it's a new block
		$blocks = Util::remove_block_from_array($blocks, 'social' );

		update_option( 'zrdn_recipe_blocks_layout', $blocks );

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