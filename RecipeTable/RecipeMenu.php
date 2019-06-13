<?php
namespace ZRDN;
require_once(dirname(__FILE__).'/metabox.php');
/**
 * If a post is saved, we will link this recipe to this post id.
 *
 * At the same time we wil unlink this recipe from any other post.
 */

add_action('edit_post', __NAMESPACE__ . '\zrdn_save_post', 10, 2);
add_action('save_post', __NAMESPACE__ . '\zrdn_save_post', 10, 2);
function zrdn_save_post($post_id, $post_data){
    if (Util::has_shortcode($post_id, $post_data)){
        $pattern = Util::get_shortcode_pattern();

        if (preg_match($pattern, $post_data->post_content, $matches)) {
            $recipe_id = intval($matches[1]);
            //check if this post is already linked to another recipe. If so, unlink it.
            //then link to current post.
            ZipRecipes::link_recipe_to_post($post_id, $recipe_id);
        }
    }
}

/**
 * If a post is deleted, we should update the recipe table as well to make sure no recipes are linked anymore to this post
 */

add_action('delete_post', __NAMESPACE__ . '\zrdn_unlink_post_from_recipe', 10, 1);
function zrdn_unlink_post_from_recipe($post_id){
    global $wpdb;
    $table = $wpdb->prefix . 'amd_zlrecipe_recipes';
    $sql = $wpdb->prepare("UPDATE ".$table." SET post_id = 0 WHERE post_id = %s", $post_id);
    return $wpdb->query($sql);
}


add_action('wp_ajax_zrdn_delete_recipe', __NAMESPACE__ . '\zrdn_delete_recipe');
function zrdn_delete_recipe(){
    $error = false;

    if (!current_user_can('edit_posts')) {
        $error = true;
    }

    if (!wp_verify_nonce($_POST['nonce'],'zrdn_delete_recipe')) {
        $error = true;
    }

    if (!$error && isset($_POST['recipe_id'])) {
        $recipe = new Recipe(intval($_POST['recipe_id']));
        $success = $recipe->delete();
        if (!$success) $error = true;
    }

    $response = json_encode(array(
        'success' => !$error,
    ));
    header("Content-Type: application/json");
    echo $response;
    exit;
}

add_action('wp_ajax_zrdn_unlink_recipe', __NAMESPACE__ . '\zrdn_unlink_recipe');
function zrdn_unlink_recipe(){
    $error = false;

    if (!current_user_can('edit_posts')) {
        $error = true;
    }

    if (!wp_verify_nonce($_POST['nonce'],'zrdn_delete_recipe')) {
        $error = true;
    }

    if (!$error && isset($_POST['recipe_id'])) {
        //remove recipe shortcode from post with regex
        $recipe_id = intval($_POST['recipe_id']);
        zrdn_unlink_recipe_from_post($recipe_id);
    }

    $response = json_encode(array(
        'success' => !$error,
    ));
    header("Content-Type: application/json");
    echo $response;
    exit;

}

add_action('admin_menu',  __NAMESPACE__ . '\zrdn_recipe_admin_menu');
function zrdn_recipe_admin_menu()
{
    if (!current_user_can('manage_options')) return;
    add_menu_page(
        __('Recipes', 'zip-recipes'),
        __('Recipes', 'zip-recipes'),
        'manage_options',
        'zrdn-recipes',
        __NAMESPACE__ . '\zrdn_recipe_overview',
        ZRDN_PLUGIN_URL . 'images/recipe-icon.png',
        apply_filters('zrdn_menu_position', 50)
    );
}

add_action('admin_enqueue_scripts', __NAMESPACE__ . '\zrdn_enqueue_style');
function zrdn_enqueue_style($hook){
    if (strpos($hook, 'zrdn') === FALSE) return;


    if (!isset($_GET['id']) && !(isset($_GET['action']) && $_GET['action']=='new')) return;

    wp_enqueue_script("zrdn-editor", ZRDN_PLUGIN_URL."RecipeTable/js/editor.js",  array('jquery'), ZRDN_VERSION_NUM);
    wp_enqueue_script("zrdn-conditions", ZRDN_PLUGIN_URL."RecipeTable/js/conditions.js",  array('jquery'), ZRDN_VERSION_NUM);
    $args = array(
        'str_click_to_edit_image' => __("Click to edit this image","zip-recipes"),
        'str_minutes' => __("minutes","zip-recipes"),
        'str_hours' => __("hours","zip-recipes"),
    );
    wp_localize_script('zrdn-editor', 'zrdn_editor', $args);


    //wp_enqueue_style("bootstrap-3", ZRDN_PLUGIN_URL . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css');
    wp_register_style('zrdn-editor', ZRDN_PLUGIN_URL."RecipeTable/css/editor.css", array(), ZRDN_VERSION_NUM, 'all');
    wp_enqueue_style('zrdn-editor');
    wp_enqueue_media();
}

function zrdn_recipe_overview(){
    //if (!current_user_can('edit_posts')) return;

    $id = false;
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
    }

    if ($id || (isset($_GET['action']) && $_GET['action']=='new'))  {
        include(dirname(__FILE__)."/edit.php");
    } else {
        include(dirname(__FILE__) . '/class-recipe-table.php');

        $recipes_table = new Recipe_Table();
        $recipes_table->prepare_items();

        ?>
        <script>
            jQuery(document).ready(function ($) {
                $(document).on('click', '.zrdn-recipe-action', function (e) {

                    e.preventDefault();
                    var btn = $(this);
                    var recipe_id = btn.data('id');
                    var action = btn.data('action');
                    if (action==='delete'){
                        btn.closest('tr').css('background-color', 'red');
                    }
                    console.log(recipe_id);


                    $.ajax({
                        type: "POST",
                        url: '<?php echo admin_url('admin-ajax.php')?>',
                        dataType: 'json',
                        data: ({
                            action: 'zrdn_'+action+'_recipe',
                            recipe_id: recipe_id,
                            nonce:'<?php echo wp_create_nonce('zrdn_delete_recipe')?>',
                        }),
                        success: function (response) {
                            if (response.success) {
                                if (action==='unlink'){
                                    btn.closest('tr').find('.delete a').show();
                                    btn.closest('tr').find('.unlink a').hide();
                                } else {
                                    btn.closest('tr').remove();
                                }
                            }
                        }

                });

                });
            });
        </script>

        <div class="wrap zrdn-recipes">
            <h1><?php _e("Recipes", 'zip-recipes') ?>
                <a href="<?php echo admin_url('admin.php?page=zrdn-recipes&action=new'); ?>"
                   class="page-title-action"><?php _e('Add recipe', 'zip-recipes') ?></a>
                <?php do_action('zrdn_after_recipes_overview_title'); ?>
            </h1>


            <form id="zrdn-recipe-filter" method="get"
                  action="">

                <?php
                $recipes_table->search_box(__('Filter', 'zip-recipes'), 'zrdn-recipe');
                $recipes_table->display();
                ?>
                <input type="hidden" name="page" value="zrdn-recipe"/>
            </form>
        </div>
        <?php
    }
}

