<?php

namespace ZRDN;
require_once( 'class-shortcode.php' );
require_once( 'widget.php' );
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output the label markup
 *
 * @param Recipe $recipe
 * @param array $settings
 * @param bool $is_shortcode
 *
 * @return string
 */

function zrdn_label_markup($recipe, $settings, $is_shortcode = false ) {
	$amp_on = false;
	$html = "";
	if ( function_exists( 'is_amp_endpoint' ) ) {
		$amp_on = is_amp_endpoint();
	}

	$description = $recipe->preview
		? ''
		: sprintf( __( 'Nutrition label for %s', "zip-recipes" ),
			$recipe->recipe_title );

	$settings['description'] = $description;
	$settings['site_name'] = get_bloginfo( 'name' );
	$settings['amp_on']    = $amp_on;
	$nutrition_label_type = isset($settings['nutrition_label_type']) ? $settings['nutrition_label_type'] : 'html';
	if ( $recipe->has_nutrition_data ) {
		if ( $nutrition_label_type != 'html' && $recipe->nutrition_label ){
			$nutrition_label = Util::render_template( 'nutrition-label-image.php', $recipe, $settings );
		} else {
			$nutrition_label = Util::render_template( 'nutrition-label-html.php', $recipe, $settings );
		}

		$args['nutritionlabel'] = $nutrition_label;
		$args['hide_print'] = isset($settings['hide_print_nutrition_label']) ? $settings['hide_print_nutrition_label'] : false;
		$html = Util::render_template( 'nutrition-label.php', $recipe, $args );
	}
	return $html;

}
add_filter( 'zrdn__nutrition_get_label', __NAMESPACE__ . '\zrdn_label_markup', 10, 2 );

/**
 * Render nutrition label in recipe block
 * @param Recipe $recipe
 * @param array $settings
 */

function zrdn_nutrition_label( $recipe, $settings ){
	echo zrdn_label_markup( $recipe, $settings, false);
}
add_action("zrdn_recipe_block_nutrition_label", __NAMESPACE__ . '\zrdn_nutrition_label', 10, 2);

