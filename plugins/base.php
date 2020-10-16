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
