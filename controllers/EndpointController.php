<?php

namespace ZRDN;
/**
 * Zip Recipes API endpoint
 *
 * JSON base Restful API for Zip Recipes Operations.
 *
 * PHP version 5.3
 * Depends: WP REST API, Plugin Dependencies
 *
 * @package    Zip Recipes
 * @author     Mudassar Ali <sahil_bwp@yahoo.com>
 * @copyright  2019 Rogier Lankhorst
 * @version 1.0
 * @example  wp-json/zip-recipes/v2/recipe
 */
use WP_REST_Server;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use WP_Http;
use ZRDN\Recipe as RecipeModel;

class ZRDN_API_Endpoint_Controller extends WP_REST_Controller {

    /**
     *  constant
     */

    protected $namespace = 'zip-recipes/v1';
    protected $namespace_v2 = 'zip-recipes/v2';
    protected $rest_base = 'recipe';

    public function __construct() {

    }

    /**
     * Register our REST Server
     */
    public function boot_rest_server() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Register routs
     */

    public function register_routes() {

        register_rest_route($this->namespace_v2, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_recipe_v2'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => array($this, 'validate_numeric')
                    )
                ),
                'permission_callback' => '__return_true',
            ),
        ));

        register_rest_route($this->namespace, '/recipes', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_recipes'),
                'permission_callback' => '__return_true',
                'args' => array(),
            ),
        ));

        // end routes functions    
    }

    /**
     * Get a collection of recipes
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */

    public function get_recipes(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . "amd_zlrecipe_recipes";
        $recipes = $wpdb->get_results("SELECT * FROM $table");
        if (!$recipes || count($recipes)==0) $output = array();
        foreach ($recipes as $recipe) {
        	$preview_post_id = Util::get_preview_post_id(false);
        	$post_id = $recipe->post_id === $preview_post_id ? 0 : $recipe->post_id;
            $output[] =
                array(
                    'id' => $recipe->recipe_id,
                    'title' => $recipe->recipe_title,
                    'post_id' => $post_id,
                );
        }
        return ZRDN_REST_Response::success($output);
    }

    public function get_recipe_v2(WP_REST_Request $request) {
        $recipe_id = (int) $request['id'];
        $recipe = new Recipe($recipe_id);
        if (empty($recipe_id) || !isset($recipe->recipe_id)) {
            return ZRDN_REST_Response::error(__('Invalid recipe ID or recipe not found', 'zip-recipes'));
        }
        $html =  ZipRecipes::zrdn_format_recipe($recipe);
        $output['content'] =$html;

        return ZRDN_REST_Response::success($output);
    }


    /**
     * Checks that value is numeric.
     *
     * @param $param
     *
     * @return bool
     */
    public function validate_numeric($param) {
        return is_numeric($param);
    }


}

$server = new ZRDN_API_Endpoint_Controller();
$server->boot_rest_server();
