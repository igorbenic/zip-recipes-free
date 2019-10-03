<?php
/**
 * Created by PhpStorm.
 * User: gezimhome
 * Date: 2018-04-03
 * Time: 13:56
 */

namespace ZRDN;

abstract class PluginBase
{
    public $suffix = '';

    public function __construct()
    {
		
		if ($this->isDisabled()) {
            return false;
        }

        $this->suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

        return true;
	}

	
	public function zrdn_plugin_enqueue_settings_css () {

		$currentPage = $_GET['page'];

		// pages which we need to load the style in
		$requiredPages = array('zrdn-imperial-metrics-converter');

		if (in_array($currentPage, $requiredPages)) {
			wp_enqueue_style('zrdn_plugin_settings_css', ZRDN_PLUGIN_URL . "/RecipeTable/css/editor.css", /* deps */ array(), ZRDN_VERSION_NUM);
		}

    }
    
    public function settingsPageWrapper ($fields) {
        $field = ZipRecipes::$field;

        echo '<div class="wpwrap">';

        foreach ($fields as $field_args) {
            $field->get_field_html($field_args);
        }

        echo '</div>';
    }

    private function isDisabled()
    {
        $disabled = false;
        $pluginOptions = get_option(ZipRecipes::PLUGIN_OPTION_NAME, array());
        if (isset($pluginOptions[get_class($this)]) && $pluginOptions[get_class($this)]["active"]) {
            $disabled = false;
        } else {
            $disabled = true;
        }

        return $disabled;
    }
}


// example how to implement settings page in base class:

        // add_action("zrdn__menu_page", array($this, 'admin_menu_setup'));

// public function settings_page_renderer() {

//     if (!current_user_can('manage_options')) return;

//     $fields = array(
//         array(
//             'type' => 'checkbox',
//             'fieldname' => 'recipe_image',
//             'value'=> true,
//             'label' => __("Recipe is converted to visitor's country units", 'zip-recipes'),
//         ),
//     );

//     $fields = apply_filters('zrdn_edit_metricimperial_fields', $fields);

//     $this->settingsPageWrapper($fields);

// }


    // public function admin_menu_setup($settings=array()) {
    //     $recipe_indexes_page_title = $recipe_indexes_menu_title = "unit conversion";
    //     add_submenu_page(
    //         $settings['parent_slug'], // parent_slug
    //         $recipe_indexes_page_title, // page_title
    //         $recipe_indexes_menu_title, // menu_title
    //         $settings['capability'], // capability
    //         $this::INDEX_PAGE_ID, // menu_slug
    //         array($this, 'settings_page_renderer') // callback function
    //     );
    // }