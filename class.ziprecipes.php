<?php
namespace ZRDN;

if ( ! defined( 'ABSPATH' ) ) exit;

//use Cassandra\Custom;

use ZRDN\Recipe as RecipeModel;
class ZipRecipes {

    const MAIN_CSS_SCRIPT = "zrdn-recipes";
    const MAIN_PRINT_SCRIPT = "zrdn-print-js";

    public static $suffix = '';
    public static $field;
    public static $authors;
	public static $addons_lover = array(
		'Authors',
		'AutomaticNutrition',
		'ServingAdjustment',
		'RecipeActions',
		'RecipeSearch',
		'Import',
		'RecipeReviews',
		'VisitorRating',
		'RecipeGrid2',
		'CustomTemplates',
		'ImperialMetricsConverter',
		'MostPopularRecipes',
	);

    /**
     * Init function.
     */
    public static function init()
    {
        if (is_admin()) {
            self::$field = new ZRDN_Field();
        }
        self::$suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
	    add_action('zrdn_tab_content', __NAMESPACE__ . '\ZipRecipes::extensions_tab');
	    add_action('plugins_loaded', __NAMESPACE__ . '\ZipRecipes::load_plugins', 20);
	    add_action('plugins_loaded', __NAMESPACE__ . '\ZipRecipes::process_template_update', 21);
	    add_action('plugins_loaded', __NAMESPACE__ . '\ZipRecipes::process_settings_update', 22);


	    // We need to call `zrdn__init_hooks` action before `init_hooks()` because some actions/filters registered
        //	in `init_hooks()` get called before plugins have a chance to register their hooks with `zrdn__init_hooks`
	    add_action('admin_head', __NAMESPACE__ . '\ZipRecipes::zrdn_js_vars');
	    add_action('admin_init', __NAMESPACE__ . '\ZipRecipes::zrdn_add_recipe_button');
	    add_action( 'admin_bar_menu', __NAMESPACE__ . '\ZipRecipes::add_top_bar_edit_button', 90 );


	    // `the_post` has no action/filter added on purpose. It doesn't work as well as `the_content`.
	    // We're using priority of 11 here because in some cases VisualComposer seems to be running
	    //  a hook after us and adding <br /> and <p> tags
	    add_filter('the_content', __NAMESPACE__ . '\ZipRecipes::zrdn_convert_to_full_recipe', 11);
	    add_action('admin_enqueue_scripts', __NAMESPACE__ . '\ZipRecipes::enqueue_admin_assets');
	    add_action('admin_footer', __NAMESPACE__ . '\ZipRecipes::zrdn_plugin_footer');
	    add_filter('amp_post_template_metadata', __NAMESPACE__ . '\ZipRecipes::amp_format', 10, 2);
	    add_action('amp_post_template_css', __NAMESPACE__ . '\ZipRecipes::amp_styles');
	    // check GD or imagick support
	    add_action('admin_notices', __NAMESPACE__ . '\ZipRecipes::zrdn_check_image_editing_support');
	    add_action('the_content', __NAMESPACE__ . '\ZipRecipes::jump_to_recipes_button');

	    add_action('init', __NAMESPACE__ . '\ZipRecipes::zrdn_recipe_install');
	    add_action('wp_ajax_zrdn_save_template', __NAMESPACE__ . '\ZipRecipes::save_template_structure');

	    add_action('init',__NAMESPACE__ . '\ZipRecipes::register_images');
	    add_action('init',__NAMESPACE__ . '\ZipRecipes::reset_template_settings');

	    add_action('wp_enqueue_scripts',__NAMESPACE__ . '\ZipRecipes::enqueue_scripts', 10);
	    add_action('zrdn_enqueue_scripts',__NAMESPACE__ . '\ZipRecipes::load_assets', 10);
	    add_action('zrdn_update_option',__NAMESPACE__ . '\ZipRecipes::update_template', 10, 4);

	    add_action('zrdn_after_update_options',  __NAMESPACE__. '\ZipRecipes::set_defaults_for_template', 10, 1);

	    add_action( 'zrdn_load_recipe',  __NAMESPACE__. '\ZipRecipes::on_load_recipe' , 10, 1 );
	    $plugin = ZRDN_PLUGIN_BASENAME;
	    add_filter( "plugin_action_links_$plugin",
		    __NAMESPACE__ . '\ZipRecipes::plugin_settings_link'  );
	    //multisite
	    add_filter( "network_admin_plugin_action_links_$plugin",
		    __NAMESPACE__ . '\ZipRecipes::plugin_settings_link' );
	    add_filter("zrdn_update_option", __NAMESPACE__ . '\ZipRecipes::maybe_reset_nutrition_import' , 10, 4);

	    //add official shortcode support
	    add_shortcode( 'zrdn-recipe', __NAMESPACE__ . '\ZipRecipes::load_recipe_shortcode' );

	    add_filter('zrdn_tabs', __NAMESPACE__ . '\ZipRecipes::add_menu_tabs');
	    add_action('admin_init', __NAMESPACE__ . '\ZipRecipes::run_first_install_init', 20 );
    }

	/**
	 * Install a demo recipe on activation
	 */

	public static function run_first_install_init() {
	    if (!get_option('zrdn_activated_once')) {
		    update_option('zrdn_activated_once', true);
			//demo recipe
			$args = array(
				'searchFields' => 'recipe_title',
				'search'       => __( 'Demo Recipe', 'zip-recipes' ),
			);

			$recipes = Util::get_recipes( $args );
			if ( count( $recipes ) == 0 ) {
				$recipe = new Recipe();
				$recipe->load_default_data();
				$recipe->recipe_title    = __( 'Demo Recipe', 'zip-recipes' );

				$recipe->save();
				update_option( 'zrdn_demo_recipe_id', $recipe->recipe_id );

				//we do this separately, in case there is an issue with file insertion. This we the recipe can be created, only not with image.
				$recipe->recipe_image_id = ZipRecipes::insert_media( ZRDN_PATH . 'images', 'demo-recipe.jpg' );
				$recipe->save();
			}

			//set some defaults
			$settings = get_option('zrdn_settings_general');
			$zrdn_print['show_summary_on_archive_pages'] = true;
			update_option('zrdn_settings_general', $settings);

		}
	}

	/**
     * Add tabs to the menu header
	 * @param array $tabs
	 *
	 * @return array mixed
	 */
    public static function add_menu_tabs($tabs){
	    $tabs = $tabs + array(
		    'recipes' => array(
			    'title' => __('Recipes', 'zip-recipes'),
			    'page' => 'zrdn-recipes',
		    ),
		    'template' => array(
			    'title' => __('Template', 'zip-recipes'),
			    'page' => 'zrdn-template',
		    ),
		    'dashboard' => array(
			    'title' => __('Settings', 'zip-recipes'),
			    'page' => 'zrdn-settings',
		    ),
		    'extensions' => array(
			    'title' => __('Extensions', 'zip-recipes'),
			    'page' => 'zrdn-settings',
		    ),
	    );
        return apply_filters("zrdn_menu_tabs", $tabs);
    }


	/**
	 * Track recipe visit
	 *
	 * @param Recipe $recipe
	 */

	public static function on_load_recipe( $recipe ) {
		if (!ZipRecipes::is_rest() && !is_admin() && !is_singular()) {
			$recipe->track_hit();
		}
	}

    public static function enqueue_scripts (){
	    do_action('zrdn_enqueue_scripts');
    }

	public static function maybe_reset_nutrition_import($new, $old, $fieldname, $source) {
		if (!current_user_can('manage_options')) return $new;

		if ( $source == 'nutrition' && $fieldname == 'import_nutrition_data_all_recipes' && $new !== $old ) {
			update_option("zrdn_nutrition_data_import_completed", false);
		}
		return $new;
	}

	/**
     * if the template default has changed, reload the template structure the matches this template
	 * @param $new_value
	 * @param $old_value
	 * @param $fieldname
	 * @param $source
     *
     * @return mixed
	 */

	public static function update_template($new_value, $old_value, $fieldname, $source) {
		if (!current_user_can('manage_options')) return $new_value;

        if ( $source === 'template' && $fieldname === 'template' && $new_value !== $old_value) {
	        $template = ZipRecipes::default_recipe_blocks($new_value);
	        update_option('zrdn_recipe_blocks_layout', $template);
	        do_action('zrdn_change_template');
        }
        return $new_value;
    }

	/**
	 * Add custom link to plugins overview page
	 *
	 * @hooked plugin_action_links_$plugin
	 *
	 * @param array $links
	 *
	 * @return array $links
	 */

