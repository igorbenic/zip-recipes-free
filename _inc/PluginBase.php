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
    private static $unitInfoLoaded = false;

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


    // JS_script_tag -> JS script tag which to enqueue the data to
    public function loadUnitInfo($JS_script_tag) {

        if (PluginBase::$unitInfoLoaded) {
            return;
        }

                $units = array(
                    array(
                       'src' => array(__('ounce','zip-recipes'),__('ounces','zip-recipes')),
                       'type' => 'imperial',
                       'conversionUnit' => array(__('gram','zip-recipes'),_x('grams','plural','zip-recipes')),
                       'ratio' => '28.3495231',
                       'decimal_rounding' => 1,
                   ),
                   array(
                       'src' => array('[\d ]'.__('oz','zip-recipes'),'[\d ]'.__('oz','zip-recipes')),
                       'type' => 'imperial',
                       'conversionUnit' => array(__('gram','zip-recipes'),_x('grams','plural','zip-recipes')),
                       'ratio' => '28.3495231',
                       'decimal_rounding' => 1,
                   ),
                   array(
                       'src' => array(__('pound','zip-recipes'),__('pounds', 'zip-recipes')),
                       'type' => 'imperial',
                       'conversionUnit' => array(__('gram','zip-recipes'),_x('grams','plural','zip-recipes')),
                       'ratio' => '453.59237',
                       'decimal_rounding' => 2,
                   ),
                   array(
                       'src' => array('[\d ]'.__('lb','zip-recipes'),'[\d ]'.__('lbs', 'zip-recipes')),
                       'type' => 'imperial',
                       'conversionUnit' => array(__('gram','zip-recipes'),_x('grams','plural','zip-recipes')),
                       'ratio' => '453.59237',
                       'decimal_rounding' => 2,
                   ),
                   array(
                       'src' =>array(__('gram','zip-recipes'),_x('grams','plural','zip-recipes')),
                       'type' => 'metric',
                       'conversionUnit' => array(__('ounce','zip-recipes'),__('ounces','zip-recipes')),
                       'ratio' => '0.0352739619',
                       'decimal_rounding' => 0,
                   ),
                   array(
                       'src' =>array('[\d ]'._x('g','short for gram','zip-recipes'),'[\d ]' . _x('g','short for grams, plural','zip-recipes')),
                       'type' => 'metric',
                       'conversionUnit' => array(__('ounce','zip-recipes'),__('ounces','zip-recipes')),
                       'ratio' => '0.0352739619',
                       'decimal_rounding' => 0,
                   ),
                   array(
                       'src' =>array(__('kilogram','zip-recipes'),_x('kilograms','plural','zip-recipes')),
                       'type' => 'metric',
                       'conversionUnit' => array(__('ounce','zip-recipes'),__('ounces','zip-recipes')),
                       'ratio' => '35.2739619‬',
                       'decimal_rounding' => 2,
                   ),
                   array(
                       'src' =>array('[\d ]'.__('kg','zip-recipes'),'[\d ]'._x('kgs ','plural','zip-recipes')),
                       'type' => 'metric',
                       'conversionUnit' => array(__('ounce','zip-recipes'),__('ounces','zip-recipes')),
                       'ratio' => '35.2739619‬',
                       'decimal_rounding' => 2,
                   ),
                   // array(
                   //     'src' => __('cup','zip-recipes'),
                   //     'type' => 'imperial',
                   //     'conversionUnit' => __('liter','zip-recipes'),
                   //     'ratio' => '1.5',
                   // ),

                   // array(
                   //     'src' => __('liter','zip-recipes'),
                   //     'type' => 'metric',
                   //     'conversionUnit' => __('gram','zip-recipes'),
                   //     'ratio' => '1.5',
                   // ),
           );

        wp_localize_script($JS_script_tag, 'zrdn_units', $units);
        PluginBase::$unitInfoLoaded = true;
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