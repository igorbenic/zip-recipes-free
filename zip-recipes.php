<?php
/*
Plugin Name: Zip Recipes
Text Domain: zip-recipes
Domain Path: /languages
Plugin URI: http://www.ziprecipes.net/
Plugin GitHub: https://github.com/Really-Simple-Plugins/zip-recipes-free
Description: A plugin that adds all the necessary microdata to your recipes, so they will show up in Google's Recipe Search
Version: 8.1.1
Author: Really Simple Plugins
Author URI: https://www.really-simple-plugins.com/
License: GPL2
Copyright 2022 Really Simple Plugins
This code is derived from the 2.6 version build of ZipList Recipe Plugin released by ZipList Inc.:
http://get.ziplist.com/partner-with-ziplist/wordpress-recipe-plugin/ and licensed under GPL2 or later
*/

/*  Copyright 2022  Zip Recipes BV  (email : support@ziprecipes.net)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace ZRDN;
defined('ABSPATH') or die("Error! Cannot be called directly.");

if (!function_exists(__NAMESPACE__ . '\zrdn_activation_check')) {
	/**
	 * Checks if the plugin can safely be activated, at least php 5.6 and wp 4.6
	 * @since 7.0.0
	 */
	function zrdn_activation_check()
	{
		if (version_compare(PHP_VERSION, '7.2', '<')) {
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(__('Zip Recipes cannot be activated. The plugin requires PHP 7.2 or higher', 'zip-recipes'));
		}

		global $wp_version;
		if (version_compare($wp_version, '4.9', '<')) {
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(__('Zip Recipes cannot be activated. The plugin requires WordPress 4.9 or higher', 'zip-recipes'));
		}
	}
	register_activation_hook( __FILE__, __NAMESPACE__ . '\zrdn_activation_check' );
}

if (defined('ZRDN_PLUGIN_BASENAME')) {
	if (defined('ZRDN_FREE')) {
		deactivate_plugins('zip-recipes/zip-recipes.php');
		//add_action('admin_notices', __NAMESPACE__ . '\zrdn_notice_free_active');
	}elseif (defined('ZRDN_PRODUCT_ID') && ZRDN_PRODUCT_ID === 1851 ) {
		deactivate_plugins('zip-recipes-friend/zip-recipes-friend.php');
		//add_action('admin_notices', __NAMESPACE__ . '\zrdn_notice_free_active');
	} else {
		deactivate_plugins('zip-recipes-lover/zip-recipes-lover.php');
	}
} else {
	define('ZRDN_PATH', plugin_dir_path( __FILE__ ) );
	define('ZRDN_FREE', true);
	define('ZRDN_PLUGIN_DIRECTORY_URL', plugin_dir_url( __FILE__ ));
	define('ZRDN_PLUGIN_BASENAME', plugin_basename(__FILE__));
	define('ZRDN_PLUGIN_URL', sprintf('%s/%s/', plugins_url(), dirname(plugin_basename(__FILE__))));
	define('ZRDN_API_URL', "https://api.ziprecipes.net");
	define('ZRDN_RECIPEDATABASE_URL', 'https://share.ziprecipes.net/');
	define('ZRDN_PLUGIN_PRODUCT_NAME', 'Zip Recipes' );
	$debug = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? time() : '';
	define('ZRDN_VERSION_NUM', '8.1.1' . $debug);

	add_action('plugins_loaded', __NAMESPACE__ . '\init', 9);
}

if (!function_exists(__NAMESPACE__ . '\init')) {
	function init( $className ) {
		require_once( ZRDN_PATH . '/models/Recipe.php' );
		require_once( ZRDN_PATH . '_inc/class.ziprecipes.util.php' );
		require_once( ZRDN_PATH . 'class.ziprecipes.php' );
		require_once( ZRDN_PATH . 'class-review.php' );
		require_once( ZRDN_PATH . '_inc/helper_functions.php' );
		require_once( ZRDN_PATH . '_inc/PluginBase.php' );
		require_once( ZRDN_PATH . 'RecipeTable/RecipeMenu.php' );
		require_once( ZRDN_PATH . 'NutritionLabel/NutritionLabel.php' );

		if ( is_admin() ) {
			require_once( ZRDN_PATH . 'upgrade-zip.php' );
			require_once( ZRDN_PATH . 'grid/grid-enqueue.php' );
			require_once( ZRDN_PATH . 'class-field.php' );
			require_once( ZRDN_PATH . 'shepherd/tour.php' );

		}
		/**
		 * Recipe sharing
		 */
		require_once( ZRDN_PATH . 'sharing/class-recipe-sharing-admin.php' );

		/**
		 * API endpoint & Basic Auth
		 */
		require_once( ZRDN_PATH . "controllers/Response.php" );
		require_once( ZRDN_PATH . "controllers/EndpointController.php" );
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		ZipRecipes::init();
	}
}

if (!function_exists(__NAMESPACE__ . '\zrdn_set_defaults')) {
	register_activation_hook( __FILE__, __NAMESPACE__ . '\zrdn_set_defaults' );

	/**
	 * set defaults on activation
	 */

	function zrdn_set_defaults() {
		if (!get_option('zrdn_defaults_set')) {
			//set some defaults
			$settings = get_option('zrdn_settings_general');
			$zrdn_print['show_summary_on_archive_pages'] = true;
			update_option('zrdn_settings_general', $settings);

			update_option('zrdn_defaults_set', true);
		}
	}
}

if (!function_exists(__NAMESPACE__ . '\zrdn_check_translations')) {
	register_activation_hook(__FILE__, __NAMESPACE__ . '\zrdn_check_translations');
	function zrdn_check_translations(){
		//dirname with levels does not exist before php 7
		if (version_compare(PHP_VERSION, '7.0', '<')) return;

		$path = dirname(ZRDN_PATH, 2)."/languages/plugins/";
		if (!file_exists($path)) return;

		$extensions = array("po", "mo");
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$file = $path . '/' . $file;
					$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
					if (is_file($file) && in_array($ext, $extensions) && strpos($file, 'zip-recipes')!==FALSE && strpos($file, 'backup')===FALSE) {
						//copy to new file
						$new_name = str_replace('zip-recipes','zip-recipes-backup',$file);

						rename($file, $new_name);
					}
				}
			}
			closedir($handle);
		}
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

if ( ! function_exists( __NAMESPACE__ . '\zrdn_start_tour' ) ) {
	/**
	 * Start the tour of the plugin on activation
	 */
	function zrdn_start_tour() {
		if ( ! get_site_option( 'zrdn_tour_shown_once' ) ) {
			update_site_option( 'zrdn_tour_started', true );
		}
	}

	register_activation_hook( __FILE__, __NAMESPACE__ . '\zrdn_start_tour' );
}