	public static function  plugin_settings_link( $links ) {
		$settings_link = '<a href="'
		                 . admin_url( "admin.php?page=zrdn-settings" )
		                 . '" class="zrdn-settings-link">'
		                 . __( "Settings", 'zip-recipes' ) . '</a>';
		array_unshift( $links, $settings_link );

		$support_link = defined( 'ZRDN_FREE' )
			? "https://wordpress.org/support/plugin/zip-recipes"
			: "https://ziprecipes.net/support";
		$faq_link     = '<a target="_blank" href="' . $support_link . '">'
		                . __( 'Support', 'zip-recipes' ) . '</a>';
		array_unshift( $links, $faq_link );

		if ( defined( 'ZRDN_FREE' ) ) {
			$upgrade_link
				= '<a style="color:#F343A0;font-weight:bold" target="_blank" href="https://ziprecipes.net/premium">'
				  . __( 'Upgrade to premium', 'zip-recipes' ) . '</a>';
			array_unshift( $links, $upgrade_link );
		}

		return $links;
	}

	/**
	 * Add an edit recipe button to the admin toolbar
	 */

	public static function add_top_bar_edit_button() {
		global $wp_admin_bar;

		if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		/*
		 * Show "view post" link in top of recipe
		 */
		if ( isset($_GET["page"]) && $_GET["page"] == "zrdn-recipes" && isset($_GET["id"]) ) {
			$title = __("View post", "zip-recipes");
			$recipe = new Recipe(intval($_GET["id"]));
			$url = get_permalink($recipe->post_id);
			if ($url) {
				$wp_admin_bar->add_menu( array( 'id' => 'zrdn_view_post', 'title' => $title, 'href' => esc_url_raw($url) ) );

			}

			/*
			 * Show "edit recipe" link in post/page
			 */
		} else {
			//only add recipe button on single pages
			if ( !is_singular() ) {
				return;
			}

			global $post;
			if (!$post) return;

			$recipe = new Recipe(false, $post->ID);
			if ($recipe->recipe_id) {
				$url = add_query_arg(array('page'=>'zrdn-recipes', 'id' => $recipe->recipe_id), admin_url('admin.php'));
				$title = __( 'Edit recipe', 'zip-recipes' );
			} else {
				$url = add_query_arg(array('page'=>'zrdn-recipes', 'action' => 'new', 'post_id' => $post->ID, 'post_type' =>$post->post_type ), admin_url('admin.php'));
				$title = __( 'Insert new recipe', 'zip-recipes' );
			}


			$wp_admin_bar->add_menu( array( 'id' => 'zrdn_edit_recipe', 'title' => $title, 'href' => esc_url_raw($url) ) );
		}
	}

	/**
	 * Load add ons
	 */

    public static function load_plugins(){
	    // Instantiate plugin classes
	    $parentPath = dirname(__FILE__);
	    $pluginsPath = "$parentPath/plugins";
	    $active_plugins = Util::get_active_plugins();

        if ( count($active_plugins)>0) {
	        require_once($pluginsPath.'/base.php');
        }

	    foreach ($active_plugins as $plugin_name){
		    $pluginPath = $pluginsPath."/".$plugin_name.'/'.$plugin_name.'.php';
		    if (!file_exists($pluginPath)) {
			    $fields = Util::get_fields();
			    if (isset($fields[$plugin_name])) {
				    $source = $fields[ $plugin_name ]['source'];
				    $zrdn_settings = get_option( "zrdn_settings_$source" );
				    $zrdn_settings[ $plugin_name ] = false;
				    update_option( "zrdn_settings_$source", $zrdn_settings );
			    }
			    continue;
		    }
		    require_once($pluginPath);

		    // instantiate class
		    $namespace = __NAMESPACE__;
		    $fullPluginName = "$namespace\\$plugin_name"; // double \\ is needed because \ is an escape char
		    $pluginInstance = new $fullPluginName;
	    }
	    do_action("zrdn__init_hooks"); // plugins can add an action to listen for this event and register their hooks

	    self::$authors = Util::get_authors();
    }

	/**
	 * Register Zip Recipes image sizes
	 */

    public static function register_images(){
        if ( function_exists( 'add_image_size' ) ) {
            add_image_size( 'zrdn_recipe_image_main',   800,  600, true);
            add_image_size( 'zrdn_recipe_image_json_1x1',   1200,  1200, true);
            add_image_size( 'zrdn_recipe_image_json_4x3',   1200,  900, true);
            add_image_size( 'zrdn_recipe_image_json_16x9',   1600,  900, true);

            //as fallback, we add some images that are just above the google treshold of 50000K
            add_image_size( 'zrdn_recipe_image_json_1x1_s',   250,  250, true);
            add_image_size( 'zrdn_recipe_image_json_4x3_s',   198, 164,   true);
            add_image_size( 'zrdn_recipe_image_json_16x9_s',   320,  200, true);

	        //custom print button
	        add_image_size('zrdn_custom_print_image', 40, 40, true);
        }
    }

    public static function enqueue_admin_assets($hook){
	    if (strpos($hook, "zrdn")===false ) return;

	    wp_register_style('zrdn-admin-styles',
		    trailingslashit(ZRDN_PLUGIN_URL) . "admin/css/style.css", "",
		    ZRDN_VERSION_NUM);
	    wp_enqueue_style('zrdn-admin-styles');


	   if (strpos($hook, "zrdn-settings")===false ) return;
	    wp_enqueue_script( 'zrdn-ace', ZRDN_PLUGIN_URL . "admin/assets/ace/ace.js",
		    array(), ZRDN_VERSION_NUM, false );
    }


    /**
     * This is used to get post title in recipe insertion iframe
     */
    public static function zrdn_js_vars()
    {
        if (is_admin()) {
            ?>
            <script type="text/javascript">
                var post_id = '<?php
                    global $post;
                    if (isset($post)) {
                        echo $post->ID;
                    }
                    ?>';
            </script>
            <?php
        }
    }


    public static function zrdn_add_recipe_button()
    {
        // check user permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        // check if WYSIWYG is enabled
        if (get_user_option('rich_editing') == 'true') {
            add_filter('mce_external_plugins', __NAMESPACE__ . '\ZipRecipes::zrdn_tinymce_plugin');
            add_filter('mce_buttons', __NAMESPACE__ . '\ZipRecipes::zrdn_register_tinymce_button');
        }
    }

	/**
     * Render a recipe using the modern shortcode registration
	 * @param array  $atts
	 * @param null   $content
	 * @param string $tag
	 *
	 * @return false|string
	 */

    public static function load_recipe_shortcode($atts = array(), $content = null, $tag = '') {
			// normalize attribute keys, lowercase
			$atts = array_change_key_case( (array) $atts, CASE_LOWER );
			ob_start();

			// override default attributes with user attributes
			$atts   = shortcode_atts( array(
				'id'   => false,
			),
				$atts, $tag );
			$recipe_id   = sanitize_title( $atts['id'] );

			if (!$recipe_id) return '';

            $recipe = new Recipe($recipe_id);
            echo self::zrdn_format_recipe($recipe);

            return ob_get_clean();
        }

    /**
     * Replace zip recipes shortcodes with actual, full, formatted recipe(s).
     *
     * @param $post_text String Text of the post which to replace shortcodes in
     *
     * @return String updated $post_text with formatted recipe(s)
     */
    public static function zrdn_convert_to_full_recipe($post_text)
    {
        $output = $post_text;
        $needle_old = 'id="amd-zlrecipe-recipe-';
        $preg_needle_old = '/(id)=("(amd-zlrecipe-recipe-)[0-9^"]*")/i';
        $needle = '[amd-zlrecipe-recipe:';
        $preg_needle = '/\[amd-zlrecipe-recipe:([0-9]+)\]/i';

        if (strpos($post_text, $needle_old) !== false) {
            // This is for backwards compatability. Please do not delete or alter.
            preg_match_all($preg_needle_old, $post_text, $matches);
            foreach ($matches[0] as $match) {
                $recipe_id = str_replace('id="amd-zlrecipe-recipe-', '', $match);
                $recipe_id = str_replace('"', '', $recipe_id);
                $recipe = new Recipe($recipe_id);
                $formatted_recipe = self::zrdn_format_recipe($recipe);
                $output = str_replace('<img id="amd-zlrecipe-recipe-' . $recipe_id . '" class="amd-zlrecipe-recipe" src="' . plugins_url() . '/' . dirname(plugin_basename(__FILE__)) . '/images/zip-recipes-placeholder.png?ver=1.0" alt="" />', $formatted_recipe, $output);
            }
        }

        if (strpos($post_text, $needle) !== false) {
            preg_match_all($preg_needle, $post_text, $matches);
            foreach ($matches[0] as $match) {
                $recipe_id = str_replace('[amd-zlrecipe-recipe:', '', $match);
                $recipe_id = str_replace(']', '', $recipe_id);
                $recipe = new Recipe($recipe_id);
                $formatted_recipe = self::zrdn_format_recipe($recipe);
                $output = str_replace('[amd-zlrecipe-recipe:' . $recipe_id . ']', $formatted_recipe, $output);
            }
        }
        return $output;
    }

