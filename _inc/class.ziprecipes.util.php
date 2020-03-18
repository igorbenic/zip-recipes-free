<?php
namespace ZRDN;

require_once(ZRDN_PLUGIN_DIRECTORY . 'vendor/autoload.php');

class Util {
	public static $authors;

	/* Send debug code to the Javascript console */


    public static function zrdn_debug_to_console($data) {
        if (is_array($data) || is_object($data)) {
            echo("<script>console.log('PHP: " . json_encode($data) . "');</script>");
        } else {
            echo("<script>console.log('PHP: " . $data . "');</script>");
        }
    }

	public static function timeToISO8601($hours, $minutes) {
		$time = '';
		if ($hours || $minutes) {
			$time = 'P';
			if (isset($hours) || isset($minutes)) {
				$time .= 'T';
			}
			if (isset($hours)) {
				if ($minutes && $hours == '') { // if there's minutes and not hours set hours to 0 ..
					$time .= '0H';
				}
				else {
					$time .= $hours . 'H';
				}
			}
			if (isset($minutes)) {
				if ($hours && $minutes == '') { // if there's hours and but not minutes, set minutes to 0..
					$time .=  '0M';
				}
				else {
					$time .= $minutes . 'M';
				}
			}
		}
		return $time;
	}

    /**
     * Check if this setup uses Gutenberg
     * @return bool
     */
    public static function uses_gutenberg()
    {

        if (function_exists('has_block') && !class_exists('Classic_Editor')) {
            return true;
        }
        return false;
    }

    /**
     * Check if this site uses Elementor
     * When Elementor is used, the classic shortcode should be used, even when on Gutenberg
     *
     * @return bool $uses_elementor
     */

    public static function uses_elementor(){
        if (defined('ELEMENTOR_VERSION')) return true;

        return false;
    }

    /**
     *
     * get the shortcode or block for a page type
     *
     * @param string $type
     * @return string $shortcode
     *
     *
     */


    public static function get_shortcode($recipe_id)
    {
        if (!$recipe_id) return false;

        //even if on gutenberg, with elementor we have to use classic shortcodes.
        if (Util::uses_gutenberg() && !Util::uses_elementor()){
            return '<!-- wp:zip-recipes/recipe-block {"id":"'.$recipe_id.'"} /-->';
        } else {
            return '[amd-zlrecipe-recipe:'.$recipe_id.']';

        }
    }

    /**
     * Check if post contains a Gutenberg block or shortcode from Zip
     * @param $post_id
     * @param $post_data
     * @return bool
     */


    public static function has_shortcode($post_id, $post_data){
        if (!$post_data) $post_data = get_post($post_id);
        if (!$post_data) return false;

        if (strpos($post_data->post_content, 'amd-zlrecipe-recipe')!==FALSE || strpos($post_data->post_content, 'wp:zip-recipes/recipe-block')!==FALSE){
            return true;
        }
        return false;
    }


    /**
     *
     * get the shortcode or block for a page type
     *
     * @param string $type
     * @param boolean empty, to get pattern for gutenberg shortcode without recipeid
     * @return string $shortcode
     *
     *
     */


    public static function get_shortcode_pattern($recipe_id=false, $match_all=false, $force_classic=false)
    {
        //even if on gutenberg, with elementor we have to use classic shortcodes.
        $gutenberg = Util::uses_gutenberg() && !Util::uses_elementor();
        $classic = !$gutenberg;
        if ($force_classic || $classic) {
            if ($recipe_id){
                return '/(\[amd-zlrecipe-recipe:'.$recipe_id.'\])/i';
            }
            if ($match_all){
                return '/(\[amd-zlrecipe-recipe:.*?\])/i';
            }
            return '/\[amd-zlrecipe-recipe:([0-9]\d*).*?\]/i';
        } else {
            if ($recipe_id){
                return '/<!-- wp:zip-recipes\/recipe-block {"id":"'.$recipe_id.'".*?} \/-->/i';
            }
            if ($match_all){
                return '/(<!-- wp:zip-recipes\/recipe-block {.*?} \/-->)/i';
            }
            return '/<!-- wp:zip-recipes\/recipe-block {.*?"id":"([0-9]\d*)".*?} \/-->/i';
        }
    }

    /**
     * Render PHP template
     * @param string $file
     * @param array $options
     * @param string|bool $pluginDir
     * @return string $html
     */

