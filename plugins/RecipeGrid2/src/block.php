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
 * Enqueue Gutenberg block assets for backend editor.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\zrdn_grid_editor_assets' );
function zrdn_grid_editor_assets() {

    // Scripts.
	wp_enqueue_script(
		'zrdn-grid-block', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-api' ), // Dependencies, defined above.
        filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: File modification time.
		true // Enqueue the script in the footer.
	);

    do_action('zrdn_grid_enqueue_styles');

    wp_set_script_translations( 'zrdn-grid-block', 'zip-recipes' , trailingslashit(ZRDN_PATH) . 'languages');

	// Styles.
    wp_enqueue_style(
        'zrdn-grid-block', // Handle.
        trailingslashit(ZRDN_PLUGIN_URL) . "plugins/RecipeGrid2/dist/blocks.style.build.css", array( 'wp-edit-blocks' ), ZRDN_VERSION_NUM
    );

}


/**
 * Handles the front end rendering of the zip recipes grid block
 *
 * @param $attributes
 * @param $content
 * @return string
 */
function zrdn_render_grid_block($atts, $content)
{
    $shortcode_args[] = isset($atts['layoutMode']) ? 'layoutMode="'.sanitize_title($atts['layoutMode']).'"' : false;
    $shortcode_args[] = isset($atts['category']) ? 'category="'.sanitize_title($atts['category']).'"' : false;
    $shortcode_args[] = isset($atts['animationType']) ? 'animationtype="'.sanitize_text_field($atts['animationType']).'"' : false;
    $shortcode_args[] = isset($atts['size']) ? 'size="'.sanitize_title($atts['size']).'"' : false;
    $shortcode_args[] = isset($atts['gapHorizontal']) ? 'gaphorizontal='.intval($atts['gapHorizontal']) : false;
    $shortcode_args[] = isset($atts['gapVertical']) ? 'gapvertical='.intval($atts['gapVertical']) : false;
    $shortcode_args[] = isset($atts['showTitle']) ? 'showtitle="'.sanitize_title($atts['showTitle']).'"' : false;
    $shortcode_args[] = isset($atts['recipesPerPage']) ? 'recipesperpage='.intval($atts['recipesPerPage']) : false;
    $shortcode_args[] = isset($atts['loadMoreButton']) ? 'loadmorebutton="'.sanitize_title($atts['loadMoreButton']).'"' : false;
    $shortcode_args[] = isset($atts['search']) ? 'search="'.sanitize_title($atts['search']).'"' : false;
    $shortcode_args[] = isset($atts['backgroundColor']) ? 'backgroundcolor="'.sanitize_hex_color($atts['backgroundColor']).'"' : false;
    $shortcode_args[] = isset($atts['color']) ? 'color="'.sanitize_hex_color($atts['color']).'"' : false;
    $shortcode_args[] = isset($atts['borderColor']) ? 'bordercolor="'.sanitize_hex_color($atts['borderColor']).'"' : false;
    $shortcode_args[] = isset($atts['useAjax']) ? 'use_ajax="'.intval($atts['useAjax']).'"' : false;

    $shortcode_string = implode(' ',$shortcode_args);

    $html = do_shortcode("[zrdn-grid $shortcode_string]");

    return $html;
}

register_block_type('zip-recipes/recipe-grid-block', array(
    'render_callback' => __NAMESPACE__ . '\zrdn_render_grid_block',
));