    /**
     * Load css/js files
     */
    public static function load_assets()
    {
	    wp_register_style('zrdn-recipeblock-grid', plugins_url('styles/zrdn-grid' . self::$suffix . '.css', __FILE__), array(), ZRDN_VERSION_NUM, 'all');
	    wp_enqueue_style('zrdn-recipeblock-grid');

        if (Util::get_option('use_zip_css')) {
            wp_register_style(self::MAIN_CSS_SCRIPT, plugins_url('styles/zlrecipe-std' . self::$suffix . '.css', __FILE__), array(), ZRDN_VERSION_NUM, 'all');
            wp_enqueue_style(self::MAIN_CSS_SCRIPT);
        }
        wp_register_script(self::MAIN_PRINT_SCRIPT, plugins_url('scripts/zlrecipe_print' . self::$suffix . '.js', __FILE__), array('jquery'), ZRDN_VERSION_NUM, true);
        wp_enqueue_script(self::MAIN_PRINT_SCRIPT);
	    $grid_style = apply_filters('zrdn_print__grid_style_url', ZRDN_PLUGIN_DIRECTORY_URL.'styles/zrdn-grid.css?v='.ZRDN_VERSION_NUM);
	    $print_css = apply_filters('zrdn_print__print_style_url', ZRDN_PLUGIN_DIRECTORY_URL.'styles/zrdn-print.css?v='.ZRDN_VERSION_NUM);
	    $stylesheet = '';
	    if (Util::get_option('use_zip_css')) {
		    $stylesheet = ZRDN_PLUGIN_DIRECTORY_URL . 'styles/zlrecipe-std.css?v=' . ZRDN_VERSION_NUM ;
	    }
	    apply_filters( 'zrdn_print_style_url', $stylesheet);
	    wp_localize_script(
	            self::MAIN_PRINT_SCRIPT,
            'zrdn_print_styles',
            array(
                    'grid_style' => $grid_style,
                    'stylesheet_url' => $stylesheet,
                    'print_css' => $print_css
            )
        );

	    if ( isset($_GET['mode']) && $_GET['mode'] === 'zrdn-preview') {
	        if (current_user_can('edit_posts')) {
		        $user_id = get_current_user_id();
		        $recipe_id = intval($_GET['id']);
		        $stored_temp_recipe_data = get_transient('zrdn_preview_recipe_data');
		        if ($stored_temp_recipe_data) unset($stored_temp_recipe_data[$user_id][$recipe_id]);
		        set_transient('zrdn_preview_recipe_data', $stored_temp_recipe_data , HOUR_IN_SECONDS);

		        set_transient('zrdn_preview_recipe_data', $stored_temp_recipe_data , HOUR_IN_SECONDS);
		        wp_register_style('zrdn-editor-preview', plugins_url('styles/zrdn-preview' . self::$suffix . '.css', __FILE__), array(), ZRDN_VERSION_NUM, 'all');
		        wp_enqueue_style('zrdn-editor-preview');
		        wp_register_script( 'zrdn-editor-preview',
			        plugins_url( 'scripts/zrdn-editor-preview' . self::$suffix
			                     . '.js', __FILE__ ), array( 'jquery' ),
			        ZRDN_VERSION_NUM, true );
		        wp_enqueue_script( 'zrdn-editor-preview' );
		        $args = array(
			        'admin_url'               => admin_url( 'admin-ajax.php' ),
			        'str_click_to_edit_image' => __( "Click to edit this image",
				        "zip-recipes" ),
			        'str_remove'              => __( "clear image",
				        "zip-recipes" ),
			        'default_image'           => ZRDN_PLUGIN_URL
			                                     . '/images/recipe-default-bw.png',
			        'nonce'                   => wp_create_nonce( 'zrdn_edit_recipe' ),
			        'image_placeholder'       => ZRDN_PLUGIN_URL
			                                     . '/images/s.png',

		        );
		        wp_localize_script( 'zrdn-editor-preview', 'zrdn_editor_preview', $args );
		        wp_enqueue_media();
	        }
	    }
    }

	/**
     * Get default list of blocks
     * @param string $name
	 * @return array();
	 */

    public static function default_recipe_blocks($name = 'default'){
        $templates['default'] = array(
	        array(
		        'type' => 'block-100',
		        'blocks' => array(
			        array( 'type' =>'recipe_title'),
			        array( 'type' =>'actions'),
			        array( 'type' =>'divider'),
			        array( 'type' =>'author'),
			        array( 'type' =>'category'),
			        array( 'type' =>'summary'),
			        array( 'type' =>'details'),
		        ),
	        ),
	        array(
		        'type' => 'block-100',
		        'blocks' => array(
			        array( 'type' =>'recipe_image'),
			        array( 'type' =>'notes'),
			        array( 'type' =>'ingredients'),
			        array( 'type' =>'instructions'),
			        array( 'type' =>'nutrition_label'),
			        array( 'type' =>'tags'),
			        array( 'type' =>'copyright'),
		        ),
	        ),
        );
	   $templates  = apply_filters('zrdn_recipe_templates', $templates );

        if (!isset($templates[$name])) $name = 'default';
	    return  apply_filters('zrdn_default_recipe_blocks', $templates[$name] );
    }

	/**
	 * set some defaults for the custom template
	 */

	public static function set_defaults_for_template() {
	    if (!current_user_can('manage_options')) return;

		if ( get_option( 'zrdn_reload_template_settings' ) ) {
			$template = util::get_option('template');

			if ( $template === 'default' ) {
				Util::update_option( 'ingredients_list_type', 'nobullets' );
				Util::update_option( 'instructions_list_type', 'numbers' );
				Util::update_option( 'primary_color', '#000000' );
				Util::update_option( 'background_color', '#ffffff' );
				Util::update_option( 'text_color', '#000000' );
				Util::update_option( 'border_width', '0' );
				Util::update_option( 'border_style', 'initial' );
				Util::update_option( 'border_color', '#000000' );
				Util::update_option( 'link_color', '#000000' );
			}

			if ( $template === 'custom' ) {
				$structure = array(
					array(
						'type' => 'block-100',
						'blocks' => array(),
					),
					array(
						'type' => 'block-50',
						'blocks' => array(),
					),
					array(
						'type' => 'block-50',
						'blocks' => array(),
					),
				);
				update_option('zrdn_recipe_blocks_layout', $structure);

				Util::update_option( 'ingredients_list_type', 'nobullets' );
				Util::update_option( 'instructions_list_type', 'numbers' );
				Util::update_option( 'primary_color', '#000000' );
				Util::update_option( 'background_color', '#ffffff' );
				Util::update_option( 'text_color', '#000000' );
				Util::update_option( 'border_width', '0' );
				Util::update_option( 'border_style', 'initial' );
				Util::update_option( 'border_color', '#000000' );
				Util::update_option( 'box_shadow', false );
				Util::update_option( 'link_color', '#000000' );

			}

			do_action('zrdn_set_defaults_for_template', $template);

			update_option('zrdn_reload_template_settings', false);
		}
	}

	/**
     * Get config of a specific block
	 * @param string $block
	 *
	 * @return array
	 */

    public static function get_block_data($block){
	    $all = ZipRecipes::all_available_blocks();
	    $index_in_all_array = array_search($block, array_column($all, 'type'));
	    return $all[$index_in_all_array];
    }

	/**
	 * Get default list of blocks
	 * @return array();
	 */

	public static function all_available_blocks(){
		$recipe_blocks = apply_filters('zrdn_all_recipe_blocks', array(
			array(
                'type' => 'recipe_title',
				'title' => __( "Recipe title", "zip-recipes" ),
				'single' => true,
                'settings' => false,
			),
			array(
				'type' => 'VisitorRating',
				'title' => __( "Visitor Rating", "zip-recipes" ),
				'single' => true,
				'settings' => false,
				'premium' => true,
				'active' => false,
			),
            array(
				'type' => 'RecipeReviews',
				'title' => __( "Recipe Reviews", "zip-recipes" ),
				'single' => true,
				'settings' => false,
				'premium' => true,
				'active' => false,
			),
			array(
				'type' => 'actions',
				'title' => __( "Actions", "zip-recipes" ),
				'single' => true,
				'settings' => true,
				'settings_height' => 275,
			),
			array(
				'type' => 'divider',
				'title' => __( "Divider", "zip-recipes" ),
				'single' => false,
				'settings' => false,
			),
			array(
				'type' => 'author',
				'title' => __( "Author", "zip-recipes" ),
				'single' => true,
			),
			array(
				'type' => 'category',
				'title' => __( "Categories", "zip-recipes" ),
				'single' => true,
				'settings' => false,
			),
			array(
				'type' => 'summary',
				'title' => __( "Summary", "zip-recipes" ),
				'single' => true,
				'settings' => false,
			),
			array(
				'type' => 'details',
				'title' => __( "Details", "zip-recipes" ),
				'single' => true,
				'settings' => false,
			),
			array(
				'type' => 'recipe_image',
				'title' => __( "Image", "zip-recipes" ),
				'single' => true,
				'settings' => true,
				'settings_height' => 170,
			),
			array(
				'type' => 'video',
				'title' => __( "Video", "zip-recipes" ),
				'single' => true,
				'settings' => false,
			),
			array(
				'type' => 'notes',
				'title' => __( "Notes", "zip-recipes" ),
				'single' => true,
				'settings' => true,
				'settings_height' => 100,
			),
			array(
				'type' => 'ingredients',
				'title' => __( "Ingredients", "zip-recipes" ),
				'single' => true,
				'settings' => true,
				'settings_height' => 200,
			),
			array(
				'type' => 'instructions',
				'title' => __( "Instructions", "zip-recipes" ),
				'single' => true,
				'settings' => true,
				'settings_height' => 200,
			),
			array(
				'type' => 'nutrition_label',
				'title' => __( "Nutrition Label", "zip-recipes" ),
				'single' => true,
				'settings' => true,
				'settings_height' => 100,
			),
			array(
				'type' => 'nutrition_text',
				'title' => __( "Nutrition info", "zip-recipes" ),
				'single' => true,
				'settings' => true,
			),
			array(
				'type' => 'tags',
				'title' => __( "Tags", "zip-recipes" ),
				'single' => true,
				'settings' => true,
				'settings_height' => 100,
			),
			array(
				'type' => 'social_sharing',
				'title' => __( "Social", "zip-recipes" ),
				'single' => true,
				'settings' => true,
				'settings_height' => 200,
			),
            array(
				'type' => 'copyright',
				'title' => __( "Copyright", "zip-recipes" ),
				'single' => true,
				'settings' => true,
				'settings_height' => 200,
            ),

		) );

		return $recipe_blocks;
	}

