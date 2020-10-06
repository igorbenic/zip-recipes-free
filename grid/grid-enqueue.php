<?php
defined( 'ABSPATH' ) or die( "you do not have access to this page!" );

add_action( 'admin_enqueue_scripts', 'zrdn_enqueue_assets' );
function zrdn_enqueue_assets( $hook ) {
	if (strpos($hook, "zrdn-settings")===false ) return;

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


add_action( 'admin_enqueue_scripts', 'zrdn_enqueue_template_assets' );
function zrdn_enqueue_template_assets( $hook ) {
	if ( strpos($hook, "zrdn-template")===false) return;
	do_action('zrdn_enqueue_scripts');
	wp_register_style('zrdn-editor', ZRDN_PLUGIN_URL."RecipeTable/css/editor.css", array(), ZRDN_VERSION_NUM, 'all');
	wp_enqueue_style('zrdn-editor');

	wp_register_style('zrdn-admin-styles',
		trailingslashit(ZRDN_PLUGIN_URL) . "admin/css/style.css", "",
		ZRDN_VERSION_NUM);
	wp_enqueue_style('zrdn-admin-styles');
	wp_enqueue_script("zrdn-conditions", ZRDN_PLUGIN_URL."RecipeTable/js/conditions.js",  array('jquery'), ZRDN_VERSION_NUM);


	wp_register_style('ziprecipes-css', trailingslashit( ZRDN_PLUGIN_URL ).'styles/zlrecipe-std.css', array(), ZRDN_VERSION_NUM, 'all');
	wp_enqueue_style('ziprecipes-css');

	wp_register_style( ' zrdn-templates',
		trailingslashit( ZRDN_PLUGIN_URL ) . "grid/css/templates.css", "",
		ZRDN_VERSION_NUM );
	wp_enqueue_style( ' zrdn-templates' );

	wp_register_script( 'zrdn-muuri',
		trailingslashit( ZRDN_PLUGIN_URL )
		. 'grid/js/muuri.min.js', array( "jquery" ),
		ZRDN_VERSION_NUM );
	wp_enqueue_script( 'zrdn-muuri' );
	wp_enqueue_style( 'wp-color-picker' );
	wp_register_script( 'wp-color-picker-alpha',  trailingslashit( ZRDN_PLUGIN_URL ).'/scripts/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), '1.0.0', true );

	$color_picker_strings = array(
		'clear'            => __( 'Clear', 'textdomain' ),
		'clearAriaLabel'   => __( 'Clear color', 'textdomain' ),
		'defaultString'    => __( 'Default', 'textdomain' ),
		'defaultAriaLabel' => __( 'Select default color', 'textdomain' ),
		'pick'             => __( 'Select Color', 'textdomain' ),
		'defaultLabel'     => __( 'Color value', 'textdomain' ),
	);
	wp_localize_script( 'wp-color-picker-alpha', 'wpColorPickerL10n', $color_picker_strings );
	wp_enqueue_script( 'wp-color-picker-alpha' );
	wp_register_script( 'zrdn-templates',
		trailingslashit( ZRDN_PLUGIN_URL )
		. 'grid/js/templates.js', array( "jquery", "zrdn-muuri", 'wp-color-picker' ),
		ZRDN_VERSION_NUM );
	wp_enqueue_script( 'zrdn-templates' );
	$args = array(
		'admin_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('zrdn_edit_template'),
		'strings' => array(
			'settings_changed' => __("Settings changed, you should save!"),
		),
	);
	wp_localize_script('zrdn-templates', 'zrdn', $args);

}

function zrdn_grid_container(){
	$file = trailingslashit(ZRDN_PATH) . 'grid/templates/grid-container.php';

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
	$file = trailingslashit(ZRDN_PATH) . 'grid/templates/grid-element.php';

	if (strpos($file, '.php') !== false) {
		ob_start();
		require $file;
		$contents = ob_get_clean();
	} else {
		$contents = file_get_contents($file);
	}

	return $contents;
}


