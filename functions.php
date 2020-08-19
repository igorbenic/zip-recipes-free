<?php
namespace ZRDN;
defined('ABSPATH') or die("Error! Cannot be called directly.");

/**
 * Load the translation files
 *
 *
 */

if (!function_exists(__NAMESPACE__ . '\zrdn_load_translation')) {
	add_action('init',  __NAMESPACE__ . '\zrdn_load_translation', 20);
	function zrdn_load_translation()
	{
		load_plugin_textdomain('zip-recipes', FALSE,  ZRDN_PATH.'/languages/');
	}
}

/**
 * Load iframe has to hook into admin init, otherwise languages are not loaded yet.
 *
 * */
if (!function_exists(__NAMESPACE__ . '\zrdn_maybe_load_iframe')) {
	function zrdn_maybe_load_iframe()
	{
		// Setup query catch for recipe insertion popup.
		if (strpos($_SERVER['REQUEST_URI'], 'media-upload.php') && strpos($_SERVER['REQUEST_URI'], '&type=z_recipe') && !strpos($_SERVER['REQUEST_URI'], '&wrt=')) {
			// pluggable.php is needed for current_user_can
			require_once(ABSPATH . 'wp-includes/pluggable.php');

			// user is logged in and can edit posts or pages
			if (\current_user_can('edit_posts') || \current_user_can('edit_pages')) {
				$get_info = $_REQUEST;
				$post_id = isset($get_info["post_id"]) ? intval($get_info["post_id"]) : 0;

				if (isset($get_info["recipe_post_id"]) &&
				    !isset($get_info["add-recipe-button"]) &&
				    strpos($get_info["recipe_post_id"], '-') !== false
				) { // EDIT recipe
					$recipe_id = preg_replace('/[0-9]*?\-/i', '', $get_info["recipe_post_id"]);
					wp_redirect(add_query_arg(array("page"=>"zrdn-recipes","action"=>"new","id"=>$recipe_id, "post_id" => $post_id,"popup"=>true),admin_url("admin.php")));

				} else { // New recipe
					wp_redirect(add_query_arg(array("page"=>"zrdn-recipes","action"=>"new", "post_id" => $post_id,"popup"=>true),admin_url("admin.php")));
				}
			}
			exit;
		}
	}
	add_action('admin_init', __NAMESPACE__ . '\zrdn_maybe_load_iframe', 30);
}

/**
 * Get number from string, and limit to three digits max.
 * @param string $value
 * @return string
 */

if (!function_exists('zrdn_minimal_number')) {
	function zrdn_minimal_number( $value ) {
		preg_match_all( '/(^[0-9]{1,5})\.([0-9]{1,5})(.*)/', $value, $matches );
		if ( isset( $matches[1][0] ) && isset($matches[2][0]) && isset($matches[3][0]) ) {
			$number = $matches[1][0];
			$first_part = $matches[1][0];
			$second_part = $matches[2][0];
			$text = $matches[3][0];
			if (strlen($first_part) >=2) {
				return $first_part.$text;
			} else {
				return $first_part.'.'.substr($second_part, 0, 1).$text;
			}

		}
		return $value;
	}
}