    /**
     * Formats the recipe for output
     *
     * @param Recipe $recipe
     * @return string
     */

    public static function zrdn_format_recipe($recipe)
    {
	    do_action('zrdn_load_recipe', $recipe);
        $settings = Util::get_option('all');

	    $settings = apply_filters('zrdn_recipe_settings', $settings, $recipe);

	    //get layout template
        $recipe_blocks_layout = ZipRecipes::get_active_template_layout();
	    $html = ZipRecipes::parse_layout_to_html($recipe_blocks_layout, $recipe, $settings);

        $settings['blocks'] = $html;
        $output = Util::render_template('recipe.php', $recipe, $settings);

	    if (!self::is_rest() && !is_admin() && !is_singular() && Util::get_option('show_summary_on_archive_pages')) {
		    $output = $recipe->summary;
	    }

        $output = apply_filters( 'zrdn_recipe_content', $output, $recipe->recipe_id );
        $output = do_shortcode( $output );

        return $output;
    }

	/**
     * Render blocks to array or html
	 * @param array $blocks
	 * @param Recipe $recipe
	 * @param array $settings
	 * @param bool $return_html
	 *
	 * @return string|array
	 */

    public static function parse_layout_to_html($blocks, $recipe, $settings, $return_html = true ) {
        $html = '';

        if (!$return_html) $field  = ZipRecipes::$field;

        foreach ( $blocks as $col_index => $column ) {
	        $child_blocks_html = '';
	        if (isset($column['blocks']) && is_array($column['blocks']) ) {
		        foreach ( $column['blocks'] as $index => $recipe_block ) {
			        $add_block_data = ! $return_html;
			        $tmpl = self::render_recipe_block( $recipe_block, $recipe, $settings, $add_block_data );
			        if ( $return_html ) {
				        $child_blocks_html .= $tmpl;
			        } else {
				        $blocks[ $col_index ]['blocks'][ $index ]['template'] = $tmpl;
				        $fields = Util::get_fields( $recipe_block['type'] );
				        $settings_html = '';
				        foreach ( $fields as $fieldname => $field_args ) {
					        $settings_html .= $field->get_field_html( $field_args , $fieldname, true);
				        }
				        $blocks[ $col_index ]['blocks'][ $index ]['settings'] = $settings_html;
			        }
		        }
	        }

	        //wrap in column block
	        if ($return_html) {
		        $html .= Util::render_template('block_wrap.php', $recipe, array( 'type' => $column['type'], 'blocks' => $child_blocks_html ));
	        } else {
		        $blocks[$col_index]['template'] = Util::render_template('block_wrap.php', $recipe, array( 'type' => $column['type'] ));;
	        }
        }

	    if ($return_html) {
	        return $html;
        } else {
	        return $blocks;
        }
    }

	/**
     * Check if this is a RESt_API request
	 * @return bool
	 */

	public static function is_rest(){
		return ( defined( 'REST_REQUEST' ) && REST_REQUEST );
	}

	public static function block_is_active($recipe_block) {
		if ( isset($recipe_block['active']) && !$recipe_block['active'] ) {
			return false;
		} else {
			return true;
		}
    }

    public static function block_is_premium($recipe_block) {
        if (isset($recipe_block['premium']) && $recipe_block['premium'] && defined('ZRDN_FREE')) {
            return true;
        } else {
	        return false;
        }
    }

	/**
	 * Render this particular recipe block
	 * @param array $recipe_block
	 * @param Recipe $recipe
	 * @param array $settings
	 * @return string
	 */

	public static function render_recipe_block( $recipe_block, $recipe, $settings, $add_block_data=false ){
		$block_data = '';
		$type = $recipe_block['type'];
	    ob_start();

		if ( !ZipRecipes::block_is_active($recipe_block) ) {
		    if ( is_admin() ) {
			    if (ZipRecipes::block_is_premium($recipe_block)) {
				    $html = __("Premium block, enable the premium plugin to activate this block.","zip-recipes");
			    } else {
				    $html = __("Inactive block, enable in the settings.","zip-recipes");
			    }
		    } else {
		    	$html = '';
		    }

        } else {
			do_action("zrdn_recipe_block", $type, $recipe, $settings);
			do_action("zrdn_recipe_block_$type", $recipe, $settings);
			$html = ob_get_clean();

			if (strlen($html)==0){
				$html = Util::render_template("$type.php", $recipe, $settings);
			}

			$html = apply_filters('zrdn_render_recipe_block', $html, $recipe_block, $recipe, $settings);

		}

		if ($add_block_data) {
		    $data = ZipRecipes::get_block_data($type);
		    $block_data = 'data-blocktype="'.$type.'" data-single="'.$data['single'].'"';
        }
		$html = '<div class="zrdn-block-wrap zrdn-'.$type.'" '.$block_data.' >'.$html.'</div>';
		$html .= "\n";
		return $html;
	}

	/**
     * Get the active layout
	 * @return array
	 */

	public static function get_active_template_layout( ){

		$template_name = Util::get_option('template');
		$recipe_blocks_layout = apply_filters('zrdn_default_recipe_blocks_layout', ZipRecipes::default_recipe_blocks( $template_name ) );
		$recipe_blocks_layout = get_option( 'zrdn_recipe_blocks_layout', $recipe_blocks_layout );
		$recipe_blocks_layout[] = array(
			'type' => 'block-0 print',
			'blocks' => array(
				array(
					'type' => 'permalink',
					'title' => __( "Link to recipe", "zip-recipes" ),
					'single' => true,
					'settings' => false,
				),
			),
		);
		$recipe_blocks_layout[] = array(
			'type' => 'block-0',
			'blocks' => array(
				array(
					'type' => 'jsonld',
					'title' => 'json',
					'single' => true,
					'settings' => false,
				),
			),
		);
		$all = ZipRecipes::all_available_blocks();

		//for each block, find the block in the "all" list, and get it's data
		foreach($recipe_blocks_layout as $index => $column_blocks ) {
		    if (isset($column_blocks['blocks']) && is_array($column_blocks['blocks'])) {
			    foreach ( $column_blocks['blocks'] as $sub_index => $data ) {
			        if (empty($data)) {
			            unset($recipe_blocks_layout[ $index ]['blocks'][ $sub_index ]);
			        } else {
				        $index_in_all_array = array_search( $data['type'],
					        array_column( $all, 'type' ) );
				        if ( $index_in_all_array !== false ) {
					        $recipe_blocks_layout[ $index ]['blocks'][ $sub_index ]
						        = array_merge( $data,
						        $all[ $index_in_all_array ] );
				        }
                    }
			    }
		    }
		}

		return $recipe_blocks_layout;
    }

	/**
	 * Reset settings on post submit
	 */

    public static function reset_template_settings(){
	    if (!current_user_can('manage_options')) return;

	    if (isset( $_POST['zrdn-reset-template'] ) && $_POST['zrdn-reset-template'] && isset($_POST['zrdn_edit_template_nonce']) && wp_verify_nonce($_POST['zrdn_edit_template_nonce'], 'zrdn_edit_template') ){
	        $default_recipe_blocks = ZipRecipes::default_recipe_blocks();
		    update_option('zrdn_settings_template', false);
		    update_option('zrdn_recipe_blocks_layout', $default_recipe_blocks);
	    }

    }

	/**
	 * Save the template structure from ajax call
	 *
	 */