	public static function render_template($file, $options=array(), $pluginDir=false){

	    if (!$pluginDir) {
		    $pluginDir = '';
        } else  {
            if (file_exists(ZRDN_PLUGIN_DIRECTORY."plugins/$pluginDir/")) $pluginDir = "plugins/$pluginDir/";
        }

        $viewDir = ZRDN_PLUGIN_DIRECTORY . $pluginDir . 'views/';

        $theme_file = trailingslashit(get_stylesheet_directory()) . dirname(ZRDN_PLUGIN_DIRECTORY) . $file;

        $plugin_file = $viewDir . $file;

        $file = file_exists($theme_file) ? $theme_file :$plugin_file;

        if (strpos($file, '.php') !== FALSE) {
            ob_start();
            require $file;
            $contents = ob_get_clean();
        } else {
            $contents = file_get_contents($file);
        }

        if (count($options)>0){
            foreach($options as $placeholder => $value){

                if (strpos($contents,'{/'.$placeholder.'}')!==FALSE){

                    $value = ($value==='true' || $value==1 || $value) ? true : false;

                    if (!$value){
                        //remove the entire string
                        $contents = preg_replace('/{'.$placeholder.'}.*?{\/'.$placeholder.'}/s', '', $contents);
                    } else {
                        //only remove the placeholders
                        $contents = str_replace(array('{'.$placeholder.'}','{/'.$placeholder.'}'),'', $contents);
                    }
                } else {
                    $contents = str_replace('{'.$placeholder.'}', $value, $contents);
                }
            }
        }


        return $contents;
    }

    /**
     * Get the number of recipes on this site
     * @return int
     */

    public static function count_recipes($args=array()){
        $default_args = array(
            'search' =>'',
        );
        $args = wp_parse_args($args, $default_args);

        global $wpdb;
        $search_sql = '';
        if (strlen($args['search'])>0){
            $search_sql = $wpdb->prepare(" AND recipe_title like %s", $args['search']);
        }
        $table = $wpdb->prefix . "amd_zlrecipe_recipes";
        $count = $wpdb->get_var("SELECT count(*) FROM $table WHERE 1=1 $search_sql ");
        return intval($count);
    }

	public static function iso8601toHoursMinutes($time) {
		try {
			if ($time) {
				$date = new \DateInterval($time);
				$minutes = $date->i;
				$hours = $date->h;
			}

			return array($hours, $minutes);
		} catch (\Exception $e) {
			return null;
		}
	}

    /**
     * validate a time string, make sure a valid string is returned.
     * @param $time_str
     * @return string
     */


    public static function validate_time($time_str){
        $pattern="/PT[0-9]{1,2}H[0-9]{1,2}M/i";

        if (preg_match($pattern, $time_str, $matches) === 1 && $matches[0] === $time_str) {
            return $time_str;
        }

        return 'PT0H0M';
    }

	/**
	 * Render view and echo it.
	 * @param       $name name of html view to be found in views/ directory. Doesn't contain .html extension.
	 * @param array $args object View context parameters.
	 *
	 * @return string Rendered view.
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
    public static function _view($name, $args = array()) {
        $trace = debug_backtrace();
        $caller = $trace[2]; // 0 here is direct caller of _view, 1 would be our Util class so we want 2

        $plugin_name = "";
        if (isset($caller['class'])) {
            $classComponents = explode("\\", $caller['class']);
            $class = $classComponents[count($classComponents) - 1];
            $plugin_name = $class;
        }

        $pluginDir = "";
        // don't consider core class a plugin
        if ($plugin_name && $plugin_name !== "ZipRecipes") { // TODO: ZipRecipes is hardcoded and needs to change
            $pluginDir = "plugins/$plugin_name/";
        }

        $viewDir = ZRDN_PLUGIN_DIRECTORY . $pluginDir . 'views/';
        $file = $name . '.twig';

        $uploads = wp_upload_dir();
        $uploads_dir = trailingslashit($uploads['basedir']);

        if (!file_exists($uploads_dir . 'zip-recipes/')){
            mkdir($uploads_dir . 'zip-recipes/');
        }

        if (!file_exists($uploads_dir . 'zip-recipes/cache/')) {
            mkdir($uploads_dir . 'zip-recipes/cache/');
        }

        $cacheDir = false;
        if (is_writable($uploads_dir . 'zip-recipes/cache')) {
            $cacheDir = $uploads_dir . 'zip-recipes/cache';
        }

        //fallback own plugin directory
        if (!$cacheDir) {
            if (is_writable($viewDir) || chmod($viewDir, 0660)) {
                $cacheDir = "${viewDir}cache";
            }
        }

        Util::log("Looking for template in dir:" . $viewDir);
        Util::log("Template name:" . $file);

        $loader = new \Twig_Loader_Filesystem(array($viewDir, ZRDN_PLUGIN_DIRECTORY . 'views/'));

        $twig_settings = array(
            'autoescape' => true,
            'auto_reload' => true
        );
        //if ($cacheDir) $twig_settings['cache'] = $cacheDir;

        $twig = new \Twig_Environment($loader, $twig_settings);

        $twig->addFunction( '__', new \Twig_SimpleFunction( '__', function ( $text ) {
            return __( $text, 'zip-recipes' );
        } ) );
        return $twig->render($file, $args);
    }

    public static function print_view($name, $args = array()) {
        echo self::_view($name, $args);
    }

    public static function view($name, $args = array()) {
        return self::_view($name, $args);
    }


    public static function get_charset_collate() {
        global $wpdb;

        $charset_collate = '';

        if (!empty($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if (!empty($wpdb->collate))
            $charset_collate .= " COLLATE $wpdb->collate";

        return $charset_collate;
    }

    // Get value of an array key
    // Used to suppress warnings if key doesn't exist
    public static function get_array_value($key, $array) {
        if (isset($array[$key])) {
            return $array[$key];
        }

        return null;
    }

    /**
     * Get list of installed plugins as a string. Each plugin is separated with ;
     */
    public static function zrdn_get_installed_plugins() {
        $pluginsString = '';
        $plugins = get_plugins();
        foreach ($plugins as $path => $pluginData) {
            // if you update the delimiter here, ensure the api.ziprecipes.net changes as well
            $pluginsString .= $pluginData['Name'] . "|";
        }

        return $pluginsString;
    }

