<?php
/*
Plugin Name: Zip Recipes
Text Domain: zip-recipes
Domain Path: /languages
Plugin URI: http://www.ziprecipes.net/
Plugin GitHub: https://github.com/hgezim/zip-recipes-plugin
Description: A plugin that adds all the necessary microdata to your recipes, so they will show up in Google's Recipe Search
Version: 7.0.0
Author: RogierLankhorst, markwolters
Author URI: http://www.really-simple-plugins.com/
License: GPLv3 or later

Copyright 2019 Rogier Lankhorst
This code is derived from the 2.6 version build of ZipList Recipe Plugin released by ZipList Inc.:
http://get.ziplist.com/partner-with-ziplist/wordpress-recipe-plugin/ and licensed under GPLv3 or later

*/

/*
    This file is part of Zip Recipes Plugin.

    Zip Recipes Plugin is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Zip Recipes Plugin is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Zip Recipes Plugin. If not, see <http://www.gnu.org/licenses/>.
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
		if (version_compare(PHP_VERSION, '5.6', '<')) {
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(__('Zip Recipes cannot be activated. The plugin requires PHP 5.6 or higher', 'zip-recipes'));
		}

		global $wp_version;
		if (version_compare($wp_version, '4.8', '<')) {
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(__('Zip Recipes cannot be activated. The plugin requires WordPress 4.6 or higher', 'zip-recipes'));
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

	require_once(ABSPATH . 'wp-admin/includes/plugin.php');
	$plugin_data = get_plugin_data(__FILE__);
	define('ZRDN_PLUGIN_PRODUCT_NAME', $plugin_data['Name'] );
	$debug = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? time() : '';
	define('ZRDN_VERSION_NUM', $plugin_data['Version'] . $debug);

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
		}

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

require_once(ZRDN_PATH . 'functions.php');

if (!function_exists(__NAMESPACE__ . '\zrdn_run_first_install_init')) {
	register_activation_hook( __FILE__,
		__NAMESPACE__ . '\zrdn_run_first_install_init' );

	/**
	 * Install a demo recipe on activation
	 */

	function zrdn_run_first_install_init() {

		if (!get_option('zrdn_activated_once')) {
			if (!class_exists(__NAMESPACE__ . '\Util')){
				require_once( ZRDN_PATH . '_inc/class.ziprecipes.util.php' );
			}

			//demo recipe
			$args = array(
				'searchFields' => 'recipe_title',
				'search'       => __( 'Demo Recipe', 'zip-recipes' ),
			);

			$recipes = Util::get_recipes( $args );
			if ( count( $recipes ) == 0 ) {
				$recipe = new Recipe();
				$recipe->load_default_data();
				$recipe->recipe_title    = __( 'Demo Recipe', 'zip-recipes' );
				$recipe->recipe_image_id = ZipRecipes::insert_media( ZRDN_PATH
				                                                     . 'images',
					'demo-recipe.jpg' );
				$recipe->save();
				update_option( 'zrdn_demo_recipe_id', $recipe->recipe_id );
			}

			//set some defaults
			$settings = get_option('zrdn_settings_general');
			$zrdn_print['show_summary_on_archive_pages'] = true;
			update_option('zrdn_settings_general', $settings);

			update_option('zrdn_activated_once', true);
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
