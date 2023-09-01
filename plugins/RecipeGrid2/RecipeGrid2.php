<?php
namespace ZRDN;

require_once(ZRDN_PATH . 'plugins/RecipeGrid2/api.php');

if (Util::uses_gutenberg()) {
    require_once plugin_dir_path(__FILE__) . 'src/block.php';
}

class RecipeGrid2 extends PluginBase
{
    public $dir;
    public $recipes_per_page = 20;

    const VERSION = "1.0";
    const ZRDN_RECIPE_TEMPLATE_NAME = "zrdn-custom-grid-template-name";
    const INDEX_PAGE_ID = "zrdn-recipe-grid";
    const MENU_SECTION_TITLE = 'Recipe Grid';

    function __construct() {
        $this->dir = 'plugins/'.basename(dirname(__FILE__)).'/';

        add_shortcode('zrdn-grid', array($this, 'render_grid') );

        //backward compatibility:
        add_shortcode('ziprecipes', array($this, 'render_grid'));
        add_action('zrdn_enqueue_scripts', array($this, 'enqueue_assets'));
        if ( !Util::uses_gutenberg() ) {
            add_action('admin_init', array($this, 'add_grid_editor_button'));
            add_action('admin_enqueue_scripts', array($this, 'tinymce_css'));
        }

        add_action( 'enqueue_block_assets', array($this, 'enqueue_assets_gutenberg') );
        add_action( 'wp_ajax_nopriv_zrdn_grid_load_more',  array($this, 'zrdn_grid_load_more'));
        add_action( 'wp_ajax_zrdn_grid_load_more',  array($this, 'zrdn_grid_load_more'));
        add_action( 'post_updated', array($this, 'clear_category_cache'), 10, 3 );
        add_action( 'save_post', array($this, 'clear_category_cache' ));
        add_action( 'post_updated', array($this, 'clear_json_cache'), 10, 3 );
        add_action( 'save_post', array($this, 'clear_json_cache' ));
        add_action( 'init', array($this, 'register_image_sizes' ));
        add_action( 'admin_init',array($this,  'maybe_load_iframe'), 30);

    }

    public function clear_category_cache($post_ID, $post_after=false, $post_before=false){
        delete_transient('zrdn_recipe_categories');
    }

    public function register_image_sizes()
    {
        if (function_exists('add_image_size')) {
            add_image_size('zrdn_grid_image_landscape', 380, 250, true);

            //use in mosaic
            add_image_size('zrdn_grid_image_portrait', 380, 450, true);
            add_image_size('zrdn_grid_image_portrait_large', 790, 900, true);
        }
    }


    public function clear_json_cache($post_ID, $post_after=false, $post_before=false){
        delete_transient('zrdn_grid_json');
    }

    /**
     *
     *enqueue
     *
     */


    public function zrdn_grid_load_more()
    {
        $search = (isset($_GET['search']) && strlen($_GET['search'])>0) ? sanitize_text_field($_GET['search']) : '';
        $category = (isset($_GET['category']) && strlen($_GET['category'])>0) ? sanitize_title($_GET['category']) : '';
        $showTitle = (isset($_GET['showTitle'])) ? intval($_GET['showTitle']) : false;
        $page = (isset($_GET['page'])) ? intval($_GET['page']) : 0;
        $layoutMode = (isset($_GET['layoutMode'])) ? sanitize_text_field($_GET['layoutMode']) : 'grid';
        $recipesPerPage = (isset($_GET['recipesPerPage'])) ? intval($_GET['recipesPerPage']) : 20;
        $args = array(
            'post_status'=>'publish',
            'offset' => $page * $recipesPerPage,
            'number' => $recipesPerPage,
            'search' => $search,
            'category' => $category,
            'searchFields' => 'all',
        );
        $recipes = Util::get_recipes($args);
        $count = count($recipes);

        $html = $this->get_grid_items_html($recipes, $showTitle, $layoutMode);

        $response = json_encode(array(
            'html' => $html,
            'count' => $count,
        ));
        header("Content-Type: application/json");
        echo $response;
        exit;
    }


    /**
     * load styles and scripts on grid pages.
     */
    public function enqueue_assets()
    {
        if ($this->is_grid_page()) {
            $min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

            wp_register_style('zrdn-grid-css', ZRDN_PLUGIN_URL . $this->dir."css/recipegrid.css", false, ZRDN_VERSION_NUM);
            wp_enqueue_style('zrdn-grid-css');

            wp_enqueue_script('zrdn-grid', ZRDN_PLUGIN_URL . $this->dir . "js/jquery.cubeportfolio.min.js", array('jquery'), ZRDN_VERSION_NUM, true);
            //	    wp_enqueue_script('zrdn-grid-custom', ZRDN_PLUGIN_URL . $this->dir . "js/main$min.js", array('jquery'), ZRDN_VERSION_NUM, true);

        }

    }

