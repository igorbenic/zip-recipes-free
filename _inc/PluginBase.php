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
        /**
         * Imperial values are not translated, as they will be only used in english
         *
         */

                $units = array(
                    array(
                       'src' => array('[\d ]'.'ounce','[\d ]'.'ounces'),
                       'type' => 'imperial',
                       'conversionUnit' => array(__('gram','zip-recipes'),_x('grams','plural','zip-recipes')),
                       'ratio' => '28.3495231',
                       'decimal_rounding' => 1,
                   ),
//                    array(
//                        'src' => array('cup','cups'),
//                        'type' => 'imperial',
//                        'conversionUnit' => array(__('ml','zip-recipes'),_x('ml','plural','zip-recipes')),
//                        'ratio' => '250',
//                        'decimal_rounding' => 1,
//                    ),
                   array(
                       'src' => array('[\d ]'.'oz','[\d ]'.'oz'),
                       'type' => 'imperial',
                       'conversionUnit' => array(__('gram','zip-recipes'),_x('grams','plural','zip-recipes')),
                       'ratio' => '28.3495231',
                       'decimal_rounding' => 1,
                   ),
                   array(
                       'src' => array('[\d ]'.'pound','[\d ]'.'pounds'),
                       'type' => 'imperial',
                       'conversionUnit' => array(__('gram','zip-recipes'),_x('grams','plural','zip-recipes')),
                       'ratio' => '453.59237',
                       'decimal_rounding' => 2,
                   ),
                   array(
                       'src' => array('[\d ]'.'lb','[\d ]'.'lbs'),
                       'type' => 'imperial',
                       'conversionUnit' => array(__('gram','zip-recipes'),_x('grams','plural','zip-recipes')),
                       'ratio' => '453.59237',
                       'decimal_rounding' => 2,
                   ),
                    //untranslated
                    array(
                        'src' =>array('[\d ]'.'gram','[\d ]'.'grams'),
                        'type' => 'metric',
                        'conversionUnit' => array('ounce','ounces'),
                        'ratio' => '0.0352739619',
                        'decimal_rounding' => 1,
                    ),
                   array(
                       'src' =>array('[\d ]'.__('gram','zip-recipes'),'[\d ]'._x('grams','plural','zip-recipes')),
                       'type' => 'metric',
                       'conversionUnit' => array('ounce','ounces'),
                       'ratio' => '0.0352739619',
                       'decimal_rounding' => 1,
                   ),

                   array(
                       'src' =>array('[\d ]'._x('g','short for gram','zip-recipes'),'[\d ]' . _x('g','short for grams, plural','zip-recipes')),
                       'type' => 'metric',
                       'conversionUnit' => array('ounce','ounces'),
                       'ratio' => '0.0352739619',
                       'decimal_rounding' => 1,
                   ),
                    //untranslated
                    array(
                        'src' =>array('[\d ]'.'g','[\d ]' . 'g'),
                        'type' => 'metric',
                        'conversionUnit' => array('ounce','ounces'),
                        'ratio' => '0.0352739619',
                        'decimal_rounding' => 1,
                    ),
                    //untranslated
                    array(
                        'src' =>array('[\d ]'.'kilogram','[\d ]'.'kilograms'),
                        'type' => 'metric',
                        'conversionUnit' => array('ounce','ounces'),
                        'ratio' => '35.2739619‬',
                        'decimal_rounding' => 2,
                    ),
                   array(
                       'src' =>array('[\d ]'.__('kilogram','zip-recipes'),'[\d ]'._x('kilograms','plural','zip-recipes')),
                       'type' => 'metric',
                       'conversionUnit' => array('ounce','ounces'),
                       'ratio' => '35.2739619‬',
                       'decimal_rounding' => 2,
                   ),

                   array(
                       'src' =>array('[\d ]'.__('kg','zip-recipes'),'[\d ]'._x('kgs ','plural','zip-recipes')),
                       'type' => 'metric',
                       'conversionUnit' => array('ounce','ounces'),
                       'ratio' => '35.2739619‬',
                       'decimal_rounding' => 2,
                   ),
                    //untranslated
                    array(
                        'src' =>array('[\d ]'.'kilo','[\d ]'.'kilos '),
                        'type' => 'metric',
                        'conversionUnit' => array('ounce','ounces'),
                        'ratio' => '35.2739619‬',
                        'decimal_rounding' => 2,
                    ),
                    array(
                        'src' =>array('[\d ]'.__('kilo','zip-recipes'),'[\d ]'._x('kilos ','plural','zip-recipes')),
                        'type' => 'metric',
                        'conversionUnit' => array('ounce','ounces'),
                        'ratio' => '35.2739619‬',
                        'decimal_rounding' => 2,
                    ),

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