	/**
	 * Get list of grid items
	 * @return array
	 */

    public static function grid_items(){
	    $grid_items = array(
		    array(
			    'title' => __("General", " zip-recipes"),
			    'source' => 'general',
			    'class' => '',
			    'can_hide' => true,
		    ),

		    array(
			    'title' => __("Nutrition", " zip-recipes"),
			    'source' => "nutrition",
			    'class' => 'small',
			    'can_hide' => true,
		    ),

		    array(
			    'title' => __("Image", " zip-recipes"),
			    'source' => "image",
			    'class' => 'small',
			    'can_hide' => true,
		    ),

		    array(
			    'title' => __("Print settings", " zip-recipes"),
			    'source' => "print",
			    'class' => '',
			    'can_hide' => true,
		    ),

		    array(
			    'title' => __("Social", " zip-recipes"),
			    'source' => "social",
			    'class' => 'small',
			    'can_hide' => true,
		    ),

		    array(
			    'title' => __("Authors", " zip-recipes"),
			    'source' => "authors",
			    'class' => 'small',
			    'can_hide' => true,
		    ),

		    array(
			    'title' => __("Hide labels", " zip-recipes"),
			    'source' => "labels",
			    'class' => '',
			    'can_hide' => true,
		    ),

		    array(
			    'title' => __("Add-ons", " zip-recipes"),
			    'source' => "plugins",
			    'class' => 'small',
			    'can_hide' => true,
		    ),

		    array(
			    'title' => __("Advanced", " zip-recipes"),
			    'source' => "advanced",
			    'class' => 'small',
			    'can_hide' => true,
		    ),
	    );
	    return $grid_items;
    }

	/**
	 * Wrapper function to return a list of authors
	 * @return array
	 */

    public static function get_authors(){
        $authors = apply_filters('zrdn_authors',array());
        return $authors;
    }

