<?php

namespace ZRDN;
if (!defined('ABSPATH')) exit;

add_action('admin_init', __NAMESPACE__.'\zrdn_check_upgrade');
function zrdn_check_upgrade()
{
    if (!current_user_can('manage_options')) return;
    if (!get_option('zrdn_checked_for_multiple_recipes')) {
        /**
         * Make sure each post type has only one recipe
         */
        $args = array(
            'post_type' => array('post', 'page'),
            'posts_per_page' => 20,
            'meta_query' => array(
                array(
                    'key' => 'zrdn_verified_for_multiples',
                    'compare' => 'NOT EXISTS' // this should work...
                ),
            )
        );

        global $wpdb;
        $table = $wpdb->prefix . "amd_zlrecipe_recipes";

        $posts = get_posts($args);

        //if nothing is found, disable checking.
        if (count($posts)==0) update_option('zrdn_checked_for_multiple_recipes',true);
        foreach ($posts as $post) {
            //get all recipes with this post_id
            $recipes = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table where post_id = %s ORDER BY recipe_id DESC", $post->ID));
            $index = 0;

            //if there's more than one recipe, there are multiple posts linked
            if (count($recipes) > 1) {

                foreach ($recipes as $recipe) {
                    $index++;
                    //the first one is the highest number recipe_id (order desc) so we skip this row
                    if ($index == 1) continue;

                    //unlink from post
                    $wpdb->update(
                        $table,
                        array(
                            'post_id' => 0,
                        ),
                        array('recipe_id' => $recipe->recipe_id)
                    );
                }
            }

            //mark this post as having been checked for duplicates, so we don't check it twice.
            update_post_meta($post->ID, 'zrdn_verified_for_multiples', true);
        }


    }



}