    public static function save_template_structure(){
	    $error = false;
	    $msg = '';
	    if (!current_user_can('manage_options')) {
		    $error = true;
		    $msg = __("You do not have manage options permissions.","zip-recipes");
	    }

	    if (!$error && !wp_verify_nonce($_POST['nonce'], 'zrdn_edit_template')) {
		    $error = true;
		    $msg = __('An unexpected error has occurred. Please try again', "zip-recipes");
	    }

	    //make sure the last changes in the ingredients (which might not be saved yet) are saved before continuing
	    if (!$error && isset($_POST["template_structure"])){
		    $data = $_POST["template_structure"];
		    $data = $data==='false' ? false : $data;
		    if ( $data ) {
			    $total_count = $data ? count($data) : 0;

			    //clean up empty column blocks, but not if it's the default set from the custom template.
			    $is_default_custom_template = false;
			    if ($total_count === 3 && $data[0]['type'] === 'block-100' && $data[1]['type'] === 'block-50' && $data[2]['type'] === 'block-50') {
				    $is_default_custom_template = true;
			    }

			    if (!$is_default_custom_template) {
				    foreach ( $data as $index => $column ) {
					    if ( ! isset( $column['blocks'] )
					         || empty( $column['blocks'] )
					    ) {
						    if ( $column['type'] === 'block-100' ) {
							    unset( $data[ $index ] );
							    //if it's the last block, drop empty ones
						    } else if ( $index + 1 === $total_count ) {
							    unset( $data[ $index ] );
						    }
					    }
				    }
			    }

			    //save block settings
			    foreach ($data as $index => $column){
				    if (!isset($column['blocks'])) continue;

				    foreach($column['blocks'] as $block_index => $block ) {
					    if (isset($block['settings'])) {
						    foreach ($block['settings'] as $index => $setting) {
							    $name = str_replace('zrdn_','',$setting['name']);
							    Util::update_option($name, $setting['value']);
						    }
					    }
				    }
			    }
            } else {
		        //reset to empty custom when empty
			    $data = array();
            }

            update_option('zrdn_recipe_blocks_layout', $data);
	    }

	    if (!$error){
		    $msg = __("Successfully saved the template","zip-recipes");
	    }

	    $data = array(
		    'success' => !$error,
		    'msg' => $msg,
	    );
	    $response = json_encode($data);
	    header("Content-Type: application/json");
	    echo $response;
	    exit;
    }



    public static function zrdn_tinymce_plugin($plugin_array)
    {
        $plugin_array['zrdn_plugin'] = plugins_url('scripts/zlrecipe_editor_popup' . self::$suffix . '.js?sver=' . ZRDN_VERSION_NUM, __FILE__);
        return $plugin_array;
    }

    public static function zrdn_register_tinymce_button($buttons)
    {
        array_push($buttons, "zrdn_buttons");
        return $buttons;
    }

	/**
	 * Template settings page
	 */

    public static function template_page(){
	    if (!current_user_can('manage_options')) return;
	    $tabs =  array(
		    'Settings' => array(
			    'title' => __('Style settings', 'zip-recipes'),
                'page' => 'zrdn-template',
		    ),
	    );
	    ?>
        <style>
            .zrdn-grid .grid-active.small.zrdn-inactive-container{
                width:320px;
            }
            .zrdn-grid .grid-active.small.zrdn-inactive-container .item-container{
                width:300px;
            }

            .zrdn-grid .grid-active.small.zrdn-inactive-container .zrdn-grid-item .sub-item-content{
                width:300px;
            }
        </style>
	    <div class="wrap" id="zip-recipes">
		    <?php //this header is a placeholder to ensure notices do not end up in the middle of our code ?>
            <h1 class="zrdn-notice-hook-element"></h1>
		    <?php Util::settings_header(apply_filters('zrdn_tabs', $tabs ), false);?>
		    <?php
		    $empty_recipe = new Recipe();
		    $empty_recipe->load_default_data();
		    $settings = Util::get_option('all');
		    $settings = apply_filters('zrdn_recipe_settings', $settings, $empty_recipe);
		    $all_blocks = ZipRecipes::all_available_blocks();
		    $field = ZipRecipes::$field;
		    foreach ($all_blocks as $index => $recipe_block ){
			    $all_blocks[$index]['template'] = self::render_recipe_block($recipe_block, $empty_recipe, $settings, true);
			    $fields = Util::get_fields( $recipe_block['type'] );
			    $settings_html = '';
			    foreach ( $fields as $fieldname => $field_args ) {
				    $field_args['disabled'] = true;
				    $settings_html .= $field->get_field_html( $field_args , $fieldname, true);
			    }
			    $all_blocks[$index]['settings'] = $settings_html;
		    }

		    $recipe_blocks_layout = ZipRecipes::get_active_template_layout( );

		    $recipe_blocks_layout = ZipRecipes::parse_layout_to_html($recipe_blocks_layout, $empty_recipe, $settings, false);
		    $block_template = file_get_contents(trailingslashit(ZRDN_PATH) . 'grid/templates/recipe-block-element.php');
		    $settings_icon_template = '<div class="zrdn-icon zrdn-block-settings dashicons dashicons-admin-generic" ></div>';

		    ?>
		    <div class="zrdn-main-container">
                <!--    Dashboard tab   -->
                <div id="dashboard" class="current">
					<?php
					$settings_html = Util::render_template("template_settings.php", false, array());
					$output = '';
					foreach ($all_blocks as $data ) {
					    $data = wp_parse_args($data, array('settings_height'=>100, 'settings' => false));
                        $settings_icon = $data['settings'] ? $settings_icon_template : '';
						$premium_class = '';
						$premium_link = '';
					    if ( !ZipRecipes::block_is_active($data) ) {
						    if (ZipRecipes::block_is_premium($data)) {
                                $premium_link
                                    = '<div class="zrdn-premium-link"><a target="_blank" class="zrdn-premium" href="https://ziprecipes.net/premium">'
                                      . __( "premium", 'zip-recipes' )
                                      . '</a></div>';
                                $premium_class = 'zrdn-premium';
                            } else {
	                            $premium_link = '<div class="zrdn-inactive-block">'.__('Inactive',"zip-recipes").'</div>';
	                            $premium_class = 'zrdn-inactive';

                            }
                        }
						$output .= str_replace(array('{type}', '{title}', '{content}', '{settings}', '{settings-icon}', '{settings_height}', '{class}'),array($data['type'], $data['title'].$premium_link, $data['template'], $data['settings'], $settings_icon, $data['settings_height'], $premium_class), $block_template );
                    }
	                $inactive = '<div class="zrdn-sub-grid zrdn-grid-inactive">'.$output.'</div>';

					$active = '';
					foreach ($recipe_blocks_layout as $index => $column ) {
						$block_html = '';
						$drag_area_class = '';

						if (isset($column['blocks'])) {
							foreach ( $column['blocks'] as $recipe_block ) {
								$recipe_block = wp_parse_args( $recipe_block,
									array(
										'settings_height' => 100,
										'settings'        => false
									) );
								$settings_icon = $recipe_block['settings']
									? $settings_icon_template : '';
								$block_html .= str_replace( array(
									'{type}',
									'{title}',
									'{content}',
									'{settings}',
									'{settings-icon}',
									'{settings_height}'
								), array(
									$recipe_block['type'],
									$recipe_block['title'],
									$recipe_block['template'],
									$recipe_block['settings'],
									$settings_icon,
									$recipe_block['settings_height']
								), $block_template );
							}
						} else {
						    $drag_area_class = 'zrdn-highlight-dragarea';
                        }
						$block_html = '<div class="zrdn-sub-grid '.$drag_area_class.' zrdn-grid-active-element zrdn-grid-'.$index.'" data-grid_index = "'.$index.'">'.$block_html.'</div>';
						$active .= str_replace(array('{type}', '{blocks}'), array($column['type'],  '<div class="zrdn-block-icon"><div class="zrdn-move zrdn-up-block zrdn-icon dashicons dashicons-arrow-up-alt2" data-direction="up"></div><div data-direction="down" class="zrdn-move zrdn-icon zrdn-down-block dashicons dashicons-arrow-down-alt2"></div><div class="zrdn-remove-block zrdn-icon dashicons dashicons-trash"></div></div>'.$block_html), $column['template'] );
					}
					$settings['blocks'] = $active;
					$active = Util::render_template("recipe.php", $empty_recipe, $settings );

					$grid_items = array(
						array(
							'title' => __("Settings", "zip-recipes"),
							'source' => 'general',
							'class' => 'small',
							'can_hide' => true,
							'content' => $settings_html,
							'controls' => '',
						),
						array(
							'title' => __("Inactive", "zip-recipes"),
							'class' => 'small zrdn-inactive-container',
							'can_hide' => true,
							'content' => $inactive,
							'controls' => '',
						),
						array(
							'title' => __("Template", "zip-recipes"),
							'source' => 'general',
							'class' => 'zrdn-active-container',
							'content' => $active,
							'can_hide' => true,
							'controls' => '<button class="button button-default zrdn-add-block" data-width="100">'.__("Add full column","zip-recipes").'</button><button class="button button-default zrdn-add-block" data-width="50">'.__("Add half column","zip-recipes").'</button>',
						),
					);
					$container = zrdn_grid_container();
					$element = zrdn_grid_element();
					$output = '';
					foreach ($grid_items as $index => $grid_item) {
						$output .= str_replace(array('{class}', '{title}', '{content}', '{index}', '{controls}'), array($grid_item['class'].' zrdn-', $grid_item['title'],  $grid_item['content'], $index, $grid_item['controls']), $element);
					}
					echo str_replace('{content}', $output, $container);
					?>
                </div>
			</div>
	    </div>
	    <?php
    }