	/**
	 * Get array of settings fields
	 * @param bool $type
	 * @param bool $plugins_only
	 *
	 * @return array
	 */
	public static function get_fields( $type = false, $plugins_only = false ) {
		$result = array();

		$fields = array(
			'template' => array(
				'type'      => 'select',
				'source'    => 'general',
				'options'   => array(
					'default'               => __( "default", "zip-recipes" ),
					'_template_autumn'      => __( "Autumn", "zip-recipes" ),
					'_template_canada'      => __( "Canada", "zip-recipes" ),
					'_template_cozy_orange' => __( "Cozy Orange",
						"zip-recipes" ),
					'_template_vanilla'     => __( "Vanilla", "zip-recipes" ),
					'_template_vera'        => __( "Vera", "zip-recipes" ),
				),
				'disabled'  => array(
					'_template_autumn',
					'_template_canada',
					'_template_cozy_orange',
					'_template_vanilla',
					'_template_vera',
				),
				'default'   => 'default',
				'table'     => false,
				'label'     => __( 'Recipe template', 'zip-recipes' ),
				'comment'   => sprintf(__("To get more templates, check out %spremium%s", "zip-recipes"), '<a target="_blank" href="https://ziprecipes.net/premium">', '</a>'),

			),

			'hide_attribution' => array(
				'type'               => 'checkbox',
				'source'             => 'general',
				'table'              => false,
				'label'              => __( "Hide attribution", 'zip-recipes' ),
				'callback_condition' => 'zrdn_is_free',
			),

			'border_style' => array(
				'type'      => 'select',
				'source'    => 'general',
				'default'    => '1px dotted',
				'options'   => array(
					'0px'        => __( 'No border', "zip-recipes" ),
					'1px solid'       => __( '1px solid', "zip-recipes" ),
					'1px dotted'      => __( '1px dotted', "zip-recipes" ),
					'1px dashed'      => __( '1px dashed', "zip-recipes" ),
					'2px solid' => __( '2px solid', "zip-recipes" ),
					'double'      => __( 'double', "zip-recipes" ),
				),
				'table'     => false,
				'label'     => __( 'Style of border around recipe', 'zip-recipes' ),
				'condition' => array(
					'template' => 'default',
				),
			),

			'ingredients_list_type' => array(
				'type'      => 'select',
				'source'    => 'general',
				'options'   => array(
					'l'   => __( 'List', 'zip-recipes' ),
					'ol'  => __( 'Numbered List', 'zip-recipes' ),
					'ul'  => __( 'Bulleted List', 'zip-recipes' ),
					'p'   => __( 'Paragraphs', 'zip-recipes' ),
					'div' => __( 'Divs', 'zip-recipes' ),
				),
				'table'     => false,
				'label'     => __( 'Ingredients List Type', 'zip-recipes' ),
				'default'   => 'l',
			),

			'instructions_list_type' => array(
				'type'      => 'select',
				'source'    => 'general',
				'options'   => array(
					'l'   => __( 'List', 'zip-recipes' ),
					'ol'  => __( 'Numbered List', 'zip-recipes' ),
					'ul'  => __( 'Bulleted List', 'zip-recipes' ),
					'p'   => __( 'Paragraphs', 'zip-recipes' ),
					'div' => __( 'Divs', 'zip-recipes' ),
				),
				'table'     => false,
				'label'     => __( 'Instructions List Type', 'zip-recipes' ),
				'default'   => 'l',

			),

			'copyright_statement' => array(
				'type'      => 'text',
				'source'    => 'general',
				'table'     => false,
				'label'     => __( "Copyright statement", 'zip-recipes' ),
			),

			'hide_title' => array(
				'type'      => 'checkbox',
				'source'    => 'general',
				'table'     => false,
				'label'     => __( "Hide recipe title", 'zip-recipes' ),
				'help'      => __( 'Hide Recipe Title in post (still shows in print view)', 'zip-recipes' ),
				'condition' => array(
					'template' => 'default',
				),
			),

			'hide_image' => array(
				'type'      => 'checkbox',
				'source'    => 'image',
				'table'     => false,
				'label'     => __( "Hide recipe image", 'zip-recipes' ),
			),

			'set_image_width' => array(
				'type'               => 'checkbox',
				'source'             => 'image',
				'table'              => false,
				'label'              => __( "Set image width", 'zip-recipes' ),
				'condition' => array(
					'hide_image' => false,
				),
			),

			'image_width' => array(
				'type'               => 'number',
				'source'             => 'image',
				'table'              => false,
				'default'              => '',
				'label'              => __( "Image Width", 'zip-recipes' ),
				'help'              => __( "Set the image width in pixels", 'zip-recipes' ),
				'condition' => array(
					'set_image_width' => true,
				),
			),

			'hide_on_duplicate_image' => array(
				'type'               => 'checkbox',
				'source'             => 'image',
				'table'              => false,
				'label'              => __( "Hide recipe image when post image is the same", 'zip-recipes' ),
				'help'              => __( "When enabled, the recipe image will be hidden if it's the same as the image in the post", 'zip-recipes' ),
				'condition' => array(
					'hide_image' => false,
				),
			),

			'hide_print_link' => array(
				'type'      => 'checkbox',
				'source'    => 'print',
				'table'     => false,
				'label'     => __( "Hide Print Button", 'zip-recipes' ),
			),

			'hide_print_image' => array(
				'type'      => 'checkbox',
				'source'    => 'print',
				'table'     => false,
				'label'     => __( "Hide Image in print view", 'zip-recipes' ),
				'default'   => true,
				'condition' => array(
					'hide_print_link' => false,
				),
			),

			'hide_permalink' => array(
				'type'      => 'checkbox',
				'source'    => 'print',
				'table'     => false,
				'label'     => __( "Hide link to recipe in print view", 'zip-recipes' ),
				'help'     => __( "The link is a direct link to the recipe, at the bottom of your recipe printout", 'zip-recipes' ),
			),

			'hide_print_nutrition_label' => array(
				'type'      => 'checkbox',
				'source'    => 'print',
				'table'     => false,
				'label'     => __( 'Hide nutrition label in print view',
					'zip-recipes' ),
				'condition' => array(
					'hide_print_link' => false,
				),
			),

			'print_image' => array(
				'type'                  => 'upload',
				'source'                => 'print',
				'low_resolution_notice' => __( "Image resolution too low, or image size not generated",
					"zip-recipes" ),
				'size'                  => 'zrdn_custom_print_image',
				'table'                 => false,
				'condition'             => array(
					'template' => 'default',
				),
				'label'                 => __( 'Custom Print Button', 'zip-recipes' ),
			),

			'Authors' => array(
				'type'      => 'checkbox',
				'source'    => 'authors',
				'is_plugin' => true,
				'disabled'  => true,
				'table'     => false,
				'label'     => __( 'Enable author field', 'zip-recipes' ),
				'comment' => sprintf(__("The author field is a %spremium%s feature", "zip-recipes"), '<a target="_blank" href="https://ziprecipes.net/premium">', '</a>'),
			),

			'use_custom_authors' => array(
				'type'               => 'checkbox',
				'source'             => 'authors',
				'disabled'           => true,
				'table'              => false,
				'label'              => __( "Use custom authors", 'zip-recipes' ),
				'help'              => __( "By default, Zip Recipes uses WordPress authors. You can use your own, custom authors as well.", 'zip-recipes' ),
				'condition'         => array(
					'Authors' => true,
				),
			),

			'default_author' => array(
				'type'               => 'select',
				'source'             => 'authors',
				'options'            => ZipRecipes::$authors,
				'table'              => false,
				'disabled'           => true,
				'label'              => __( "Select a default author", 'zip-recipes' ),
				'condition'         => array(
					'use_custom_authors' => true,
				),
			),

			'custom_authors' => array(
				'type'               => 'authors',
				'source'             => 'authors',
				'table'              => false,
				'disabled'              => true,
				'label'              => __( "Manage authors", 'zip-recipes' ),
				'help'              => __( "Add and remove your authors", 'zip-recipes' ),
				'default'            => array(),
				'condition'         => array(
					'use_custom_authors' => true,
				),
			),

			'hide_ingredient_label' => array(
				'type'      => 'checkbox',
				'source'    => 'labels',
				'table'     => false,
				'label'     => __( "Hide ingredient label", 'zip-recipes' ),
			),

			'hide_instructions_label' => array(
				'type'      => 'checkbox',
				'source'    => 'labels',
				'table'     => false,
				'label'     => __( "Hide instructions label", 'zip-recipes' ),
			),

			'hide_notes_label' => array(
				'type'      => 'checkbox',
				'source'    => 'labels',
				'table'     => false,
				'label'     => __( 'Hide notes label', 'zip-recipes' ),
			),

			'hide_prep_time_label' => array(
				'type'      => 'checkbox',
				'source'    => 'labels',
				'table'     => false,
				'label'     => __( 'Hide prep time label', 'zip-recipes' ),
			),

			'hide_cook_time_label' => array(
				'type'      => 'checkbox',
				'source'    => 'labels',
				'table'     => false,
				'label'     => __( 'Hide cook time label', 'zip-recipes' ),
			),

			'hide_total_time_label' => array(
				'type'      => 'checkbox',
				'source'    => 'labels',
				'table'     => false,
				'label'     => __( 'Hide total time label', 'zip-recipes' ),
			),

			'hide_yield_label' => array(
				'type'      => 'checkbox',
				'source'    => 'labels',
				'table'     => false,
				'label'     => __( 'Hide yield label', 'zip-recipes' ),
			),

			'hide_serving_size_label' => array(
				'type'      => 'checkbox',
				'source'    => 'labels',
				'table'     => false,
				'label'     => __( 'Hide serving size label', 'zip-recipes' ),
			),

			'hide_category_label' => array(
				'type'      => 'checkbox',
				'source'    => 'labels',
				'table'     => false,
				'label'     => __( 'Hide category label', 'zip-recipes' ),
			),

			'hide_cuisine_label' => array(
				'type'      => 'checkbox',
				'source'    => 'labels',
				'table'     => false,
				'label'     => __( 'Hide cuisine label', 'zip-recipes' ),
			),

			'AutomaticNutrition' => array(
				'type'      => 'checkbox',
				'source'    => 'nutrition',
				'is_plugin' => true,
				'disabled'  => true,
				'table'     => false,
				'comment'   => sprintf(__("To automatically generate nutrition data, check out %spremium%s", "zip-recipes"), '<a target="_blank" href="https://ziprecipes.net/automatic-nutrition-for-your-recipes/">', '</a>'),
				'label'     => __( 'Enable the Automatic Nutrition generator', 'zip-recipes' ),
			),

			'show_textual_nutrition_information' => array(
				'type'      => 'checkbox',
				'source'    => 'nutrition',
				'table'     => false,
				'label'     => __( "Show nutritional values in text on your recipe",
					'zip-recipes' ),
			),

			'hide_text_nutrition_labels' => array(
				'type'      => 'checkbox',
				'source'    => 'nutrition',
				'table'     => false,
				'label'     => __( 'Hide labels for text nutrition info', 'zip-recipes' ),
				'condition' => array(
					'show_textual_nutrition_information' => true,
				),
			),

			'hide_nutrition_label' => array(
				'type'      => 'checkbox',
				'source'    => 'nutrition',
				'table'     => false,
				'label'     => __( 'Hide the nutrition information label', 'zip-recipes' ),
			),

			'nutrition_label_type' => array(
				'type'      => 'select',
				'source'    => 'nutrition',
				'options'   => array(
					'html'  => __( "HTML format", "zip-recipes" ),
					'image' => __( "Image", "zip-recipes" ),
				),
				'disabled'  => array(
					'image',
				),
				'default'   => 'image',
				'table'     => false,
				'label'     => __( 'Choose nutrition label display method', 'zip-recipes' ),
				'help'      => __( 'You can choose if you want to show the label in html format, which can be understood better by search engines, or in image format', 'zip-recipes' ),
				'condition' => array(
					'hide_nutrition_label' => false,
				)
			),

			'RecipeActions' => array(
				'type'      => 'checkbox',
				'source'    => 'social',
				'is_plugin' => true,
				'disabled'  => true,
				'table'     => false,
				'label'     => __( 'Social Recipe sharing', 'zip-recipes' ),
				'comment'   => sprintf(__("Check out %spremium%s to see our recipe sharing features", "zip-recipes"), '<a target="_blank" href="https://ziprecipes.net/premium">', '</a>'),

			),

			'recipe_action_yummly' => array(
				'type'      => 'checkbox',
				'source'    => 'social',
				'disabled'    => 'true',
				'table'     => false,
				'label'     => sprintf( __( 'Add %s sharing button',
					'zip-recipes' ), 'Yummly' ),
				'comment'   => sprintf( __( 'By enabling %s you agree to %sthe terms%s',
					'zip-recipes' ), 'Yummly',
					'<a target="_blank" href="https://www.yummly.com/toolterms" target="_blank">',
					'</a>' ),

			),

			'recipe_action_bigoven' => array(
				'type'      => 'checkbox',
				'source'    => 'social',
				'disabled'    => 'true',

				'table'     => false,
				'label'     => sprintf( __( 'Add %s sharing button',
					'zip-recipes' ), 'BigOven' ),
				'comment'   => sprintf( __( 'By enabling %s you agree to %sthe terms%s',
					'zip-recipes' ), 'BigOven',
					'<a target="_blank" href="https://www.bigoven.com/site/terms" target="_blank">',
					'</a>' ),

			),

			'recipe_action_pinterest' => array(
				'type'      => 'checkbox',
				'source'    => 'social',
				'table'     => false,
				'disabled'    => 'true',
				'label'     => sprintf( __( 'Add %s sharing button',
					'zip-recipes' ), 'Pinterest' ),
				'comment'   => sprintf( __( 'By enabling %s you agree to %sthe terms%s',
					'zip-recipes' ), 'Pinterest',
					'<a target="_blank" href="policy.pinterest.com/en/terms-of-service" target="_blank">',
					'</a>' ),

			),

			'ImperialMetricsConverter' => array(
				'type'      => 'checkbox',
				'source'    => 'plugins',
				'is_plugin' => true,
				'disabled'  => true,
				'table'     => false,
				'label'     => __( 'Imperial Metrics Converter',
					'zip-recipes' ),

			),

			'RecipeGrid' => array(
				'type'      => 'checkbox',
				'source'    => 'plugins',
				'is_plugin' => true,
				'disabled'  => true,
				'table'     => false,
				'label'     => __( 'Legacy Recipe Grid', 'zip-recipes' ),
				'condition' => array(
					'RecipeGrid2' => false,
				)
			),

			'RecipeGrid2' => array(
				'type'      => 'checkbox',
				'source'    => 'plugins',
				'is_plugin' => true,
				'disabled'  => true,
				'table'     => false,
				'label'     => __( 'Recipe Gallery', 'zip-recipes' ),

			),

			'VisitorRating' => array(
				'type'      => 'checkbox',
				'source'    => 'plugins',
				'is_plugin' => true,
				'disabled'  => true,
				'default'     => true,
				'table'     => false,
				'condition' => array(
					'RecipeReviews' => false,
				),
				'label'     => __( 'Visitor Rating', 'zip-recipes' ),

			),

			'RecipeReviews' => array(
				'type'      => 'checkbox',
				'source'    => 'plugins',
				'is_plugin' => true,
				'disabled'  => true,
				'table'     => false,
				'condition' => array(
					'VisitorRating' => false,
				),
				'label'     => __( 'Recipe Reviews', 'zip-recipes' ),

			),

			'Import' => array(
				'type'      => 'checkbox',
				'source'    => 'plugins',
				'is_plugin' => true,
				'disabled'  => true,
				'table'     => false,
				'label'     => __( 'Import', 'zip-recipes' ),

			),

			'RecipeSearch' => array(
				'type'      => 'checkbox',
				'source'    => 'plugins',
				'is_plugin' => true,
				'disabled'  => true,
				'table'     => false,
				'label'     => __( 'Recipe Search', 'zip-recipes' ),
			),

			'ServingAdjustment' => array(
				'type'      => 'checkbox',
				'source'    => 'plugins',
				'is_plugin' => true,
				'disabled'  => true,
				'table'     => false,
				'label'     => __( 'Automatic Serving Adjustment',
					'zip-recipes' ),

			),

//			'use_custom_css' => array(
//				'type'      => 'checkbox',
//				'source'    => 'advanced',
//				'table'     => false,
//				'label'     => __( "Use custom CSS",
//					'zip-recipes' ),
//			),
//
//			'custom_css' => array(
//				'type'      => 'css',
//				'source'    => 'advanced',
//				'table'     => false,
//				'label'     => __( "Custom CSS",
//					'zip-recipes' ),
//				'condition' => array(
//					'use_custom_css' => true,
//				),
//			),

			'use_zip_css' => array(
				'type'      => 'checkbox',
				'source'    => 'advanced',
				'default'    => true,
				'table'     => false,
				'label'     => __( "Use Zip Recipes style",
					'zip-recipes' ),
			),


		);

		if ( $type ) {
			foreach ( $fields as $fieldname => $field ) {
				if ( $field['source'] === $type ) {
					$result[ $fieldname ] = $field;
				}
			}
		} else if ($plugins_only){
			foreach ( $fields as $fieldname => $field ) {
				if ( isset($field['is_plugin']) && $field['is_plugin'] ) {
					$result[ $fieldname ] = $field;
				}
			}
		} else {
			$result = $fields;
		}

	    return apply_filters("zrdn_get_fields", $result, $type);
    }