    public function add_grid_editor_button()
    {
        // check user permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        // check if WYSIWYG is enabled
        if (get_user_option('rich_editing') == 'true') {
            add_filter('mce_external_plugins', array($this, 'tinymce_plugin'));
            add_filter('mce_buttons', array($this, 'register_tinymce_button'));
        }
    }

    public static function register_tinymce_button($buttons)
    {
        array_push($buttons, "zrdn_buttons_grid");
        return $buttons;
    }


    public function tinymce_css() {
        wp_register_style('zrdn-tinymce-css', ZRDN_PLUGIN_URL . $this->dir."css/tinymce.css", false, ZRDN_VERSION_NUM);
        wp_enqueue_style('zrdn-tinymce-css');
    }




    public function tinymce_plugin($plugin_array)
    {
        $plugin_array['zrdn_plugin_grid'] = ZRDN_PLUGIN_URL . $this->dir . "js/grid-popup.js?sver=" . ZRDN_VERSION_NUM;
        return $plugin_array;
    }


    public function enqueue_assets_gutenberg()
    {

        $min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

        wp_register_style('zrdn-grid-css', ZRDN_PLUGIN_URL . $this->dir."css/recipegrid$min.css", false, ZRDN_VERSION_NUM);
        wp_enqueue_style('zrdn-grid-css');
        wp_enqueue_script('zrdn-grid', ZRDN_PLUGIN_URL . $this->dir . "js/jquery.cubeportfolio.min.js", array('jquery'), ZRDN_VERSION_NUM, true);
//	    wp_enqueue_script('zrdn-grid-custom', ZRDN_PLUGIN_URL . $this->dir . "js/main$min.js", array('jquery'), ZRDN_VERSION_NUM, true);

    }

    /**
     * checks if the current page contains the grid shortcode.
     * @param int|bool $post_id
     * @return boolean
     * @since 1.0
     */

    public function is_grid_page($post_id = false)
    {

        $block = 'zip-recipes/recipe-grid-block';

        if ($post_id){
            $post = get_post($post_id);
        } else {
            global $post;
        }

        if ($post) {
            if (Util::uses_gutenberg() && !Util::uses_elementor() && has_block($block, $post)) return true;
            if (has_shortcode($post->post_content, 'ziprecipes')) return true;
            if (has_shortcode($post->post_content, 'zrdn-grid')) return true;
        }
        return false;
    }


    /**
     * get caroussel json
     * @param $recipes
     * @return string json
     */

    private function json($recipes){
        //get the current recipe grid URL
        global $post;
        $grid_url = $post ? get_permalink($post) : "#";
        $recipe_json_ld = get_transient('zrdn_grid_json');
        if (!$recipe_json_ld) {
            $position = 1;
            $itemlistElements = array();
            foreach ($recipes as $recipe) {
                $recipe = new Recipe($recipe->recipe_id, false, false );
                $recipeItem = $recipe->jsonld();
                $recipeItem["url"] = $grid_url;
                $itemlistElements[] = array(
                    "@type" => "ListItem",
                    "position" => $position,
                    "item" => $recipeItem
                );

                $position++;
            }

            $recipe_json_ld = array(
                "@context" => "http://schema.org",
                "@type" => "ItemList",
                "itemListElement" => $itemlistElements
            );

            set_transient('zrdn_grid_json',$recipe_json_ld, DAY_IN_SECONDS);
        }


        return $recipe_json_ld;
    }


    /**
     * loads document content on shortcode call
     *
     * @param array $atts
     * @param null $content
     * @param string $tag
     * @return string $html
     *
     *
     */