	/**
	 * Zip Recipes general settings page
	 */
    public static function settings_page() {

        if (!current_user_can('manage_options')) return;
        do_action('zrdn_on_settings_page' );
	    $field = ZipRecipes::$field;

	    ?>
        <div class="wrap" id="zip-recipes">
	        <?php //this header is a placeholder to ensure notices do not end up in the middle of our code ?>
            <h1 class="zrdn-notice-hook-element"></h1>
            <div id="zrdn-toggle-wrap">
                <div id="zrdn-toggle-dashboard">
                    <div id="zrdn-toggle-dashboard-text">
                        <?php _e("Select which dashboard items should be displayed", "zip-recipes") ?>
                    </div>
                    <div id="zrdn-checkboxes">
                        <?php
                        $grid_items = Util::grid_items();
                        foreach ($grid_items as $index => $grid_item) {
                            $style = "";
                            if (!$grid_item['can_hide']) {
                                $style = 'style="display:none"';
                            }
                            ?>
                            <label for="zrdn-hide-panel-<?= $index ?>" <?php echo $style ?>>
                                <input class="zrdn-toggle-items" name="zrdn_toggle_data_id_<?= $index ?>" type="checkbox"
                                       id="zrdn_toggle_data_id_<?= $index ?>" value="data_id_<?= $index ?>">
                                <?= $grid_item['title'] ?>
                            </label>
                            <?php
                        }
                        ?>
                        <button id="zrdn-reset-layout" class="button button-secondary"><?php _e("Reset","zip-recipes")?></button>
                    </div>
                </div>
            </div>

            <div id="zrdn-dashboard">

                <?php Util::settings_header(apply_filters('zrdn_tabs', array()), true);?>

                <div class="zrdn-main-container">
                    <!--    Dashboard tab   -->
                    <div id="dashboard" class="tab-content current">
                        <form id="zrdn-settings" method="POST">

                        <?php
                        $grid_items = Util::grid_items();
                        $container = zrdn_grid_container();
                        $element = zrdn_grid_element();
	                    $output = '';
	                    foreach ($grid_items as $index => $grid_item) {
		                    ob_start();
	                        if ( isset( $grid_item['template' ] ) ) {
                                echo Util::render_template($grid_item['template']);
                            } else {
		                        $fields = Util::get_fields($grid_item['source']);
		                        foreach ( $fields as $fieldname => $field_args ) {
			                        $field->get_field_html( $field_args , $fieldname);
		                        }
		                        $field->save_button();

                            }
		                    $contents = ob_get_clean();
		                    $output .= str_replace(array('{class}', '{title}', '{content}', '{index}','{controls}'), array($grid_item['class'], $grid_item['title'],  $contents, $index, $grid_item['controls']), $element);
	                    }
	                    echo str_replace('{content}', $output, $container);
				        ?>
                        </form>
                    </div>

                    <?php do_action('zrdn_tab_content'); ?>

                </div>

            </div><!--dashboard close -->


        </div><!--wrap close -->
        <?php
    }

	/**
	 * Show extension tabs
	 */

    public static function extensions_tab(){
	    $element = zrdn_grid_element();
	    $templates_extensions = array(
		    array(
			    'title' => "Autumn - ".__("Compact and clean" , "zip-recipes"),
			    'color' => 'default-grey',
			    'link' => 'https://demo.ziprecipes.net/best-guacamole-ever/',
		    ),
		    array(
			    'title' => "Canada - ".__("Powerful and colorful" , "zip-recipes"),
			    'color' => 'default-grey',
			    'link' => 'https://demo.ziprecipes.net/tres-leches/',
		    ),
		    array(
			    'title' => "Cozy Orange - ".__("Warm and beautiful" , "zip-recipes"),
			    'color' => 'default-grey',
			    'link' => 'https://demo.ziprecipes.net/pumpkin-soup-recommended-fall-recipe/',
		    ),
		    array(
			    'title' => "Vanilla - ".__("A smooth looking recipe" , "zip-recipes"),
			    'color' => 'default-grey',
			    'link' => 'https://demo.ziprecipes.net/plum-cake-with-streusel/',
		    ),
		    array(
			    'title' => "Vera - ".__("Grey background for contrast" , "zip-recipes"),
			    'color' => 'default-grey',
			    'link' => 'https://demo.ziprecipes.net/mussels-with-fennel-and-chorizo/',
		    ),
		    array(
			    'title' => "Default - ".__("Simplicity sometimes is best" , "zip-recipes"),
			    'color' => 'default-grey',
			    'link' => 'https://demo.ziprecipes.net/corn-salad/',
		    ),
	    );
	    $other_extensions = array(
		    'RecipeSearch'             => array(
			    'title' => __( "Search by ingredients", "zip-recipes" ),
			    'color' => 'cmplz-blue',
			    'link' => 'https://demo.ziprecipes.net/?s=coriander',
		    ),
		    'RecipeActions'            => array(
                'title' => __( "Social sharing with BigOven, Yummly and Pinterest", "zip-recipes" ),
                'color' => 'cmplz-blue',
		        'link' => 'https://demo.ziprecipes.net/best-guacamole-ever/',
            ),
		    'VisitorRating'            => array(
			    'title' => __( "Star ratings for your recipes", "zip-recipes" ),
			    'color' => 'cmplz-blue',
			    'link' => 'https://demo.ziprecipes.net/best-guacamole-ever/',

		    ),
		    'RecipeReviews'            => array(
			    'title' => __( "Text-based reviews of your recipes", "zip-recipes" ),
			    'color' => 'cmplz-blue',
			    'link' => 'https://demo.ziprecipes.net/best-guacamole-ever/',
		    ),
		    'ImperialMetricsConverter' => array(
			    'title' => __( "Imperial - Metric converter", "zip-recipes" ),
			    'color' => 'cmplz-blue',
		    ),
		    'structured-data' => array(
			    'title' => __( "All extensions are optimized for Google's structured data", "zip-recipes" ),
			    'color' => 'default-grey',
		    ),
	    );

	    $template_content = '';
	    foreach ($templates_extensions as $templates_extension ) {
		    $template_content .= Util::render_template('extension-bulleted-item.php', false, $templates_extension);
	    }

	    $other_extensions_content = '';
	    foreach ($other_extensions as $index => $other_extension ) {
		    $other_extensions_content .= Util::render_template('extension-bulleted-item.php', false, $other_extension);
	    }

        $extensions = array(
                'general' => array(
                    'title' => __("About Extensions", "zip-recipes"),
                    'class' => 'small zrdn-about-extensions',
                    'image'     => '',
                    'link'     => 'https://demo.ziprecipes.net/corn-salad/',
                    'description' => Util::render_template('about_extensions.php'),
                ),

                'AutomaticNutrition' => array(
                    'title' => __("Automatic Nutrition", "zip-recipes"),
                    'class' => 'small',
                    'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/nutrition.jpg',
                    'link'     => 'https://demo.ziprecipes.net/tres-leches/',
                    'description' => __("Automatically generate all nutritional values of your recipe.", "zip-recipes"),
                ),

                'ServingAdjustment' => array(
	                'title' => __("Serving Adjustments", "zip-recipes"),
	                'class' => 'small',
	                'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/servingadjustments.jpg',
	                'link'     => 'https://demo.ziprecipes.net/best-guacamole-ever/',
	                'description' => __("Visitors can adjust the ingredients to the number of servings they need: it won't get easier for your visitors!", "zip-recipes"),
                ),

                'RecipeGrid2' => array(
	                'title' => __("Recipe Gallery", "zip-recipes"),
	                'class' => 'small',
	                'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/recipegrid2.jpg',
	                'link'     => 'https://demo.ziprecipes.net/recipe-gallery/',
	                'description' => __("Display your recipes in this beautiful, dynamically filterable grid gallery", "zip-recipes"),
                ),

                'CustomTemplates' => array(
	                'title' => __("Templates", "zip-recipes"),
	                'class' => 'half-height zrdn-bulleted-block',
	                'content' => '<div>'.$template_content.'</div>',
                ),

                'other-extensions' => array(
	                'title' => __("And even more!", "zip-recipes"),
	                'class' => 'half-height zrdn-bulleted-block',
	                'content' => '<div>'.$other_extensions_content.'</div>',
                ),
        );

        $extensions = apply_filters('zrdn_extensions', $extensions);
	    $output = "";
	    ?>
        <div id="extensions" class="zrdn-gridless tab-content">
            <div class="zrdn-grid">
            <?php
            foreach ($extensions as $index => $grid_item){
                if ($index === 'general') {
                    $btn_title = apply_filters('zrdn_upgrade_button', __('Get Premium', 'zip-recipes'));
                    if (strtolower($btn_title) === 'account'){
	                    $button = '<a href="https://ziprecipes.net/account" target="_blank" class="button button-primary">'.$btn_title.'</a>';
                    } else {
                        $button = '<a href="https://ziprecipes.net/premium" target="_blank" class="button button-primary">'.$btn_title.'</a>';
                    }
                } else {
                    $button = '';
	                if (isset($grid_item['link'])) $button = '<a href="'.$grid_item['link'].'" target="_blank" class="zrdn-button">'.__("See it live on our demo website", "zip-recipes").'</a>';
                }

                $grid_item['button'] = $button;
                if (isset($grid_item['content'])) {
                    $content = $grid_item['content'];
                } else {
	                $content = Util::render_template('extension-grid.php', false, $grid_item);
                }

	            $output .= str_replace( array('{class}', '{title}', '{content}', '{index}', '{controls}', 'grid-active'), array($grid_item['class'], $grid_item['title'],  $content, $index, '', ''), $element);
            }
            echo $output;
            ?>
            </div>
        </div>
        <?php
    }