	/**
	 * Get list of active plugins
	 * @return array
	 */
    public static function get_active_plugins(){
    	$fields = self::get_fields(false, $plugins = true);
	    $active = array();
	    foreach ( $fields as $fieldname => $field ) {
		    if (Util::get_option($fieldname)){
		    	$active[] = $fieldname;
		    }
	    }
	    $active[] = 'Licensing';
	    $active[] = 'CustomTemplates';
	    return apply_filters('zrdn_active_plugins' ,$active);
    }

	/**
	 * Check if plugin is active
	 * @param string $plugin
	 * @return bool
	 */
	public static function is_plugin_active($plugin){
		$fields = self::get_fields(false, $plugins = true);
		if ($plugin === 'CustomTemplates') return true;

		if (isset($fields[$plugin])){
			return Util::get_option($plugin);
		}

		return false;
	}

	/**
	 * Get the value for a ZRDN field
	 * @param string $name
	 * @param bool $label
	 *
	 * @return bool|mixed
	 */

    public static function get_option($fieldname){

	    $fields = Util::get_fields();
	    if(isset($fields[$fieldname])) {
		    $field_config = $fields[$fieldname];
	    } else {
	    	return false;
	    }
	    $source = $field_config['source'];
	    $default = isset($field_config['default']) ? $field_config['default'] : false;
	    $zrdn_settings = get_option("zrdn_settings_$source");

	    if (!isset($zrdn_settings[$fieldname])) {
	    	$value = $default;
	    } else if (!is_array($zrdn_settings[$fieldname]) && strlen($zrdn_settings[$fieldname]) === 0) {
		    $value = $default;
	    } else if (is_array($zrdn_settings[$fieldname]) && empty($zrdn_settings[$fieldname]) ) {
		    $value = $default;
	    } else {
		    $value = $zrdn_settings[$fieldname];
	    }

	    $value = apply_filters("zrdn_get_option", $value, $fieldname );
	    return $value;
    }

