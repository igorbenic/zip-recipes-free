<?php
namespace ZRDN;
/**
 * Zip Recipes API endpoint

 * @example  wp-json/zip-recipes/v1/grid
 */

use WP_REST_Server;
use WP_REST_Controller;
use WP_REST_Request;

class ZRDN_GRID_Endpoint_Controller extends WP_REST_Controller {

    /**
     *  constant
     */

    protected $namespace = 'zip-recipes/v1';

    public function __construct() {
    }

    /**
     * Register our REST Server
     */
    public function boot_rest_server() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Register routes
     */
    public function register_routes() {


        register_rest_route($this->namespace, '/grid', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_grid'),
                'permission_callback' => '__return_true',
            ),
        ));

        register_rest_route($this->namespace, '/categories', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_categories'),
                'permission_callback' => '__return_true',
            ),
        ));
        // end routes functions
    }


    /**
     * This returns the recipe grid for Gutenberg. It is different from the front end function, because we want to enable some
     * features, like search, dynamically in Gutenberg. We have to force enable search therefore.
     * @param WP_REST_Request $request
     * @return \WP_REST_Response
     */

    public function get_grid(WP_REST_Request $request) {
	    $category = sanitize_title($request->get_param('category'));
        $layoutMode = sanitize_title($request->get_param('layoutMode'));
        $recipesPerPage = sanitize_title($request->get_param('recipesPerPage'));
        $html = do_shortcode('[zrdn-grid category="'.$category.'" search="1" showTitle="1" loadMoreButton="1" search="1" layoutMode="'.$layoutMode.'" recipesPerPage="'.$recipesPerPage.'"]');
        $output['content'] = $html;
        return ZRDN_REST_Response::success($output);
    }

    /**
     * @param WP_REST_Request $request
     * @return \WP_REST_Response
     */

    public function get_categories(WP_REST_Request $request){
	    $output = array();
        $categories = RecipeGrid2::get_categories();
        foreach ($categories as $key => $category) {
            $output[] =
                array(
                    'id' => $key,
                    'name' => $category['name'],
                );
        }
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
$grid_server = new ZRDN_GRID_Endpoint_Controller();
$grid_server->boot_rest_server();
