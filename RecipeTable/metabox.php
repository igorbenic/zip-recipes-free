<?php
namespace ZRDN;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add a metabox to the classic Editor interface for interaction with the Recipes
 * We only want to show this metabox if it's either not Gutenberg, or the Classic Editor is active
 *
 */

add_action('add_meta_boxes', __NAMESPACE__.'\zrdn_add_custom_meta_box');
function zrdn_add_custom_meta_box($post_type)
{
    if (class_exists('Classic_Editor') || !function_exists('has_block')) {
        add_meta_box('zrdn_recipe_meta_box', __('Recipe', 'complianz-gdpr'), __NAMESPACE__.'\zrdn_recipe_meta_box', null, 'side', 'high');
    }

}

/**
 * Metabox for managing recipe link to post or page
 */
function zrdn_recipe_meta_box(){
    $post_id = isset($_GET['post']) ? intval($_GET['post']) : false;
    if ($post_id){

    }
    echo Util::render_template('toolbar.php');
}