	/**
	 * Update option
	 * @param $fieldname
	 * @param $value
	 *
	 * @return bool
	 */

	public static function update_option($fieldname, $new_value){
		$fields = Util::get_fields();
		if(isset($fields[$fieldname]['type'])) {
			$source = $fields[$fieldname]['source'];
		} else {
			return false;
		}

		$zrdn_settings = get_option("zrdn_settings_$source");
		$field = ZipRecipes::$field;
		$old_value = isset($zrdn_settings[$fieldname]) ? $zrdn_settings[$fieldname] : false;
		$zrdn_settings[$fieldname] = apply_filters('zrdn_update_option', $field::sanitize($fieldname, $new_value), $old_value, $fieldname, $source);

		update_option("zrdn_settings_$source", $zrdn_settings);
	}

    /**
     * Get all recipes
     * @param array $args
     * @return array $recipes
     */

    public static function get_recipes($args=array()){
        $default_args = array(
            'post_id'=>false,
            'offset' => 0,
            'number' => 20,
            'order_by' => 'recipe_title',
            'search' =>'',
            'searchFields' => 'title',
            'orderby' => 'recipe_title',
            'order' => 'ASC',
            'post_status' => 'all',
            'category' => 'all',
        );
        $args = wp_parse_args($args, $default_args);
        $pagesize = intval($args['number']);
        $offset = $args['offset'];
        $orderby = $args['orderby'];
        $order = $args['order'];
        global $wpdb;
        $search_sql = '';
        if (strlen($args['search'])>0){
            if ($args['searchFields']==='all'){
                $fields = array(
                    'recipe_title',
                    'ingredients',
                    'instructions',
                    'summary',
                    'notes',
                    'cuisine',
                );
            } else {
                $fields = array('recipe_title');
            }
            $search = sanitize_text_field($args['search']);
            $search_sql = " AND (".implode(" like '%$search%' OR ", $fields)." like '%$search%')";
        }

        $offset = $args['number']!=-1 ? $offset = "LIMIT $offset, $pagesize" : '';

        $table = $wpdb->prefix . "amd_zlrecipe_recipes";
        if ($args['category']!=='all') {
            //get by category slug
            $term = get_category_by_slug( sanitize_title($args['category']) );
            $category_id = $term ? $term->term_id : false;            //if not found, default back to all
            if (!$category_id) $args['category'] = 'all';
        }

        if ($args['category']==='all') {
            if ($args['post_status'] === 'publish') {
                $sql = "SELECT * FROM $table INNER JOIN $wpdb->posts ON $table.post_id = $wpdb->posts.ID where $wpdb->posts.post_status='publish'";
            } else {
                $sql = "SELECT * FROM $table where 1=1 ";
            }
        } else {
            $sql = $wpdb->prepare("select * from $table INNER JOIN (select $wpdb->posts.* from $wpdb->posts inner join (select $wpdb->term_taxonomy.term_taxonomy_id, $wpdb->term_relationships.object_id from $wpdb->term_relationships inner join $wpdb->term_taxonomy on $wpdb->term_relationships.term_taxonomy_id=$wpdb->term_taxonomy.term_taxonomy_id where  $wpdb->term_taxonomy.taxonomy='category') as cats ON $wpdb->posts.ID = cats.object_id where cats.term_taxonomy_id = %s) as p ON $table.post_id = p.ID where 1=1 ", $category_id);
            if ($args['post_status'] === 'publish') {
                $sql .= " AND p.post_status='publish'";
            }
        }
        $recipes = $wpdb->get_results("$sql $search_sql ORDER BY $orderby $order $offset ");
        return $recipes;
    }

