<?php
namespace ZRDN;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 * production: npn run build
 * dev: npm start
 *
 * */

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
// Hook: Frontend assets.
//handled in documents class

//add_action( 'enqueue_block_assets', 'zrdn_block_assets' );
//function zrdn_block_assets() { // phpcs:ignore
//	// Styles.
//	wp_enqueue_style(
//		'my_block-cgb-style-css', // Handle.
//		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
//		array( 'wp-editor' ) // Dependency to include the CSS after it.
//		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
//	);
//}

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\zrdn_editor_assets' );
function zrdn_editor_assets() {

    // Scripts.
	wp_enqueue_script(
		'zrdn-block', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-api' ), // Dependencies, defined above.
        filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: File modification time.
		true // Enqueue the script in the footer.
	);

    wp_localize_script(
        'zrdn-block',
        'zrdn',
        array(
            'site_url' => site_url(),
            'is_gutenberg' => true,
            'zrdn_recipe_preview' => trailingslashit(ZRDN_PLUGIN_URL).  'images/recipe-preview.png',
            'zrdn_grid_preview' => trailingslashit(ZRDN_PLUGIN_URL).  'images/grid-preview.png',

        )
    );
	do_action('zrdn_enqueue_scripts');
    wp_set_script_translations( 'zrdn-block', 'zip-recipes' , trailingslashit(ZRDN_PATH) . 'languages');

	// Styles.
    wp_enqueue_style(
        'zrdn-block', // Handle.
        trailingslashit(ZRDN_PLUGIN_URL) . "dist/blocks.style.build.css", array( 'wp-edit-blocks' ), ZRDN_VERSION_NUM
    );

}


/**
 * Handles the front end rendering of the zip recipees block
 *
 * @param $attributes
 * @param $content
 * @return string
 */
function zrdn_render_document_block($attributes, $content)
{
    $html = __('Recipe not found', 'zip-recipes');
    if (isset($attributes['id'])) {
        $recipe = new Recipe(intval($attributes['id']));
        $html =  ZipRecipes::zrdn_format_recipe($recipe);

    }

    return $html;
}

register_block_type('zip-recipes/recipe-block', array(
    'render_callback' => __NAMESPACE__ . '\zrdn_render_document_block',
));