	/**
     * Get status for an extension
	 * @param $extension
	 *
	 * @return string
	 */

    public static function get_extension_status($extension){
	    $is_lover = defined('ZRDN_PRODUCT_ID') && ZRDN_PRODUCT_ID === 1843;
	    $is_friend = defined('ZRDN_PRODUCT_ID') && ZRDN_PRODUCT_ID === 1851;

	    if ( $extension === 'structured-data' && ($is_lover || $is_friend) ) return 'active';

	    if (in_array($extension, self::$addons_lover) ){
		    if ( $is_lover ) {
			    if (Util::is_plugin_active($extension)) {
				    return 'active';
			    } else {
				    return 'disabled';
			    }
		    } else {
			    return 'lover';
		    }
	    }

	    return 'zrdn-hide-bullet';
    }


	/**
	 * Conditionally add a "jump to recipe button" to the post.
	 */

	public static function jump_to_recipes_button($content){

		if ( is_singular() && Util::get_option('jump_to_recipe_link') && ( strpos($content, 'zrdn-jump-to-link')!==false || strpos($content, 'amd-zlrecipe-recipe') !==false ) ) {
			$button = '<a href="#zrdn-recipe-container" class="zrdn-recipe-quick-link">'.__('Jump to recipe','zip-recipes').'</a>';
			$content =  $button.$content;
		}

		return $content;
	}

	/**
	 * Save settings
	 */

    public static function process_settings_update(){

	    if (!current_user_can('manage_options')) return;

	    if (!isset($_GET['page']) || $_GET['page'] !== 'zrdn-settings') {
	        return;
        }
	    if (!isset($_POST['zrdn_nonce']) || !wp_verify_nonce($_POST['zrdn_nonce'], 'zrdn_save')) {
	        return;
	    }

        foreach ($_POST as $key => $value){
            if (strpos($key, 'zrdn_')===FALSE) continue;
            $fieldname = str_replace('zrdn_', '', $key);
            Util::update_option($fieldname, $value);
        }

	    do_action("zrdn_after_update_options");
    }

	/**
	 * Save the settings
	 */

	public static function process_template_update(){

		if (!current_user_can('manage_options')) return;

		if (!isset($_GET['page']) || ($_GET['page'] !== 'zrdn-template' ) ) {
			return;
		}

		if (!isset($_POST['zrdn_edit_template_nonce']) || !wp_verify_nonce($_POST['zrdn_edit_template_nonce'], 'zrdn_edit_template')) {
			return;
		}

		foreach ($_POST as $key => $value){
			if (strpos($key, 'zrdn_')===FALSE) continue;
			$fieldname = str_replace('zrdn_', '', $key);
			Util::update_option($fieldname, $value);
		}

		do_action("zrdn_after_update_options");
	}


    /**
     * Basic setup of text domain and gutenberg.
     * @todo move to more logical location
     */
    public static function zrdn_recipe_install()
    {
        global $wp_version;

        // Setup gutenberg
        if (\is_plugin_active('gutenberg/gutenberg.php') || version_compare($wp_version, '5.0', '>=')) {
            /* Gutenberg block */
            require_once plugin_dir_path(__FILE__) . 'src/block.php';
        }

        /**
         * Loading translations
         */
        $pluginLangDir = plugin_basename(dirname(__FILE__)) . '/languages/';
        $globalLangDir = WP_LANG_DIR; // full path
        if (is_readable($globalLangDir)) {
            load_plugin_textdomain('zip-recipes', false, $globalLangDir);
        }
        load_plugin_textdomain('zip-recipes', false, $pluginLangDir);
    }


    /**
     * Extract Time from Raw time
	 * @param $formatted_time
	 *
	 * @return array
	 * @throws \Exception
	 */

    public static function zrdn_extract_time( $formatted_time)
    {
        if (! $formatted_time) {
            return array(
                'time_hours'=> false,
                'time_minutes'=> false
            );
        }

        $formatted_time = Util::validate_time($formatted_time);
        // In 5.0 we introduced a bug that can end up saving hours as blank which causes an exception.
        // This fixed that scenario.
        $cleaned_formatted_time = preg_replace('/^PTH/','PT0H', $formatted_time);

        try {
            $dateInterval = new \DateInterval( $cleaned_formatted_time);
        } catch (Exception $e) {
            $cleaned_formatted_time =  'PT0H0M';
            $dateInterval = new \DateInterval( $cleaned_formatted_time);
        }
        return array(
            'time_hours'=>$dateInterval->h,
            'time_minutes'=>$dateInterval->i
        );
    }


    // Inserts the recipe into the post editor
    public static function zrdn_plugin_footer()
    {
        wp_enqueue_script(
            'zrdn-admin-script', plugins_url('scripts/admin' . self::$suffix . '.js', __FILE__), array('jquery'), // deps
            false, // ver
            true // in_footer
        );
        ?>
        <style type="text/css" media="screen">
            #wp_editrecipebtns { position:absolute;display:block;z-index:999998; }
            #wp_editrecipebtn { margin-right:20px; }
            #wp_editrecipebtn,#wp_delrecipebtn { cursor:pointer; padding:12px;background:#010101; -moz-border-radius:8px;-khtml-border-radius:8px;-webkit-border-radius:8px;border-radius:8px; filter:alpha(opacity=80); -moz-opacity:0.8; -khtml-opacity: 0.8; opacity: 0.8; }
            #wp_editrecipebtn:hover,#wp_delrecipebtn:hover { background:#000; filter:alpha(opacity=100); -moz-opacity:1; -khtml-opacity: 1; opacity: 1; }
        </style>
        <script>
            var baseurl = '<?php echo site_url(); ?>';          // This variable is used by the editor plugin
            var plugindir = '<?php echo ZRDN_PLUGIN_URL; ?>';  // This variable is used by the editor plugin
        </script>
        <?php

    }

    public static function zrdn_load_admin_media()
    {
        wp_enqueue_script('jquery');

        // This will enqueue the Media Uploader script
        wp_enqueue_script('media-upload');

        wp_enqueue_media();

        wp_enqueue_script('zrdn-admin-script');
    }

	/**
     * Format an ISO8601 duration for human readibility
	 * @param $duration
	 *
	 * @return string
	 */

	public static function zrdn_format_duration($duration)
	{
		if ($duration == null) {
			return '';
		}

		$date_abbr = array(
			'y' => array('singular' => __('%d year', 'zip-recipes'), 'plural' => __('%d years', 'zip-recipes')),
			'm' => array('singular' => __('%d month', 'zip-recipes'), 'plural' => __('%d months', 'zip-recipes')),
			'd' => array('singular' => __('%d day', 'zip-recipes'), 'plural' => __('%d days', 'zip-recipes')),
			'h' => array('singular' => __('%d hour', 'zip-recipes'), 'plural' => __('%d hours', 'zip-recipes')),
			'i' => array('singular' => __('%d minute', 'zip-recipes'), 'plural' => __('%d minutes', 'zip-recipes')),
			's' => array('singular' => __('%d second', 'zip-recipes'), 'plural' => __('%d seconds', 'zip-recipes'))
		);

		$results_array = array();

		try {
			$result_object = new \DateInterval($duration);
			foreach ($date_abbr as $abbr => $name_data) {
				$current_part = '';
				if ($result_object->$abbr > 0) {
					$current_part = sprintf($name_data['singular'], $result_object->$abbr);
					if ($result_object->$abbr > 1) {
						$current_part = sprintf($name_data['plural'], $result_object->$abbr);
					}
				}

				if ($current_part) {
					array_push($results_array, $current_part);
				}
			}
		} catch (\Exception $e) {
		}

		return join(", ", $results_array);
	}



    /**
     * Add recipe info to JSON-LD metadata for AMP. Only supports one recipe per page.
     * @param $metadata Existing JSON-LD metadata.
     * @param $post Object WPPost. $post->post_content should have the content and our "shortcode" of recipe.
     *
     * @return mixed
     */
    public static function amp_format($metadata, $post)
    {
        $recipe_json_ld = array();

        // get recipe id - limitation: only 1 recipe is supported
        if (Util::has_shortcode($post->ID, $post)){
            // Find recipe
            $recipe = new Recipe(false, $post->ID);
            $recipe_json_ld = $recipe->jsonld();
        }

        $metadata['hasPart'] = $recipe_json_ld;

        return $metadata;
    }