    public static function get_recipe_categories(){

        $recipes  = self::get_recipes();
        $categories = array();
        foreach ($recipes as $recipe){
            if (!empty($recipe->post_id)){
                $categories += wp_get_post_categories($recipe->post_id);
            }
        }

        return $categories;
    }

    /**
     * Log messages if WP_DEBUG is set.
     * @param $message String Message to log.
     */
    public static function log($message) {
        if (!WP_DEBUG) {
            return;
        }

        $trace = debug_backtrace();

        $traceIndex = 1;
        $caller = $trace[$traceIndex];

        $output = "";

        do {
            $className = array_key_exists('class', $caller) ? $caller['class'] : "";
            $functionName = array_key_exists('function', $caller) ? $caller['function'] : "";
            $file = array_key_exists('file', $caller) ? $caller['file'] : "";
            $lineNumber = array_key_exists('line', $caller) ? $caller['line'] : "";

            $prefix = $traceIndex === 1 ? "ZRDN: " : "";
            $message = $traceIndex === 1 ? ": $message" : "";

            $output .= str_repeat("\t", $traceIndex - 1) . "$prefix$className $functionName" . $message . "\n";
            if ($file && $lineNumber) {
                $output .= str_repeat("\t", $traceIndex) . " from $file:$lineNumber" . "\n";
            }

            if (array_key_exists(++$traceIndex, $trace)) {
                $caller = $trace[$traceIndex];
            } else {
                $caller = null;
            }
        } while ($caller);

    }

}

/**
 * @param $key
 * @param $arr
 * @param bool $keys_are_objects If array keys are objects. Default: false.
 */
function array_by_key($key, $arr, $keys_are_objects=false) {
	return array_reduce(
		$arr,
		function ($carry, $recipe) use ($keys_are_objects, $key) {
			if ($keys_are_objects) {
				$needle =  $recipe->{ $key };
			}
			else {
				$needle = $recipe[ $key ];
			}

			if (array_key_exists($needle, $carry)) {
				$carry[ $needle ][ ] = $recipe;
			}
			else {
				$carry[ $needle ] = array($recipe);
			}

			return $carry;
		},
		array()
	);
}
