<?php
namespace ZRDN;

if ( ! defined( 'ABSPATH' ) ) exit;

use ZRDN\Recipe as RecipeModel;
class ZipRecipes {

    const MAIN_CSS_SCRIPT = "zrdn-recipes";
    const MAIN_PRINT_SCRIPT = "zrdn-print-js";

    public static $suffix = '';
    public static $field;
    public static $authors;
	public static $addons_friend = array(
		'Authors',
		'RecipeActions',
		'Import',
		'RecipeReviews',
		'VisitorRating',
		'RecipeGrid2',
		'RecipeGrid',
		'CustomTemplates',
	);
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
		'RecipeGrid',
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
	    add_action('plugins_loaded', __NAMESPACE__ . '\ZipRecipes::process_settings_update', 10);
	    add_action('zrdn_tab_content', __NAMESPACE__ . '\ZipRecipes::extensions_tab');
	    add_action('plugins_loaded', __NAMESPACE__ . '\ZipRecipes::load_plugins', 20);
        // Init shortcode so shortcodes can be used by any plugins
        $shortcodes = new __shortcode();

        // We need to call `zrdn__init_hooks` action before `init_hooks()` because some actions/filters registered
        //	in `init_hooks()` get called before plugins have a chance to register their hooks with `zrdn__init_hooks`
	    add_action('admin_head', __NAMESPACE__ . '\ZipRecipes::zrdn_js_vars');
	    add_action('admin_init', __NAMESPACE__ . '\ZipRecipes::zrdn_add_recipe_button');
	    add_action( 'admin_bar_menu', __NAMESPACE__ . '\ZipRecipes::add_top_bar_edit_button', 90 );


	    // `the_post` has no action/filter added on purpose. It doesn't work as well as `the_content`.
	    // We're using priority of 11 here because in some cases VisualComposer seems to be running
	    //  a hook after us and adding <br /> and <p> tags
	    add_filter('the_content', __NAMESPACE__ . '\ZipRecipes::zrdn_convert_to_full_recipe', 11);
	    add_action('admin_menu', __NAMESPACE__ . '\ZipRecipes::menu_pages');
	    add_action('admin_enqueue_scripts', __NAMESPACE__ . '\ZipRecipes::enqueue_admin_assets');
	    add_action('admin_footer', __NAMESPACE__ . '\ZipRecipes::zrdn_plugin_footer');
	    add_filter('amp_post_template_metadata', __NAMESPACE__ . '\ZipRecipes::amp_format', 10, 2);
	    add_action('amp_post_template_css', __NAMESPACE__ . '\ZipRecipes::amp_styles');
	    // check GD or imagick support
	    add_action('admin_notices', __NAMESPACE__ . '\ZipRecipes::zrdn_check_image_editing_support');
	    add_action('the_content', __NAMESPACE__ . '\ZipRecipes::jump_to_recipes_button');
	    // This shouldn't be called directly because it can cause issues with WP not having loaded properly yet.
	    // One issue we were seeing was a client was getting an error caused by
	    //  `require_once( ABSPATH . 'wp-admin/includes/upgrade.php' )` in zrdn_recipe_install()
	    // This was the issue:
	    // PHP Fatal error: Call to undefined function get_user_by() in wp/wp-includes/meta.php on line 1308
	    add_action('init', __NAMESPACE__ . '\ZipRecipes::zrdn_recipe_install');

	    add_action('init',__NAMESPACE__ . '\ZipRecipes::register_images');

	    add_action('zrdn__enqueue_recipe_styles',__NAMESPACE__ . '\ZipRecipes::load_assets', 10);


	    $plugin = ZRDN_PLUGIN_BASENAME;
	    add_filter( "plugin_action_links_$plugin",
		    __NAMESPACE__ . '\ZipRecipes::plugin_settings_link'  );
	    //multisite
	    add_filter( "network_admin_plugin_action_links_$plugin",
		    __NAMESPACE__ . '\ZipRecipes::plugin_settings_link' );

