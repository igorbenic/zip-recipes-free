<?php
/**
 * Generic functions to run when premium is enabled
 */

namespace ZRDN;

/**
 * Remove other plugins block for premium users
 * @param array $blocks
 *
 * @return array
 */
function zrdn_remove_other_plugins_block($blocks) {
	$index = array_search( 'other', array_column( $blocks, 'source' ) );
	unset($blocks[$index]);
	return $blocks;
}
add_filter('zrdn_grid_items', __NAMESPACE__ . '\zrdn_remove_other_plugins_block', 10, 1);

/**
 * Cleanup upsells
 * @param array $fields
 *
 * @return array
 */

function zrdn_remove_upsell($fields) {
	unset($fields['author_promo']);
	unset($fields['nutrition_promo']);
	return $fields;
}
add_filter('zrdn_edit_nutrition_fields', __NAMESPACE__ . '\zrdn_remove_upsell', 10, 1);