    public function render_grid($atts = array(), $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        //old shortcodes are:
        //[ziprecipes grid id='1']
        //[ziprecipes index id='1']


        ob_start();

        /**
         * Layoutmode: grid | mosaic
         * showTitle : true | false //shows title below the picture
         * animationType
         * 'fadeOut'
            'quicksand'
            'bounceLeft'
            'bounceTop'
            'bounceBottom'
            'moveLeft'
            'slideLeft'
            'fadeOutTop'
            'sequentially'
            'skew'
            'slideDelay'
            'rotateSides'
            'flipOutDelay'
            'flipOut'
            'unfold'
            'foldLeft'
            'scaleDown'
            'scaleSides'
            'frontRow'
            'flipBottom'
            'rotateRoom'
         */
        $defaults = apply_filters('zrdn_default_grid_settings', array(
            'category' => 'all',
            'showtitle' => false,
            'layoutmode'=>'grid',
            'animationtype' =>'quicksand',
            'gaphorizontal' => 0,
            'gapvertical' => 0,
            'size' => 'medium',
            'recipesperpage' => 20,
            'loadmorebutton' => true,
            'backgroundcolor' => '#545454',
            'color' => '#fff',
            'bordercolor' => '#5d5d5d',
            'search' => true,
            'use_ajax' => false,
        ));

        // override default attributes with user attributes
        $atts = shortcode_atts($defaults, $atts, $tag);
        $layoutMode = sanitize_text_field($atts['layoutmode']);
        $category = sanitize_text_field($atts['category']);
        $animationType = sanitize_text_field($atts['animationtype']);
        $size = sanitize_text_field($atts['size']);
        $gapHorizontal = intval($atts['gaphorizontal']);
        $gapVertical = intval($atts['gapvertical']);
        $showTitle = $this->boolVal($atts['showtitle']);
        $recipesPerPage = intval($atts['recipesperpage']);
        $loadmoreButton = $this->boolVal($atts['loadmorebutton']);
        $search = $this->boolVal($atts['search']);
        $use_ajax = $this->boolVal($atts['use_ajax']);
        $backgroundColor = sanitize_hex_color($atts['backgroundcolor']);
        $color = sanitize_hex_color($atts['color']);
        $borderColor = sanitize_hex_color($atts['bordercolor']);

        switch ($size){
            case 'large':
                $colsXL = 4;
                break;
            case 'medium':
                $colsXL = 5;
                break;
            case 'small':
                $colsXL = 6;
                break;
            default:
                $colsXL = 5;
        }

        $colsL = $colsXL>1 ? $colsXL-1 : 1;
        $colsM = $colsL>1 ? $colsL-1 : 1;
        $colsS = $colsM>1 ? $colsM-1 : 1;

        $settings = apply_filters('zrdn_grid_settings', array(
            'url' => admin_url('admin-ajax.php'),
            'animationType' => $animationType,
            'layoutMode'=> $layoutMode,
            'gapHorizontal' => $gapHorizontal,
            'gapVertical' => $gapVertical,
            'colsXL' => $colsXL,
            'colsL' => $colsL,
            'colsM' => $colsM,
            'colsS' => $colsS,
            'showTitle' => $showTitle,
            'recipesPerPage' => $recipesPerPage,
            'category' => $category,
            'use_ajax' => $use_ajax,
        ));

        if ($use_ajax) {
            foreach ($this->get_categories() as $c_slugs => $cat){
	            $settings['category_counts']['.'.$c_slugs] = $cat['count'];
            }
        }

        $min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

        wp_enqueue_script('zrdn-grid-custom', ZRDN_PLUGIN_URL . $this->dir . "js/main$min.js", array('jquery'), ZRDN_VERSION_NUM, true);
        wp_localize_script('zrdn-grid-custom', 'zrdn_grid_settings', $settings);
        $args = array(
            'post_status'=>'publish',
            'number' => -1,
            'category' => $category,
        );
        $all_recipes = Util::get_recipes($args);
        //only use cats if we have more than one category
        $all_categories = ($category ==='all') ? $this->get_categories() : array();
        $json = json_encode($this->json($all_recipes));

        $args = array(
            'post_status'=>'publish',
            'offset' => 0,
            'number' => $recipesPerPage,
            'category' => $category,
        );
        $recipes = Util::get_recipes($args);
        $grid_items = $this->get_grid_items_html($recipes, $showTitle, $layoutMode);

        $category_list = '';
        if (count($all_categories)>1) {
            $category_list = Util::render_template('plugins/RecipeGrid2/views/categorylist-item.php', false, array('filter' => '*','filter-label'=>__('All','zip-recipes')));
            foreach ($all_categories as $slug => $category){
                $category_list .= Util::render_template('plugins/RecipeGrid2/views/categorylist-item.php', false, array('filter' => '.'.$slug,'filter-label'=> $category['name']));
            }
        }

        $loadmore_items = count($all_recipes) - $recipesPerPage;
        $loadmore_items = $loadmore_items<0 ? 0 : $loadmore_items;
        $settings = array(
            'categories' => $category_list,
            'grid-items' => $grid_items,
            'jsonld' => $json,
            'loadmore-items' => $loadmore_items,
            'showTitle' => $showTitle,
            'loadmoreButton' => $loadmoreButton,
            'search' => $search,
            'backgroundColor' => $backgroundColor,
            'color' => $color,
            'borderColor' => $borderColor,
        );

        $html = Util::render_template('plugins/RecipeGrid2/views/grid-wrap.php', false, $settings);
        echo $html;

        return ob_get_clean();
    }

    private function boolVal($val){
        return ($val==1 || $val=='true') ? true : false;

    }