	    add_filter("zrdn_update_option", __NAMESPACE__ . '\ZipRecipes::maybe_reset_nutrition_import' , 10, 4);

    }

	public static function maybe_reset_nutrition_import($new, $old, $fieldname, $source) {

		if ( $source == 'nutrition' && $fieldname == 'import_nutrition_data_all_recipes' && $new !== $old ) {
			update_option("zrdn_nutrition_data_import_completed", false);
		}
		return $new;
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

    public static function load_plugins(){
	    // Instantiate plugin classes
	    $parentPath = dirname(__FILE__);
	    $pluginsPath = "$parentPath/plugins";
	    $active_plugins = Util::get_active_plugins();

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



    public static function register_images(){
        if ( function_exists( 'add_image_size' ) ) {
            add_image_size( 'zrdn_recipe_image',   800,  500, true);
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
	    if (strpos($hook, "zrdn-settings")===false) return;

	    wp_register_style('zrdn-admin-styles',
		    trailingslashit(ZRDN_PLUGIN_URL) . "admin/css/style.css", "",
		    ZRDN_VERSION_NUM);
	    wp_enqueue_style('zrdn-admin-styles');

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
        if (Util::get_option('use_zip_css')) {
            wp_register_style(self::MAIN_CSS_SCRIPT, plugins_url('styles/zlrecipe-std' . self::$suffix . '.css', __FILE__), array(), ZRDN_VERSION_NUM, 'all');
            wp_enqueue_style(self::MAIN_CSS_SCRIPT);
        }
        wp_register_script(self::MAIN_PRINT_SCRIPT, plugins_url('scripts/zlrecipe_print' . self::$suffix . '.js', __FILE__), array('jquery'), ZRDN_VERSION_NUM, true);
        wp_enqueue_script(self::MAIN_PRINT_SCRIPT);
        $stylesheet = apply_filters('zrdn_print_style_url', ZRDN_PLUGIN_DIRECTORY_URL.'styles/zlrecipe-print.css?v='.ZRDN_VERSION_NUM);
	    wp_localize_script(
	            self::MAIN_PRINT_SCRIPT,
            'zrdn_print_styles',
            array('stylesheet_url' => $stylesheet)
        );


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
        $nested_ingredients = self::get_nested_items($recipe->ingredients);
        $nested_instructions = self::get_nested_items($recipe->instructions);

        $summary_rich = self::zrdn_break('<p class="summary italic">', self::zrdn_richify_item(self::zrdn_format_image($recipe->summary), 'summary'), '</p>');
        $formatted_notes = self::zrdn_break('<p class="notes">', self::zrdn_richify_item(self::zrdn_format_image($recipe->notes), 'notes'), '</p>');

        $amp_on = false;
        if (function_exists('is_amp_endpoint')) {
            $amp_on = is_amp_endpoint();
        }

	    /*
	     * jsonld is google's preferred method, microdata will only be used when json fails
         */
        $jsonld_attempt = json_encode(self::jsonld($recipe));
        $jsonld = '';
        if ($jsonld_attempt !== false || $recipe->non_food) {
            $jsonld = $jsonld_attempt;
            $schema_type = 'jsonld';
        } else {
            $schema_type = 'microdata';
            error_log("Error encoding recipe to JSON:" . json_last_error());
        }
        $recipe_id = $recipe->is_placeholder ? false: $recipe->recipe_id;
        $image_attributes = self::zrdn_get_responsive_image_attributes($recipe->recipe_image, $recipe_id);

        $embed = (strpos($recipe->video_url, '_value')!==FALSE) ? $recipe->video_url : wp_oembed_get($recipe->video_url);

        $viewParams = array(
            'ZRDN_PLUGIN_URL' => ZRDN_PLUGIN_URL,
            'permalink' => get_permalink(),
            'border_style' => Util::get_option('border_style'),
            'recipe_id' => $recipe->recipe_id,
            'custom_print_image' => Util::get_option('print_image'),
            'print_hide' => Util::get_option('hide_print_link'),
            'title_hide' => Util::get_option('hide_title'),
            'recipe_title' => $recipe->recipe_title,
            'ajax_url' => admin_url('admin-ajax.php'),
            'recipe_rating' => apply_filters('zrdn__ratings', '', $recipe->recipe_id, $recipe->post_id),
            'prep_time' => self::zrdn_format_duration($recipe->prep_time),
            'prep_time_raw' => $recipe->prep_time,
            'prep_time_label_hide' => Util::get_option('hide_prep_time_label'),
            'cook_time' => self::zrdn_format_duration($recipe->cook_time),
            'cook_time_raw' => $recipe->cook_time,
            'cook_time_label_hide' => Util::get_option('hide_cook_time_label'),
            'total_time' => self::zrdn_format_duration($recipe->total_time),
            'total_time_raw' => $recipe->total_time,
            'total_time_label_hide' => Util::get_option('hide_total_time_label'),
            'yield' => $recipe->yield,
            'yield_label_hide' => Util::get_option('hide_yield_label'),
            'show_nutritional_info' => Util::get_option('hide_nutrition_label') ? false : $recipe->has_nutrition_data,
            'show_nutritional_info_as_text' => Util::get_option('nutrition_info_use_text', false),
            'serving_size' => $recipe->serving_size,
            'serving_size_label_hide' => Util::get_option('hide_serving_size_label'),
            'calories' => $recipe->calories,
            'calories_label_hide' => Util::get_option('hide_all_nutrition_labels'),
            'fat' => $recipe->fat,
            'fat_label_hide' => Util::get_option('hide_all_nutrition_labels'),
            'saturated_fat' => $recipe->saturated_fat,
            'saturated_fat_label_hide' => Util::get_option('hide_all_nutrition_labels'),
            'carbs' => $recipe->carbs,
            'carbs_label_hide' => Util::get_option('hide_all_nutrition_labels'),
            'protein' => $recipe->protein,
            'protein_label_hide' => Util::get_option('hide_all_nutrition_labels'),
            'fiber' => $recipe->fiber,
            'fiber_label_hide' => Util::get_option('hide_all_nutrition_labels'),
            'sugar' => $recipe->sugar,
            'sugar_label_hide' => Util::get_option('hide_all_nutrition_labels'),
            'sodium' => $recipe->sodium,
            'sodium_label_hide' => Util::get_option('hide_all_nutrition_labels'),
            'summary' => $recipe->summary,
            'summary_rich' => $summary_rich,
            'image_attributes' => $image_attributes,
            'is_featured_post_image' => $recipe->is_featured_post_image,
            'image_width' => Util::get_option('image_width'),
            'image_hide' => Util::get_option('hide_image'),
            'image_hide_print' => Util::get_option('hide_print_image'),
            'ingredient_label_hide' => Util::get_option('hide_ingredients_label'),
            'ingredient_list_type' => Util::get_option('ingredients_list_type'),
            'nested_ingredients' => $nested_ingredients,
            'instruction_label_hide' => Util::get_option('hide_instructions_label'),
            'instruction_list_type' => Util::get_option('instructions_list_type'),
            'nested_instructions' => $nested_instructions,
            'notes' => $recipe->notes,
            'formatted_notes' => $formatted_notes,
            'notes_label_hide' => Util::get_option('hide_notes_label'),
            'attribution_hide' => Util::get_option('hide_attribution'),
            'trans_fat' => $recipe->trans_fat,
            'trans_fat_label_hide' => Util::get_option('hide_all_nutrition_labels'),
            'cholesterol' => $recipe->cholesterol,
            'cholesterol_label_hide' => Util::get_option('hide_all_nutrition_labels'),
            'category' => $recipe->category,
            'category_label_hide' => Util::get_option('hide_category_label'),
            'cuisine' => $recipe->cuisine,
            'cuisine_label_hide' => Util::get_option('hide_cuisine_label'),
            'version' => ZRDN_VERSION_NUM,
            'print_permalink_hide' => Util::get_option('hide_permalink'),
            'copyright' => Util::get_option('copyright_statement'),
            // author_section is used in default theme
            'author_section' => apply_filters('zrdn__authors_render_author_for_recipe', '', $recipe->recipe_id),
            // author is used in other themes
            'author' => $recipe->author,
            // The second argument to apply_filters is what is returned if no one implements this hook.
            // For `nutrition_label`, we want an empty string, not $recipe object.
            'nutrition_label' => apply_filters('zrdn__nutrition_get_label', '', $recipe),
            'amp_on' => $amp_on,
            'jsonld' => $jsonld,
            'recipe_actions' => apply_filters('zrdn__recipe_actions', ''),
            'schema_type' => $schema_type,
            'video_embed' =>  $embed,
            'metricsImperial' => apply_filters('zrdn_metricsimperial', false),
        );
        do_action('zrdn__enqueue_recipe_styles');

        $custom_template = apply_filters('zrdn__custom_templates_get_formatted_recipe', false, $viewParams);
        $output = $custom_template ?: Util::view('recipe', $viewParams);

	    if (!self::is_rest() && !is_admin() && !is_singular() && Util::get_option('show_summary_on_archive_pages')) {
	        $output = $recipe->summary;
	    }

	    $output = apply_filters('zrdn_recipe_content', $output, $recipe->recipe_id);
	    $output = do_shortcode($output);

	    return $output;
    }

    public static function is_rest(){
	    return ( defined( 'REST_REQUEST' ) && REST_REQUEST );
    }

    /**
     * Get formatted items (ingredients or instructions) in a nested array (see return).
     * @param $items string Unformatted string of ingredients or instructions.
     *
     * @return array|bool Nested array with formatted items for each sublist. E.g.:
     *  [
     *      ["4 skinless, boneless chicken breast halves", "1 1/2 tablespoons vegetable oil"]
     *      ["subtitle for second part", "4g of onions", "5g of beans"]
     * ]
     */
    private static function get_nested_items($items)
    {
        $nested_list = array();
        if (!$items) {
            return false;
        }

        $raw_items = explode("\n", $items);
        foreach ($raw_items as $raw_item) {
            // don't add items that are empty
            if (strlen(trim($raw_item)) < 1) {
                continue;
            }
            $number_of_sublists = count($nested_list);
            $item_array = self::zrdn_format_item($raw_item);
            // if last item is an array
            if ($number_of_sublists > 0 && is_array($nested_list[$number_of_sublists - 1])) {
                $subtitle = self::get_subtitle($raw_item);
                if ($subtitle) {
                    array_push($nested_list, array($item_array));
                } else {
                    array_push($nested_list[count($nested_list) - 1], $item_array);
                }
            } else {
                array_push($nested_list, array($item_array));
            }
        }

        return $nested_list;
    }


    /**
     *  Return subtitle for item.
     * @param string $item //Raw ingredients/instructions item
     * @return string
     */
    private static function get_subtitle($item)
    {
        preg_match("/^!(.*)/", $item, $matches);

        $title = "";
        if (count($matches) > 1) {
            $title = $matches[1];
        }

        return $title;
    }

    /**
     * Processes markup for attributes like labels, images and links.
     * Changed behaviour in 4.5.2.7:
     *  - links (like [margarine|http://margarine.com] no longer include an
     *    'ingredient', 'ingredient-link', 'no-bullet', 'no-bullet-link' classes or a combination thereof
     *  - images (like %http://example.com/logo.png no longer include an
     *    'ingredient', 'ingredient-image', 'no-bullet', 'no-bullet-image' classes or a combination thereof
     *  - ids are no longer added
     * Syntax:
     * !Label
     * %image
     * [link|http://example.com/link]
     * @param string $item
     *
     * @return array {
     * @type string $type
     * @type string $content
     * }
     */
    public static function zrdn_format_item($item)
    {
        $formatted_item = $item;
        if (preg_match("/^%(\S*)/", $item, $matches)) { // IMAGE Updated to only pull non-whitespace after some blogs were adding additional returns to the output
            // type: image
            // content: $matches[1]
            $attributes = self::zrdn_get_responsive_image_attributes($matches[1]);
            return array('type' => 'image', 'content' => $matches[1], 'attributes' => $attributes); // Images don't also have labels or links so return the line immediately.
        }

        $retArray = array();
        $subtitle = self::get_subtitle($item);
        if ($subtitle) { // subtitle
            // type: subtitle
            // content: formatted $item
            $formatted_item = $subtitle;
            $retArray['type'] = 'subtitle';
        } else {
            // type: default
            // content: formatted $item
            $retArray['type'] = 'default';
        }

        $retArray['content'] = self::zrdn_richify_item($formatted_item);

        return $retArray;
    }

	/**
	 * Conditionally add a "jump to recipe button" to the post.
	 */

	public static function jump_to_recipes_button($content){

		if ( is_singular() && Util::get_option('jump_to_recipe_link') && strpos($content, 'zrdn-jump-to-link')!==false ) {
		    //get recipe based on post id
            global $post;
            if ($post) {
                //create link to recipe
                $script =
                    '<script>
                        jQuery(document).ready(function ($) {
                            
                            $(document).on("click", ".zrdn-recipe-quick-link", function(){
                                $("html, body").animate({
                                    scrollTop: $(".zrdn-jump-to-link").offset().top - 75
                                }, 2000);
                            });
                        });
                    </script>';
                $button = $script.'<a href="#" class="zrdn-recipe-quick-link">'.__('Jump to recipe').'</a>';
                $content =  $button.$content;
            }
		}

		return $content;
	}

    // Adds module to left sidebar in wp-admin for ZLRecipe
    public static function menu_pages()
    {
        // Add the top-level admin menu
        $page_title = __('Zip Recipes Settings','zip-recipes');
        $capability = 'manage_options';
        $menu_slug = 'zrdn-settings';
        $function = __NAMESPACE__ . '\ZipRecipes::settings_page';

        add_menu_page(
            $page_title,
	        'Zip Recipes',
            $capability,
            $menu_slug,
            $function,
            ZRDN_PLUGIN_URL . 'images/zip-recipes-icon-16.png',
            apply_filters('zrdn_menu_position', 50)
        );

        $settings_title = __("Settings", "zip-recipes");
        add_submenu_page(
            $menu_slug, // parent_slug
            $page_title, // page_title
            $settings_title, // menu_title
            $capability, // capability
            $menu_slug, // menu_slug
            $function // callback function
        );

        do_action("zrdn__menu_page", array(
            "capability" => $capability,
            "parent_slug" => $menu_slug,
        ));
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

    // Adds 'Settings' page to the ZipRecipe module
    public static function settings_page() {

        if (!current_user_can('manage_options')) return;
        do_action('zrdn_on_settings_page' );
	    $field = ZipRecipes::$field;

	    $tabs = apply_filters('zrdn_tabs', array(
            'dashboard' => array(
                    'title' => __('Dashboard', 'zip-recipes'),
            ),
            'extensions' => array(
                'title' => __('Extensions', 'zip-recipes'),
            ),
        ));

	    ?>
        <div class="wrap" id="zip-recipes">
            <div id="zrdn-toggle-wrap">
                <div id="zrdn-toggle-dashboard">
                    <div id="zrdn-toggle-dashboard-text">
                        <?php _e("Select which dashboard items should be displayed", " zip-recipes") ?>
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
                                <input class="zrdn-toggle-items" name="toggle_data_id_<?= $index ?>" type="checkbox"
                                       id="toggle_data_id_<?= $index ?>" value="data_id_<?= $index ?>">
                                <?= $grid_item['title'] ?>
                            </label>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div id="zrdn-dashboard">

                <!--    Navigation-->
                <div class="zrdn-settings-container">
                    <ul class="tabs">
                        <div class="tabs-content">
                            <img class="zrdn-settings-logo" src="<?= trailingslashit( ZRDN_PLUGIN_URL ) . "images/logo.png"?>" alt='Zip Recipes'>
                            <div class="header-links">
                                <div class="tab-links">
                                    <?php
                                    $first = true;
                                    foreach ($tabs as $tab => $data) {
                                        if (isset($data['cap']) && current_user_can( $data['cap'] ) ) continue;
                                        $current = $first ? 'current' : '';
                                        $first = false;
                                        ?>
                                        <li class="tab-link <?=$current?>"
                                            data-tab="<?=$tab?>"><a
                                                    class="tab-text tab-<?=$tab?>"
                                                    href="#<?=$tab?>#top"><?=$data['title']?></a>
                                        </li>
                                    <?php }?>
                                </div>
                                <div class="documentation-pro">
                                    <div class="documentation">
                                        <a target="_blank" href="https://ziprecipes.net/knowledge-base-overview/"><?php _e( "Documentation",
									            " zip-recipes" ); ?></a>
                                    </div>
                                    <div id="zrdn-toggle-options">
                                        <div id="zrdn-toggle-link-wrap">
                                            <button type="button"
                                                    id="zrdn-show-toggles"
                                                    class="button button button-upsell"
                                                    aria-controls="screen-options-wrap"><?php _e( "Display options",
										            " zip-recipes" ); ?>
                                                <span id="zrdn-toggle-arrows"
                                                      class="dashicons dashicons-arrow-down-alt2"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </ul>
                </div>

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
		                    $fields = Util::get_fields($grid_item['source']);
		                    ob_start();

		                    foreach ( $fields as $fieldname => $field_args ) {
			                    $field->get_field_html( $field_args , $fieldname);
		                    }

		                    $field->save_button();
		                    $contents = ob_get_clean();
		                    $output .= str_replace(array('{class}', '{title}', '{content}', '{index}'), array($grid_item['class'], $grid_item['title'],  $contents, $index), $element);
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

    public static function extensions_tab(){
	    $element = zrdn_grid_element();
        $extensions = array(
                'general' => array(
                    'title' => __("Premium extensions", "zip-recipes"),
                    'class' => 'small',
                    'content' => 'content',
                    'image'     => '',
                    'link'     => 'https://demo.ziprecipes.net/corn-salad/',
                    'description' => __("Get the full benefits of Zip Recipes by upgrading to a premium plan, with lots of great add-ons. Even better, each add-on we will add to the plan in the future will automatically become available for you.", "zip-recipes"),
                ),
                'AutomaticNutrition' => array(
                    'title' => __("Automatic Nutrition", "zip-recipes"),
                    'class' => 'small',
                    'content' => 'content',
                    'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/nutrition.jpg',
                    'link'     => 'https://demo.ziprecipes.net/tres-leches/',
                    'description' => __("Automatically generate all nutritional values of your recipe.", "zip-recipes"),
                ),

                'RecipeGrid2' => array(
	                'title' => __("Recipe Gallery", "zip-recipes"),
	                'class' => 'small',
	                'content' => 'content',
	                'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/recipegrid2.jpg',
	                'link'     => 'https://demo.ziprecipes.net/recipe-gallery/',
	                'description' => __("Display your recipes in this beautiful, dynamically filterable grid gallery", "zip-recipes"),
                ),
                'RecipeActions' => array(
	                'title' => __("Social Recipe sharing", "zip-recipes"),
	                'class' => 'small',
	                'content' => 'content',
	                'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/socialsharing.png',
	                'link'     => 'https://demo.ziprecipes.net/pumpkin-soup-recommended-fall-recipe/',
	                'description' => __("Let your visitors share your recipes on the social networks, like Yummly, Bigoven and Pinterest", "zip-recipes"),
                ),
                'VisitorRating' => array(
	                'title' => __("Ratings and reviews", "zip-recipes"),
	                'class' => 'small',
	                'content' => 'content',
	                'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/ratings.png',
	                'link'     => 'https://demo.ziprecipes.net/tres-leches/',
	                'description' => __("Let your visitors rate your recipes anonymously with recipe ratings, or enter entire written reviews in a comment like fashion.", "zip-recipes"),
                ),

                'ServingAdjustment' => array(
	                'title' => __("Serving Adjustments", "zip-recipes"),
	                'class' => 'small',
	                'content' => 'content',
	                'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/servingadjustments.gif',
	                'link'     => 'https://demo.ziprecipes.net/best-guacamole-ever/',
	                'description' => __("Visitors can adjust the ingredients to the number of servings they need: it won't get easier for your visitors!", "zip-recipes"),
                ),

                'MostPopularRecipes' => array(
	                'title' => __("Most Popular Recipes", "zip-recipes"),
	                'class' => 'small',
	                'content' => 'content',
	                'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/mostpopular.png',
	                'link'     => 'https://demo.ziprecipes.net/corn-salad/',
	                'description' => __("Show a widget with the most popular recipes.", "zip-recipes"),
                ),
                'CustomTemplates' => array(
	                'title' => __("Premium templates", "zip-recipes"),
	                'class' => 'small',
	                'content' => 'content',
	                'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/logo.png',
	                'link'     => 'https://demo.ziprecipes.net/',
	                'description' => __("Get several beautifully designed templates for your recipes.", "zip-recipes"),
                ),
                'Authors' => array(
	                'title' => __("Authors", "zip-recipes"),
	                'class' => 'small',
	                'content' => 'content',
	                'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/logo.png',
	                'link'     => 'https://demo.ziprecipes.net/corn-salad/',
	                'description' => __("Automatically add your post author schema.org compatible to your recipe. Or define your own custom authors.", "zip-recipes"),
                ),
                'ImperialMetricsConverter' => array(
	                'title' => __("Imperial - Metric", "zip-recipes"),
	                'class' => 'small',
	                'content' => 'content',
	                'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/logo.png',
	                'link'     => 'https://demo.ziprecipes.net/pumpkin-soup-recommended-fall-recipe/',
	                'description' => __("Get more designs for your recipes, and keep receiving the latest new template designs from Zip Recipes", "zip-recipes"),
                ),
                'RecipeSearch' => array(
	                'title' => __("Recipe Search", "zip-recipes"),
	                'class' => 'small',
	                'content' => 'content',
	                'image'     => trailingslashit(ZRDN_PLUGIN_URL) . 'images/logo.png',
	                'link'     => 'https://demo.ziprecipes.net/corn-salad/',
	                'description' => __("Let your visitors search by ingredients from the default WordPress search.", "zip-recipes"),
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
                    if ($btn_title === ''){
	                    $button = '';
                    } else {
                        $button = '<a href="https://ziprecipes.net/premium" target="_blank" class="button button-primary">'.$btn_title.'</a>';
                    }
                } else {
	                $button = '<a href="'.$grid_item['link'].'" target="_blank" class="zrdn-button">'.__("See it live on our demo website", "zip-recipes").'</a>';

	                if (Util::is_plugin_active($index)) {
		                $grid_item['title'] .= '<button class="zrdn-extension-label active">'.__('active', 'zip-recipes').'</button>';
	                } else {
	                    if (in_array($index, self::$addons_lover)){
		                    $grid_item['title'] .= '<button class="zrdn-extension-label lover">lover</button>';
	                    }
		                if (in_array($index, self::$addons_friend)){
			                $grid_item['title'] .= '<button class="zrdn-extension-label friend">friend</button>';

	                    }
	                }
                }

                $grid_item['button'] = $button;
	            $content = Util::render_template('extension-grid.php', $grid_item);
	            $output .= str_replace(array('{class}', '{title}', '{content}', '{index}', 'grid-active'), array($grid_item['class'], $grid_item['title'],  $content, $index, ''), $element);

            }
            echo $output;
            ?>
            </div>
        </div>
        <?php
    }

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


    // Replaces the [a|b] pattern with text a that links to b
    // Replaces _words_ with an italic span and *words* with a bold span
    public static function zrdn_richify_item($item)
    {
        $output = preg_replace('/\[([^\]\|\[]*)\|([^\]\|\[]*)\]/', '<a href="\\2" target="_blank">\\1</a>', $item);
        $output = preg_replace('/(^|\s)\*([^\s\*][^\*]*[^\s\*]|[^\s\*])\*(\W|$)/', '\\1<span class="bold">\\2</span>\\3', $output);
        return preg_replace('/(^|\s)_([^\s_][^_]*[^\s_]|[^\s_])_(\W|$)/', '\\1<span class="italic">\\2</span>\\3', $output);
    }

    public static function zrdn_strip_chars($val)
    {
        return str_replace('\\', '', $val);
    }

    /**
     * Run the install method when plugin is updated.
     * This hook is called when any plugins are updated, so we need to ensure that Zip Recipes is updated
     *   before the install method is called.
     * @param $upgrader {Plugin_Upgrader}
     * @param $data {array} Contains "type", "action", "plugins".
     */
    public static function plugin_updated($upgrader, $data)
    {
        Util::log("In plugin_updated");

        // if this plugin is being updated, call zrdn_recipe_install method
        if (is_array($data) && $data['action'] === 'update' && $data['type'] === 'plugin' &&
            is_array($data['plugins']) &&
            in_array(ZRDN_PLUGIN_BASENAME, $data['plugins'])
        ) {
            //self::init();
        }
    }

    /**
     * Creates ZLRecipe tables in the db if they don't exist already.
     * Don't do any data initialization in this routine as it is called on both install as well as
     * every plugin load as an upgrade check.
     *
     * Updates the table if needed
     *
     * Plugin Ver       DB Ver
     * 1.0 - 1.3        3.0
     * 1.4x - 2.6       3.1  Adds Notes column to recipes table
     * 4.1.0.10 -       3.2  Adds primary key, collation
     * 4.2.0.20 -       3.3  Added carbs, protein, fiber, sugar, saturated fat, and sodium
     */
    public static function zrdn_recipe_install()
    {
        global $wp_version;

        Util::log("In zrdn_recipe_install");


        // Setup gutenberg
        if (\is_plugin_active('gutenberg/gutenberg.php') || version_compare($wp_version, '5.0', '>=')) {
            /* Gutenberg block */
            //if (cmplz_uses_gutenberg()) {
            require_once plugin_dir_path(__FILE__) . 'src/block.php';
            //}
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


    // Inserts the recipe into the database

    /**
     * @param $post_info
     *
     * @return mixed
     */
    public static function zrdn_insert_db($post_info)
    {
        $recipe_id = Util::get_array_value("recipe_id", $post_info);

        $prep_time = Util::timeToISO8601(
            Util::get_array_value("prep_time_hours", $post_info),
            Util::get_array_value("prep_time_minutes", $post_info)
        );


        $cook_time = Util::timeToISO8601(
            Util::get_array_value("cook_time_hours", $post_info),
            Util::get_array_value("cook_time_minutes", $post_info)
        );

        // Build array to be sent to db query call
        $clean_fields = array(
            'recipe_title',
            'recipe_image',
            'summary',
            'yield',
            'serving_size',
            'calories',
            'fat',
            'carbs',
            'protein',
            'fiber',
            'sugar',
            'saturated_fat',
            'sodium',
            'ingredients',
            'instructions',
            'notes',
            'author',
            'category',
            'cuisine',
            'trans_fat',
            'cholesterol',
            'serving_size',
            'is_featured_post_image'
            //'nutrition_label'
        );

        // zrdn__recipe_field_names recipe db fields that don't need special processing or formatting
        $clean_fields = apply_filters('zrdn__recipe_field_names', $clean_fields);

        $recipe = array();
        foreach ($post_info as $attr => $value) {
            if (in_array($attr, $clean_fields) && isset($post_info[$attr])) {
                $recipe[$attr] = $value;
            }
        }
        // Add fields that needed format change
        $recipe['prep_time'] = $prep_time;
        $recipe['cook_time'] = $cook_time;


        if (RecipeModel::db_select($recipe_id) == null) {
            $recipe["post_id"] = Util::get_array_value("recipe_post_id", $post_info); // set only during record creation
            $recipe_id = RecipeModel::db_insert($recipe);
        } else {
            RecipeModel::db_update($recipe, array('recipe_id' => $recipe_id));
        }


        do_action('zrdn__recipe_post_save', $recipe_id, $post_info);

        return $recipe_id;
    }





    /**
     * Extract Time from Raw time
     *
     * @param string $formatted_time
     *
     * @return array time
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

    // Pulls a recipe from the db
    public static function zrdn_select_recipe_db($recipe_id)
    {
        global $wpdb;

        $selectStatement = sprintf("SELECT * FROM `%s%s` WHERE recipe_id=%d", $wpdb->prefix, RecipeModel::TABLE_NAME, $recipe_id);
        $recipe = $wpdb->get_row($selectStatement);

        return $recipe;
    }

    public static function zrdn_break($otag, $text, $ctag)
    {
        $output = "";
        $split_string = explode("\r\n\r\n", $text, 10);
        foreach ($split_string as $str) {
            $output .= $otag . $str . $ctag;
        }
        return $output;
    }

    // Format an ISO8601 duration for human readibility
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

    // Inserts the recipe into the post editor
    public static function zrdn_plugin_footer()
    {
        wp_enqueue_script(
            'zrdn-admin-script', plugins_url('scripts/admin' . self::$suffix . '.js', __FILE__), array('jquery'), // deps
            false, // ver
            true // in_footer
        );

        Util::print_view('footer', array('url' => site_url(),
            'pluginurl' => ZRDN_PLUGIN_URL));
    }

    public static function zrdn_load_admin_media()
    {
        wp_enqueue_script('jquery');

        // This will enqueue the Media Uploader script
        wp_enqueue_script('media-upload');

        wp_enqueue_media();

        wp_enqueue_script('zrdn-admin-script');
    }

    public static function jsonld($recipe)
    {
        //if it's not a food item, return empty
        if ($recipe->non_food) return '';

        $formattedIngredientsArray = array();
        foreach (explode("\n", $recipe->ingredients) as $item) {
            $itemArray = self::zrdn_format_item($item);
            $formattedIngredientsArray[] = $itemArray['content'];
        }

        $formattedInstructionsArray = array();
        foreach (explode("\n", $recipe->instructions) as $item) {
            $itemArray = self::zrdn_format_item($item);
            $formattedInstructionsArray[] = $itemArray['content'];
        }

        $keywords= false;
        $tags =wp_get_post_tags( $recipe->post_id );
        if ($tags){
            $tags= wp_list_pluck($tags,'name');
            $keywords = implode(',',$tags);
        }
        
        $recipe_json_ld = array(
            "@context" => "http://schema.org",
            "@type" => "Recipe",
            "description" => trim(preg_replace('/\s+/', ' ', strip_tags($recipe->summary))),
            "image" => $recipe->recipe_image_json,
            "recipeIngredient" => $formattedIngredientsArray,
            "name" => $recipe->recipe_title,
            "recipeCategory" => $recipe->category,
            "recipeCuisine" => $recipe->cuisine,
            "nutrition" => array(
                "@type" => "NutritionInformation",
                "calories" => $recipe->calories,
                "fatContent" => $recipe->fat,
                "transFatContent" => $recipe->trans_fat,
                "cholesterolContent" => $recipe->cholesterol,
                "carbohydrateContent" => $recipe->carbs,
                "proteinContent" => $recipe->protein,
                "fiberContent" => $recipe->fiber,
                "sugarContent" => $recipe->sugar,
                "saturatedFatContent" => $recipe->saturated_fat,
                "sodiumContent" => $recipe->sodium
            ),
            "cookTime" => $recipe->cook_time,
            "prepTime" => $recipe->prep_time,
            "recipeInstructions" => $formattedInstructionsArray,
            "recipeYield" => $recipe->yield,
        );

        if ($keywords){
            $recipe_json_ld['keywords'] = $keywords;
        }
        if (!empty($recipe->video_url)){
            $recipe_json_ld['video'] = $recipe->video_url;
        }

        if ($recipe->total_time) {
            $recipe_json_ld["totalTime"] = $recipe->total_time;
        }

        $cleaned_recipe_json_ld = clean_jsonld($recipe_json_ld);

        $author = $recipe->author;
        if ($author) {
            $cleaned_recipe_json_ld["author"] = (object)array(
                "@type" => "Person",
                "name" => $author
            );
        }
        $rating_data = apply_filters('zrdn__ratings_format_amp', '',$recipe->recipe_id, $recipe->post_id);
        if ($rating_data && $rating_data['count']>0) {
            $itemReviewed = $recipe_json_ld;
            unset($itemReviewed['@context']);
            $rating = array(
                "bestRating" => $rating_data['max'],
                "ratingValue" => $rating_data['rating'],
//                "itemReviewed" => (object)$itemReviewed,
                "itemReviewed" => $recipe->recipe_title,
                "ratingCount" => $rating_data['count'],
                "worstRating" => $rating_data['min']
            );

            $cleaned_recipe_json_ld["aggregateRating"] = (object)$rating;
        }

        return $cleaned_recipe_json_ld;
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
            $recipe_json_ld = self::jsonld($recipe);
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
        .zip-recipes .hide-print{
            display:none;
        }
        .zip-recipes .hide-card{
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
        #zlrecipe-container-178{ padding:10px}

        .zlrecipe-print-link  a { background: url(<?php echo ZRDN_PLUGIN_URL . "images/print-icon.png"; ?> ) no-repeat scroll 0 4px transparent;
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

        #zlrecipe-container .hide-card{
        display: none;
        }
        #zlrecipe-summary{
        clear: both;
        }

        #zlrecipe-container .h-4 { font-size: 1.25em; font-weight: bold; }

        .ziprecipes-plugin { display: none; }

        #zl-printed-copyright-statement, #zl-printed-permalink { display: none; }
        <?php
    }

    /**
     * Convert Image URL to image tag
     *
     * @param String $item
     * @return String
     */
    public static function zrdn_format_image($item)
    {
        preg_match_all('/(%http|%https):\/\/[^ ]+(\.gif|\.jpg|\.jpeg|\.png)/', $item, $matches);
        if (isset($matches[0]) && !empty($matches[0])) {
            foreach ($matches[0] as $image) {
                $attributes = self::zrdn_get_responsive_image_attributes(str_replace('%', '', $image));
                $html = "<img class='' src='{$attributes['url']}";
                if (!empty($attributes['srcset'])) {
                    $html .= " srcset='{$attributes['srcset']}";
                }
                if (!empty($attributes['sizes'])) {
                    $html .= " sizes='{$attributes['sizes']}'";
                }
                if (!empty($attributes['alt'])) {
                    $html .= " alt='{$attributes['alt']}'";
                } else if (!empty($attributes['title'])){
                    $html .= " alt='{$attributes['title']}'";
                }
                $html .= ' />';
                $item = str_replace($image, $html, $item);
            }
        }

        return apply_filters('zrdn_image_html' , $item);
    }

    /**
     * Get Responsive Image attributes from URL
     *
     * It checks image is not external and return images attributes like srcset, sized etc.
     *
     * @param string $url
     * @param int|bool $recipe_id
     * @return type
     */

    public static function zrdn_get_responsive_image_attributes($url, $recipe_id=false)
    {

        /**
         * set up default array values
         */

        $attributes = array();
        $attributes['url'] = $url;
        //if a recipe_id is passed, we try to use the recipe image id
        $recipe = new Recipe($recipe_id);
        if ($recipe->recipe_image_id>0){
            $attachment_id = $recipe->recipe_image_id;
        } else {

            $attachment_id = attachment_url_to_postid($url);
            if (!$attachment_id) $attachment_id = get_post_thumbnail_id();
        }

        $attributes['attachment_id'] = $attachment_id;
        $attributes['srcset'] = '';
        $attributes['sizes'] = '';
        $attributes['title'] = '';

        if ($attachment_id) {

            $attributes['url'] = wp_get_attachment_image_url($attachment_id, 'large');
            $image_meta = wp_get_attachment_metadata($attachment_id);

            $attributes['alt'] = get_post_meta($attachment_id, '_wp_attachment_image_alt', TRUE);
            $attributes['title'] = get_the_title($attachment_id);
            $img_srcset = wp_get_attachment_image_srcset($attachment_id, 'large', $image_meta);

            $attributes['srcset'] = esc_attr($img_srcset);

            $img_sizes = wp_get_attachment_image_sizes($attachment_id, 'large');
            $attributes['sizes'] = esc_attr($img_sizes);
        }
        return apply_filters('zrdn_image_attributes', $attributes);
    }

    /**
     * Show Notice
     *
     * If GD or ImageMagick not installed it will show messages
     */
    public static function zrdn_check_image_editing_support()
    {
        $is_exist = false;
        if (extension_loaded('gd') || extension_loaded('imagick')) {
            $is_exist = true;
        } else {
            Util::log("Attempting to get responsive image: ImageMagick or GD PHP extensions not installed.");
            Util::print_view("notice");
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

}
