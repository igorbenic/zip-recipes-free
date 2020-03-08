<?php
defined( 'ABSPATH' ) or die( "you do not have access to this page!" );

add_action( 'admin_enqueue_scripts', 'zrdn_enqueue_assets' );
function zrdn_enqueue_assets( $hook ) {
	if (strpos($hook, "zrdn-settings")===false) return;

	wp_register_style( ' zrdn-muuri',
		trailingslashit( ZRDN_PLUGIN_URL ) . "grid/css/muuri.css", "",
		ZRDN_VERSION_NUM );
	wp_enqueue_style( ' zrdn-muuri' );

	wp_register_script( ' zrdn-muuri',
		trailingslashit( ZRDN_PLUGIN_URL )
		. 'grid/js/muuri.min.js', array( "jquery" ),
		ZRDN_VERSION_NUM );
	wp_enqueue_script( ' zrdn-muuri' );

	wp_register_script( ' zrdn-grid',
		trailingslashit( ZRDN_PLUGIN_URL )
		. 'grid/js/grid.js', array( "jquery", " zrdn-muuri" ),
		ZRDN_VERSION_NUM );
	wp_enqueue_script( ' zrdn-grid' );


}

function zrdn_grid_container(){
	$file = trailingslashit(ZRDN_PLUGIN_DIRECTORY) . 'grid/templates/grid-container.php';

	if (strpos($file, '.php') !== false) {
		ob_start();
		require $file;
		$contents = ob_get_clean();
	} else {
		$contents = file_get_contents($file);
	}

	return $contents;
}

function zrdn_grid_element(){
	$file = trailingslashit(ZRDN_PLUGIN_DIRECTORY) . 'grid/templates/grid-element.php';

	if (strpos($file, '.php') !== false) {
		ob_start();
		require $file;
		$contents = ob_get_clean();
	} else {
		$contents = file_get_contents($file);
	}

	return $contents;
}


