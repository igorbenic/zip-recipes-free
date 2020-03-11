<?php
namespace ZRDN;
require_once('class-shortcode.php');
require_once('widget.php');
if (!defined('ABSPATH')) exit;

/**
 * Output the label markup
 * @param $nutrition_label
 * @param $recipe
 * @param bool $is_shortcode
 * @return string
 */
function zrdn_label_markup($nutrition_label, $recipe, $is_shortcode = false)
{
    $amp_on = false;
    if (function_exists('is_amp_endpoint')) {
        $amp_on = is_amp_endpoint();
    }

    $description = $recipe->preview ? '' : sprintf(__('Nutrition label for %s', "zip-recipes"), $recipe->recipe_title);

    $data = array_merge(get_object_vars($recipe), array(
        'has_nutrition_data' => $recipe->has_nutrition_data,
        'label_url' => $recipe->nutrition_label, //when it's an image
        'description' => $description,
        'hide_print_label' => Util::get_option('hide_print_nutrition_label'),
        'show_label' => !Util::get_option('hide_nutrition_label'),
        'label_display_method' => Util::get_option('nutrition_label_type'),
    ));

    $data['site_name'] = get_bloginfo('name');
    $data['is_shortcode'] = $is_shortcode;
    $data['amp_on'] = $amp_on;

    $html = Util::view('NutritionLabel', $data);
    return $html;
}
add_filter('zrdn__nutrition_get_label', __NAMESPACE__ . '\zrdn_label_markup', 10, 2);