    /**
     * Get grid html
     * @param $recipes
     * @param $showTitle
     * @param $layoutMode
     * @return string
     */
    public function get_grid_items_html($recipes, $showTitle, $layoutMode){

        $grid_items = '';
        foreach($recipes as $index => $recipe) {
            $recipe = new Recipe($recipe->recipe_id, false, false );
            $recipes[$index] = $recipe;
            if ($layoutMode=='mosaic') {
                //get random size from array
                $sizes = array(
                    'zrdn_grid_image_landscape',
                    'zrdn_grid_image_portrait',
                    'zrdn_grid_image_portrait_large',
                );
                $key = array_rand($sizes);
                $size = $sizes[$key];

            } else {
                $size = 'zrdn_grid_image_landscape';

            }
            $image = wp_get_attachment_image($recipe->recipe_image_id, $size);
            if (!$image) {
                $image = $this->get_placeholder();
            }
            $post_categories = wp_get_post_categories($recipe->post_id);
            $cats = array();
            foreach($post_categories as $c){
                $cat = get_category( $c );
                $cats[$cat->slug] =$cat->name;
            }
            $args = array(
                'image' => $image,
                'title' => $recipe->recipe_title,
                'author' => $recipe->author,
                'url' => get_permalink($recipe->post_id),
                'category' => implode(' ', array_keys($cats)),
                'showTitle' => $showTitle,
            );
            $grid_items .= Util::render_template('plugins/RecipeGrid2/views/grid-item.php', false, $args);
        }
        return $grid_items;
    }

    public function get_placeholder(){
        $html = '<img src="'.ZRDN_PLUGIN_URL.$this->dir.'images/placeholder.png">';
        return $html;
    }

    /**
     * Get list of categories that recipes are linked to
     * @return array|mixed
     */

	public static function get_categories(){

		$all_categories = Util::get_recipe_categories();

        return $all_categories;
    }




    /**
     * Get size information for all currently-registered image sizes.
     *
     * @global $_wp_additional_image_sizes
     * @uses   get_intermediate_image_sizes()
     * @return array $sizes Data for all currently-registered image sizes.
     */
    private function get_image_sizes() {
        global $_wp_additional_image_sizes;

        $sizes = array();

        foreach ( get_intermediate_image_sizes() as $_size ) {
            if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
                $sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
                $sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
                $sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
                $sizes[ $_size ] = array(
                    'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
                    'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                    'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
                );
            }
        }

        return $sizes;
    }

    /**
     * Get size information for a specific image size.
     *
     * @uses   get_image_sizes()
     * @param  string $size The image size for which to retrieve data.
     * @return bool|array $size Size data about an image size or false if the size doesn't exist.
     */
    private function get_image_size( $size ) {
        $sizes = $this->get_image_sizes();

        if ( isset( $sizes[ $size ] ) ) {
            return $sizes[ $size ];
        }

        return false;
    }


    /**
     * Get the width of a specific image size.
     *
     * @uses   get_image_size()
     * @param  string $size The image size for which to retrieve data.
     * @return bool|string $size Width of an image size or false if the size doesn't exist.
     */
    private function get_image_width( $size ) {
        if ( ! $size = $this->get_image_size( $size ) ) {
            return false;
        }

        if ( isset( $size['width'] ) ) {
            return $size['width'];
        }

        return false;
    }

    /**
     * Get the height of a specific image size.
     *
     * @uses   get_image_size()
     * @param  string $size The image size for which to retrieve data.
     * @return bool|string $size Height of an image size or false if the size doesn't exist.
     */
    private function get_image_height( $size ) {
        if ( ! $size = $this->get_image_size( $size ) ) {
            return false;
        }

        if ( isset( $size['height'] ) ) {
            return $size['height'];
        }

        return false;
    }


    /**
     * Load iframe has to hook into admin init, otherwise languages are not loaded yet.
     *
     * */

    public function maybe_load_iframe()
    {
        // Setup query catch for recipe insertion popup.
        if (strpos($_SERVER['REQUEST_URI'], 'media-upload.php') && strpos($_SERVER['REQUEST_URI'], '&type=z_recipe_grid') && !strpos($_SERVER['REQUEST_URI'], '&wrt=')) {
            // pluggable.php is needed for current_user_can
            require_once(ABSPATH . 'wp-includes/pluggable.php');

            // user is logged in and can edit posts or pages
            if (\current_user_can('edit_posts') || \current_user_can('edit_pages')) {
                $get_info = $_REQUEST;
                $post_id = isset($get_info["post_id"]) ? intval($get_info["post_id"]) : 0;

                ?>
                    <input type="text">
                <?php
                echo $post_id ."test";

            }
            exit;
        }
    }



}