    public static function amp_styles()
    {
        $sprite_file = plugins_url('plugins/VisitorRating/images/rating-sprite.png', __FILE__);

        if (file_exists($sprite_file)) { ?>
            .zrdn__rating__container .zrdn_star
            {
            background-image: url('<?php echo $sprite_file ?>');
            background-repeat: no-repeat;
            height: 18px;
            }
        <?php
        }
        ?>
        .zrdn-hide-print{
            display:none;
        }
        .zrdn_five
        {
        background-position-y: 2px;
        }

        .zrdn_four_half
        {
        background-position-y: -16px;
        }

        .zrdn_four
        {
        background-position-y: -35px;
        }

        .zrdn_three_half
        {
        background-position-y: -54px;
        }

        .zrdn_three
        {
        background-position-y: -75px;
        }

        .zrdn_two_half
        {
        background-position-y: -93px;
        }

        .zrdn_two
        {
        background-position-y: -111px;
        }

        .zrdn_one_half
        {
        background-position-y: -129px;
        }

        .zrdn_one
        {
        background-position-y: -150px;
        }

        .zrdn_zero
        {
        background-position-y: -168px;
        }


        #zlrecipe-title{border-bottom: 1px solid #000;     font-weight: bold; font-size: 2em;
        line-height: 1.3em; padding-bottom: 0.5em;
        font-family:Georgia, "Times New Roman", Times, serif;;
        }

        .zlrecipe-print-link {float: right;  margin-top: 5px;}
        #zrdn-recipe-container{ padding:10px}

        .zlrecipe-print-link  a { background: url(<?php echo ZRDN_PLUGIN_URL . "images/print.png"; ?> ) no-repeat scroll 0 4px transparent;
        cursor: pointer;
        padding: 0 0 0 20px;
        display: block;
        height: 20px;
        color:#b72f2f;

        }
        #zlrecipe-category, #zlrecipe-cuisine, #zlrecipe-trans_fat, #zlrecipe-cholesterol, #zlrecipe-fiber, #zlrecipe-sodium, #zlrecipe-sugar, #zlrecipe-protein, #zlrecipe-protein, #zlrecipe-carbs, #zlrecipe-saturated-fat, #zlrecipe-fat, #zlrecipe-calories, #zrdn__author, #zlrecipe-total-time, #zlrecipe-prep-time, #zlrecipe-cook-time, #zlrecipe-yield, #zlrecipe-serving-size {line-height: 1.2em;
        margin: 1em 0; font-size:14px;
        font-weight: bold;}

        #zlrecipe-category span, #zlrecipe-cuisine span, #zlrecipe-trans_fat span, #zlrecipe-cholesterol span, #zlrecipe-fiber span, #zlrecipe-sodium span, #zlrecipe-sugar span, #zlrecipe-protein span, #zlrecipe-protein span, #zlrecipe-carbs span, #zlrecipe-saturated-fat span, #zlrecipe-fat span, #zlrecipe-calories span, #zrdn__author span, #zlrecipe-total-time span, #zlrecipe-prep-time span, #zlrecipe-cook-time span, #zlrecipe-yield span, #zlrecipe-serving-size span{font-weight: normal; display:block}
        .zlmeta .width-50{ width:50%; float:left}
        #zlrecipe-summary { padding: 0 10px 10px;}
        #zlrecipe-summary .summary{ margin: 10px 0; font-style: italic; line-height: 1.2em; font-size: 16px;  font-family: 'Open Sans', sans-serif;}


        #zlrecipe-ingredients, #zlrecipe-instructions{ font-weight: bold;  font-size: 1.25em; line-height: 1.2em; margin: 1em 0; padding-bottom:1em;}
        #zlrecipe-ingredients-list, #zlrecipe-instructions-list{padding:0px;line-height: 1.2em; font-size: 1.1em;
        }
        .ingredient.no-bullet  {list-style-type: none; padding: 0 0 0 2.4em; margin: 1em;}

        #zlrecipe-instructions-list li{
        text-align:left;
        }
        #zlrecipe-instructions-list .instruction {margin: 0 1em; list-style-type: decimal;}
        #zlrecipe-instructions-list {color: #000;padding:0px; margin: 0 0 24px 0;padding-left: 10px;}
        #zlrecipe-ingredients-list{color: #000;padding-left: 10px;line-height: 1.3em;}
        #zlrecipe-ingredients-list li{padding-left: 0; text-align: left; padding-bottom:5px;margin:0px; }

        #zrdn-recipe-container .hide-card{
        display: none;
        }
        #zlrecipe-summary{
        clear: both;
        }

        #zrdn-recipe-container .h-4 { font-size: 1.25em; font-weight: bold; }

        .ziprecipes-plugin { display: none; }

        #zl-printed-copyright-statement, #zl-printed-permalink { display: none; }
        <?php
    }





    /**
     * Show Notice
     *
     * If GD or ImageMagick not installed it will show messages
     */
    public static function zrdn_check_image_editing_support()
    {
        if ((isset($_GET['page']) && $_GET['page']=='zrdn-settings')) {
            $is_exist = false;
            if (extension_loaded('gd') || extension_loaded('imagick')) {
                $is_exist = true;
            } else {
                zrdn_notice(__("For the best performance, Zip Recipes recommends ImageMagick or GD PHP extensions installed. Please contact your hosting company and request that they install ImageMagick or GD PHP extensions for you website.", "zip-recipes"), 'warning', true, true);
            }
        }
    }



    /**
     * Link a recipe to a post.
     * We will also make sure
     *  - there's no other recipe linked is to this post.
     *  - the other way,  no other post linked to this recipe is not possible because the recipe table allows only one post_id
     * @param $post_id
     * @param $recipe_id
     */

    public static function link_recipe_to_post($post_id, $recipe_id)
    {

        //do not change links for revisions
        if (get_post_type($post_id)==='revision') return;

        global $wpdb;
        $table = $wpdb->prefix . RecipeModel::TABLE_NAME;

        $sql = $wpdb->prepare("UPDATE $table
                    SET post_id = null
                    WHERE recipe_id != %s AND post_id = %s;", $recipe_id, $post_id);
        $wpdb->query($sql);

        //now save this postid to make sure it's linked
        $wpdb->update(
                $table,
                array('post_id'=>$post_id),
                array('recipe_id'=>$recipe_id)
        );

    }


    /**
     * Get All recipes by post_id
     *
     * @param $post_id
     * @return mixed
     */
    public static function zrdn_get_all_recipes_by_post_db($post_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . RecipeModel::TABLE_NAME;
        $selectStatement = $wpdb->prepare("SELECT * FROM {$table} WHERE post_id=%d", $post_id);
        return $wpdb->get_results($selectStatement);
    }

	/**
     * Insert file into media library
	 * @param $filepath
	 * @param $filename
	 *
	 * @return bool|int|\WP_Error
	 */

	public static function insert_media( $filepath, $filename) {
		include_once( ABSPATH . 'wp-admin/includes/image.php' );

		$uploads = wp_upload_dir();
		$upload_dir = $uploads['basedir'];
		$upload_url = trailingslashit($uploads['baseurl']);
		//insert the thumbnail e.g. public/event/d0/06/06ca_1cb8.png
		$year = date('Y', time());
		$month = date('n', time());
		if (!file_exists(trailingslashit($filepath).$filename)) {
			return false;
		}

		//sanitize filename
		$sanitized_filename = str_replace(" ","-",$filename);

		//check extension of the file
		$i = strrpos($sanitized_filename,".");
		//if no extension was found, we exit, but leave any imported files in place, without converting.
		if (!$i) {
			return false;
		}

		//copy the file in Wordpress structure
		$targetdir = $upload_dir."/".$year."/".$month;
		if (!file_exists($upload_dir."/".$year)) {
			mkdir($upload_dir."/".$year);
		}
		if (!file_exists($upload_dir."/".$year."/".$month)) {
			mkdir($upload_dir."/".$year."/".$month);
		}
		if (!file_exists($targetdir."/".$sanitized_filename)) {
			copy(trailingslashit($filepath)."/".$filename, $targetdir."/".$sanitized_filename);
		}

		$l = strlen($filepath) - $i;
		$ext = substr($filepath,$i+1,$l);
		$filename_no_ext = substr($sanitized_filename,0,strlen($sanitized_filename)-strlen($ext)-1);
		$filename_url = $upload_url."/".$year."/".$month."/".$sanitized_filename;
		$filename_dir = $upload_dir."/".$year."/".$month."/".$sanitized_filename;
		$filetype = wp_check_filetype(basename( $filename_dir ), null );
		$args = array(
			'guid'           => $filename_url,
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename_no_ext ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		$thumbnail_id = wp_insert_attachment( $args, $filename_dir );
		$attach_data = wp_generate_attachment_metadata( $thumbnail_id, $filename_dir );
		wp_update_attachment_metadata( $thumbnail_id, $attach_data );

		return $thumbnail_id;
	}

}
