<?php
namespace ZRDN;

if ( ! defined( 'ABSPATH' ) ) exit;

use ZRDN\Recipe as RecipeModel;
class ZipRecipes {

    const PLUGIN_OPTION_NAME = "zrdn__plugins";
    const MAIN_CSS_SCRIPT = "zrdn-recipes";
    const MAIN_PRINT_SCRIPT = "zrdn-print-js";

    public static $registration_url;
    public static $suffix = '';
    public static $field;

    /**
     * Init function.
     */
    public static function init()
    {
        if (is_admin()) {
            self::$field = new ZRDN_Field();
        }
        Util::log("Core init");
        self::$suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
        self::$registration_url = ZRDN_API_URL . "/installation/register/";

        // Instantiate plugin classes
        $parentPath = dirname(__FILE__);
        $pluginsPath = "$parentPath/plugins";
        $pluginsDirHandle = opendir($pluginsPath);
        Util::log("Searching for plugins in:" . $pluginsPath);
        if ($pluginsDirHandle) {
            $originalDir = getcwd();
            chdir($pluginsPath);

            // loop through plugin dirs and require them
            while (false !== ($fileOrFolder = readdir($pluginsDirHandle))) {
                $notDir = !is_dir($fileOrFolder);
                $invalidDir = $fileOrFolder === "." || $fileOrFolder === ".." || $fileOrFolder === "_internal";
                // we don't care about files inside `plugins` dir
                if ($notDir || $invalidDir) {
                    continue;
                }

                // plugins classes will be in plugins/RecipeIndex/RecipeIndex.php
                $pluginName = $fileOrFolder;
                $pluginPath = "$fileOrFolder/$pluginName.php";
                Util::log("Attempting to load plugin:" . $pluginsPath);
                // Sometimes plugin folders end up non-empty and then a hard crash happens when the file is required
                if (! is_readable($pluginPath)) {
                    Util::log("Plugin couldn't be loaded. Main plugin file is missing.");
                    continue;
                }
                require_once($pluginPath);

                // instantiate class
                $namespace = __NAMESPACE__;
                $fullPluginName = "$namespace\\$pluginName"; // double \\ is needed because \ is an escape char
                $pluginInstance = new $fullPluginName;
                // add plugin to options if it's not already there
                // zrdn__plugins stores whether plugin is enabled or not:
                //	array("VisitorRating" => array("active" => false, "description" => "Stuff"),
                //				"RecipeIndex" => array("active" => true, "description" => "Stuff"))
                $pluginOptions = get_option(self::PLUGIN_OPTION_NAME, array());
                if (!array_key_exists($fullPluginName, $pluginOptions)) {
                    /**
                     * We don't want Recipe Reviews to activated by default
                     * Because Visitor rating activated by default
                     */
                    $default_activated  = ($fullPluginName === 'ZRDN\RecipeReviews') ? false: true;
                    $pluginOptions[$fullPluginName] = array("active" => $default_activated, "description" => $pluginInstance::DESCRIPTION);
                }
                update_option(self::PLUGIN_OPTION_NAME, $pluginOptions);
            }

            chdir($originalDir);
        }

        closedir($pluginsDirHandle);

        // Init shortcode so shortcodes can be used by any plugins
        $shortcodes = new __shortcode();

        // We need to call `zrdn__init_hooks` action before `init_hooks()` because some actions/filters registered
        //	in `init_hooks()` get called before plugins have a chance to register their hooks with `zrdn__init_hooks`
        do_action("zrdn__init_hooks"); // plugins can add an action to listen for this event and register their hooks

        self::init_hooks();
    }

    /**
     * Function to hook to specific WP actions and filters.
     */
    private static function init_hooks()
    {
        Util::log("I'm in init_hooks");

        add_action('admin_head', __NAMESPACE__ . '\ZipRecipes::zrdn_js_vars');
        add_action('admin_init', __NAMESPACE__ . '\ZipRecipes::zrdn_add_recipe_button');

        // `the_post` has no action/filter added on purpose. It doesn't work as well as `the_content`.
        // We're using priority of 11 here because in some cases VisualComposer seems to be running
        //  a hook after us and adding <br /> and <p> tags
        add_filter('the_content', __NAMESPACE__ . '\ZipRecipes::zrdn_convert_to_full_recipe', 11);

        add_action('admin_menu', __NAMESPACE__ . '\ZipRecipes::zrdn_menu_pages');

        add_option("amd_zlrecipe_db_version"); // used to store DB version - leaving as is name as legacy
        add_option('zrdn_attribution_hide', 'Hide');
        add_option('zlrecipe_printed_permalink_hide', '');
        add_option('zlrecipe_printed_copyright_statement', '');
        add_option('zlrecipe_stylesheet', 'zlrecipe-std');
        add_option('recipe_title_hide', '');
        add_option('zlrecipe_image_hide', '');
        add_option('zlrecipe_image_hide_print', 'Hide');
        add_option('zlrecipe_print_link_hide', '');
        add_option('zlrecipe_ingredient_label_hide', '');
        add_option('zlrecipe_ingredient_list_type', 'l');
        add_option('zlrecipe_instruction_label_hide', '');
        add_option('zlrecipe_instruction_list_type', 'ol');
        add_option('zlrecipe_notes_label_hide', '');
        add_option('zlrecipe_prep_time_label_hide', '');
        add_option('zlrecipe_cook_time_label_hide', '');
        add_option('zlrecipe_total_time_label_hide', '');
        add_option('zlrecipe_yield_label_hide', '');
        add_option('zlrecipe_serving_size_label_hide', '');
        add_option('zlrecipe_calories_label_hide', '');
        add_option('zlrecipe_fat_label_hide', '');
        add_option('zlrecipe_carbs_label_hide', '');
        add_option('zlrecipe_protein_label_hide', '');
        add_option('zlrecipe_fiber_label_hide', '');
        add_option('zlrecipe_sugar_label_hide', '');
        add_option('zlrecipe_saturated_fat_label_hide', '');
        add_option('zlrecipe_sodium_label_hide', '');

        add_option('zlrecipe_image_width', '');
        add_option('zlrecipe_outer_border_style', '');
        add_option('zlrecipe_custom_print_image', '');

        add_filter('wp_head', __NAMESPACE__ . '\ZipRecipes::zrdn_process_head');

        add_action('admin_footer', __NAMESPACE__ . '\ZipRecipes::zrdn_plugin_footer');

        add_filter('amp_post_template_metadata', __NAMESPACE__ . '\ZipRecipes::amp_format', 10, 2);
        add_action('amp_post_template_css', __NAMESPACE__ . '\ZipRecipes::amp_styles');
        // check GD or imagick support
        add_action('admin_notices', __NAMESPACE__ . '\ZipRecipes::zrdn_check_image_editing_support');
        // post save hook
        add_action('post_updated', __NAMESPACE__ . '\ZipRecipes::zrdn_post_featured_image');

        // This shouldn't be called directly because it can cause issues with WP not having loaded properly yet.
        // One issue we were seeing was a client was getting an error caused by
        //  `require_once( ABSPATH . 'wp-admin/includes/upgrade.php' )` in zrdn_recipe_install()
        // This was the issue:
        // PHP Fatal error: Call to undefined function get_user_by() in wp/wp-includes/meta.php on line 1308
        add_action('init', __NAMESPACE__ . '\ZipRecipes::zrdn_recipe_install');

        add_action('init',__NAMESPACE__ . '\ZipRecipes::register_images');

        add_action('zrdn__enqueue_recipe_styles',__NAMESPACE__ . '\ZipRecipes::load_assets');
    }


    public static function register_images(){
        if ( function_exists( 'add_image_size' ) ) {
            add_image_size( 'zrdn_recipe_image',   800,  500, true);
        }
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
        wp_register_style(self::MAIN_CSS_SCRIPT, plugins_url('styles/zlrecipe-std' . self::$suffix . '.css', __FILE__), array(), ZRDN_VERSION_NUM, 'all');
        wp_enqueue_style(self::MAIN_CSS_SCRIPT);

        wp_register_script(self::MAIN_PRINT_SCRIPT, plugins_url('scripts/zlrecipe_print' . self::$suffix . '.js', __FILE__), array('jquery'), ZRDN_VERSION_NUM, true);
        wp_enqueue_script(self::MAIN_PRINT_SCRIPT);
    }

    /**
     * Formats the recipe for output
     *
     * @param $recipe
     * @return string
     */
    public static function zrdn_format_recipe($recipe)
    {
        $nutritional_info = false;
        if (
            $recipe->yield != null ||
            $recipe->serving_size != null ||
            $recipe->calories != null ||
            $recipe->fat != null ||
            $recipe->carbs != null ||
            $recipe->protein != null ||
            $recipe->fiber != null ||
            $recipe->sugar != null ||
            $recipe->saturated_fat != null ||
            $recipe->cholesterol != null ||
            $recipe->sodium != null ||
            $recipe->trans_fat
        ) {
            $nutritional_info = true;
        }

        $nested_ingredients = self::get_nested_items($recipe->ingredients);
        $nested_instructions = self::get_nested_items($recipe->instructions);

        $summary_rich = self::zrdn_break('<p class="summary italic">', self::zrdn_richify_item(self::zrdn_format_image($recipe->summary), 'summary'), '</p>');
        $formatted_notes = self::zrdn_break('<p class="notes">', self::zrdn_richify_item(self::zrdn_format_image($recipe->notes), 'notes'), '</p>');

        $amp_on = false;
        if (function_exists('is_amp_endpoint')) {
            $amp_on = is_amp_endpoint();
        }
        $jsonld_attempt = json_encode(self::jsonld($recipe));
        $jsonld = '';
        if ($jsonld_attempt !== false) {
            $jsonld = $jsonld_attempt;
        } else {
            error_log("Error encoding recipe to JSON:" . json_last_error());
        }
        $image_attributes = self::zrdn_get_responsive_image_attributes($recipe->recipe_image);

        $total_time_raw = self::zrdn_calculate_total_time_raw($recipe->prep_time, $recipe->cook_time);
        $viewParams = array(
            'ZRDN_PLUGIN_URL' => ZRDN_PLUGIN_URL,
            'permalink' => get_permalink(),
            'border_style' => get_option('zlrecipe_outer_border_style'),
            'recipe_id' => $recipe->recipe_id,
            'custom_print_image' => get_option('zlrecipe_custom_print_image'),
            'print_hide' => get_option('zlrecipe_print_link_hide'),
            'title_hide' => get_option('recipe_title_hide'),
            'recipe_title' => $recipe->recipe_title,
            'ajax_url' => admin_url('admin-ajax.php'),
            'recipe_rating' => apply_filters('zrdn__ratings', '', $recipe->recipe_id, $recipe->post_id),
            'prep_time' => self::zrdn_format_duration($recipe->prep_time),
            'prep_time_raw' => $recipe->prep_time,
            'prep_time_label_hide' => get_option('zlrecipe_prep_time_label_hide'),
            'cook_time' => self::zrdn_format_duration($recipe->cook_time),
            'cook_time_raw' => $recipe->cook_time,
            'cook_time_label_hide' => get_option('zlrecipe_cook_time_label_hide'),
            'total_time' => self::zrdn_format_duration($total_time_raw),
            'total_time_raw' => $total_time_raw,
            'total_time_label_hide' => get_option('zlrecipe_total_time_label_hide'),
            'yield' => $recipe->yield,
            'yield_label_hide' => get_option('zlrecipe_yield_label_hide'),
            'nutritional_info' => get_option('zlrecipe_nutrition_info_label_hide', true) ? false : $nutritional_info,
            'serving_size' => $recipe->serving_size,
            'serving_size_label_hide' => get_option('zlrecipe_serving_size_label_hide'),
            'calories' => $recipe->calories,
            'calories_label_hide' => get_option('zlrecipe_calories_label_hide'),
            'fat' => $recipe->fat,
            'fat_label_hide' => get_option('zlrecipe_fat_label_hide'),
            'saturated_fat' => $recipe->saturated_fat,
            'saturated_fat_label_hide' => get_option('zlrecipe_saturated_fat_label_hide'),
            'carbs' => $recipe->carbs,
            'carbs_label_hide' => get_option('zlrecipe_carbs_label_hide'),
            'protein' => $recipe->protein,
            'protein_label_hide' => get_option('zlrecipe_protein_label_hide'),
            'fiber' => $recipe->fiber,
            'fiber_label_hide' => get_option('zlrecipe_fiber_label_hide'),
            'sugar' => $recipe->sugar,
            'sugar_label_hide' => get_option('zlrecipe_sugar_label_hide'),
            'sodium' => $recipe->sodium,
            'sodium_label_hide' => get_option('zlrecipe_sodium_label_hide'),
            'summary' => $recipe->summary,
            'summary_rich' => $summary_rich,
            'image_attributes' => $image_attributes,
            'is_featured_post_image' => $recipe->is_featured_post_image,
            'image_width' => get_option('zlrecipe_image_width'),
            'image_hide' => get_option('zlrecipe_image_hide'),
            'image_hide_print' => get_option('zlrecipe_image_hide_print'),
            'ingredient_label_hide' => get_option('zlrecipe_ingredient_label_hide'),
            'ingredient_list_type' => get_option('zlrecipe_ingredient_list_type'),
            'nested_ingredients' => $nested_ingredients,
            'instruction_label_hide' => get_option('zlrecipe_instruction_label_hide'),
            'instruction_list_type' => get_option('zlrecipe_instruction_list_type'),
            'nested_instructions' => $nested_instructions,
            'notes' => $recipe->notes,
            'formatted_notes' => $formatted_notes,
            'notes_label_hide' => get_option('zlrecipe_notes_label_hide'),
            'attribution_hide' => get_option('zrdn_attribution_hide'),
            'trans_fat' => $recipe->trans_fat,
            'trans_fat_label_hide' => get_option('zlrecipe_trans_fat_label_hide'),
            'cholesterol' => $recipe->cholesterol,
            'cholesterol_label_hide' => get_option('zlrecipe_cholesterol_label_hide'),
            'category' => $recipe->category,
            'category_label_hide' => get_option('zlrecipe_category_label_hide'),
            'cuisine' => $recipe->cuisine,
            'cuisine_label_hide' => get_option('zlrecipe_cuisine_label_hide'),
            'version' => ZRDN_VERSION_NUM,
            'print_permalink_hide' => get_option('zlrecipe_printed_permalink_hide'),
            'copyright' => get_option('zlrecipe_printed_copyright_statement'),
            // author_section is used in default theme
            'author_section' => apply_filters('zrdn__authors_render_author_for_recipe', '', $recipe),
            // author is used in other themes
            'author' => apply_filters('zrdn__authors_get_author_for_recipe', '', $recipe),
            // The second argument to apply_filters is what is returned if no one implements this hook.
            // For `nutrition_label`, we want an empty string, not $recipe object.
            'nutrition_label' => apply_filters('zrdn__automatic_nutrition_get_label', '', $recipe),
            'amp_on' => $amp_on,
            'jsonld' => $amp_on ? '' : $jsonld,
            'recipe_actions' => apply_filters('zrdn__recipe_actions', '')
        );

        do_action('zrdn__enqueue_recipe_styles');
        $custom_template = apply_filters('zrdn__custom_templates_get_formatted_recipe', false, $viewParams);
        return $custom_template ?: Util::view('recipe', $viewParams);
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
     * Return subtitle for item.
     * @param $item string Raw ingredients/instructions item
     *
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

    // Adds module to left sidebar in wp-admin for ZLRecipe
    public static function zrdn_menu_pages()
    {
        // Add the top-level admin menu
        $page_title = 'Zip Recipes Settings';
        $menu_title = 'Zip Recipes';
        $capability = 'manage_options';
        $menu_slug = 'zrdn-settings';
        $function = __NAMESPACE__ . '\ZipRecipes::zrdn_settings';

        add_menu_page(
            $page_title,
            $menu_title,
            $capability,
            $menu_slug,
            $function,
            ZRDN_PLUGIN_URL . 'images/zip-recipes-icon-16.png',
            apply_filters('zrdn_menu_position', 50)
        );

        $settings_title = "Settings";
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
        $plugin_array['zrdn_plugin'] = plugins_url('scripts/zlrecipe_editor_plugin' . self::$suffix . '.js?sver=' . ZRDN_VERSION_NUM, __FILE__);
        return $plugin_array;
    }

    public static function zrdn_register_tinymce_button($buttons)
    {
        array_push($buttons, "zrdn_buttons");
        return $buttons;
    }

    // Adds 'Settings' page to the ZipRecipe module
    public static function zrdn_settings()
    {
        global $wp_version;

        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        $zrdn_icon = ZRDN_PLUGIN_URL . "images/zip-recipes-icon-16.png";

        $registered_clear = get_option('zrdn_registered');

        $register_url = admin_url('admin.php?page=' . 'zrdn-register');
        $zrecipe_attribution_hide = get_option('zrdn_attribution_hide');
        $printed_permalink_hide = get_option('zlrecipe_printed_permalink_hide');
        $printed_copyright_statement = get_option('zlrecipe_printed_copyright_statement');
        $stylesheet = get_option('zlrecipe_stylesheet');
        $recipe_title_hide = get_option('recipe_title_hide');
        $image_hide = get_option('zlrecipe_image_hide');
        $image_hide_print = get_option('zlrecipe_image_hide_print');
        $print_link_hide = get_option('zlrecipe_print_link_hide');
        $ingredient_label_hide = get_option('zlrecipe_ingredient_label_hide');
        $ingredient_list_type = get_option('zlrecipe_ingredient_list_type');
        $instruction_label_hide = get_option('zlrecipe_instruction_label_hide');
        $instruction_list_type = get_option('zlrecipe_instruction_list_type');
        $image_width = get_option('zlrecipe_image_width');
        $outer_border_style = get_option('zlrecipe_outer_border_style');
        $custom_print_image = get_option('zlrecipe_custom_print_image');

        // load other option values in to variables. These variables are used to load saved values through variable variables
        $notes_label_hide = get_option('zlrecipe_notes_label_hide');
        $nutrition_info_label_hide = get_option('zlrecipe_nutrition_info_label_hide',true);
        $prep_time_label_hide = get_option('zlrecipe_prep_time_label_hide');
        $cook_time_label_hide = get_option('zlrecipe_cook_time_label_hide');
        $total_time_label_hide = get_option('zlrecipe_total_time_label_hide');
        $yield_label_hide = get_option('zlrecipe_yield_label_hide');
        $serving_size_label_hide = get_option('zlrecipe_serving_size_label_hide');
        $calories_label_hide = get_option('zlrecipe_calories_label_hide');
        $fat_label_hide = get_option('zlrecipe_fat_label_hide');
        $carbs_label_hide = get_option('zlrecipe_carbs_label_hide', '');
        $protein_label_hide = get_option('zlrecipe_protein_label_hide', '');
        $fiber_label_hide = get_option('zlrecipe_fiber_label_hide', '');
        $sugar_label_hide = get_option('zlrecipe_sugar_label_hide', '');
        $saturated_fat_label_hide = get_option('zlrecipe_saturated_fat_label_hide', '');
        $sodium_label_hide = get_option('zlrecipe_sodium_label_hide', '');
        $trans_fat_label_hide = get_option('zlrecipe_trans_fat_label_hide', '');
        $cholesterol_label_hide = get_option('zlrecipe_cholesterol_label_hide', '');
        $category_label_hide = get_option('zlrecipe_category_label_hide', '');
        $cuisine_label_hide = get_option('zlrecipe_cuisine_label_hide', '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $key => $val) {
                $_POST[$key] = stripslashes($val);
            }

            if ($_POST['action'] === "update_settings") {

                $zrecipe_attribution_hide = Util::get_array_value('zrecipe-attribution-hide', $_POST);
                $printed_permalink_hide = Util::get_array_value('printed-permalink-hide', $_POST);
                $printed_copyright_statement = Util::get_array_value('printed-copyright-statement', $_POST);
                $stylesheet = Util::get_array_value('stylesheet', $_POST);
                $recipe_title_hide = Util::get_array_value('recipe-title-hide', $_POST);
                $image_hide = Util::get_array_value('image-hide', $_POST);
                $image_hide_print = Util::get_array_value('image-hide-print', $_POST);
                $print_link_hide = Util::get_array_value('print-link-hide', $_POST);
                $ingredient_label_hide = self::zrdn_strip_chars(Util::get_array_value('ingredient-label-hide', $_POST));
                $ingredient_list_type = Util::get_array_value('ingredient-list-type', $_POST);
                $instruction_label_hide = Util::get_array_value('instruction-label-hide', $_POST);
                $instruction_list_type = self::zrdn_strip_chars(Util::get_array_value('instruction-list-type', $_POST));
                $notes_label_hide = Util::get_array_value('notes-label-hide', $_POST);
                $prep_time_label_hide = Util::get_array_value('prep-time-label-hide', $_POST);
                $cook_time_label_hide = Util::get_array_value('cook-time-label-hide', $_POST);
                $total_time_label_hide = Util::get_array_value('total-time-label-hide', $_POST);
                $yield_label_hide = Util::get_array_value('yield-label-hide', $_POST);
                $serving_size_label_hide = Util::get_array_value('serving-size-label-hide', $_POST);
                $calories_label_hide = Util::get_array_value('calories-label-hide', $_POST);
                $fat_label_hide = Util::get_array_value('fat-label-hide', $_POST);
                $carbs_label_hide = Util::get_array_value('carbs-label-hide', $_POST);
                $protein_label_hide = Util::get_array_value('protein-label-hide', $_POST);
                $fiber_label_hide = Util::get_array_value('fiber-label-hide', $_POST);
                $sugar_label_hide = Util::get_array_value('sugar-label-hide', $_POST);
                $saturated_fat_label_hide = Util::get_array_value('saturated-fat-label-hide', $_POST);
                $sodium_label_hide = Util::get_array_value('sodium-label-hide', $_POST);
                $image_width = Util::get_array_value('image-width', $_POST);
                $outer_border_style = Util::get_array_value('outer-border-style', $_POST);
                $custom_print_image = Util::get_array_value('custom-print-image', $_POST);

                $trans_fat_label_hide = Util::get_array_value('trans-fat-label-hide', $_POST);
                $cholesterol_label_hide = Util::get_array_value('cholesterol-label-hide', $_POST);
                $category_label_hide = Util::get_array_value('category-label-hide', $_POST);
                $cuisine_label_hide = Util::get_array_value('cuisine-label-hide', $_POST);
                $nutrition_info_label_hide = Util::get_array_value('nutrition-info-label-hide', $_POST);

                update_option('zrdn_attribution_hide', $zrecipe_attribution_hide);
                update_option('zlrecipe_printed_permalink_hide', $printed_permalink_hide);
                update_option('zlrecipe_printed_copyright_statement', $printed_copyright_statement);
                update_option('zlrecipe_stylesheet', $stylesheet);
                update_option('recipe_title_hide', $recipe_title_hide);
                update_option('zlrecipe_image_hide', $image_hide);
                update_option('zlrecipe_image_hide_print', $image_hide_print);
                update_option('zlrecipe_print_link_hide', $print_link_hide);
                update_option('zlrecipe_ingredient_label_hide', $ingredient_label_hide);
                update_option('zlrecipe_ingredient_list_type', $ingredient_list_type);
                update_option('zlrecipe_instruction_label_hide', $instruction_label_hide);
                update_option('zlrecipe_instruction_list_type', $instruction_list_type);
                update_option('zlrecipe_notes_label_hide', $notes_label_hide);
                update_option('zlrecipe_prep_time_label_hide', $prep_time_label_hide);
                update_option('zlrecipe_cook_time_label_hide', $cook_time_label_hide);
                update_option('zlrecipe_total_time_label_hide', $total_time_label_hide);
                update_option('zlrecipe_yield_label_hide', $yield_label_hide);
                update_option('zlrecipe_serving_size_label_hide', $serving_size_label_hide);
                update_option('zlrecipe_calories_label_hide', $calories_label_hide);
                update_option('zlrecipe_fat_label_hide', $fat_label_hide);
                update_option('zlrecipe_carbs_label_hide', $carbs_label_hide);
                update_option('zlrecipe_protein_label_hide', $protein_label_hide);
                update_option('zlrecipe_fiber_label_hide', $fiber_label_hide);
                update_option('zlrecipe_sugar_label_hide', $sugar_label_hide);
                update_option('zlrecipe_saturated_fat_label_hide', $saturated_fat_label_hide);
                update_option('zlrecipe_sodium_label_hide', $sodium_label_hide);
                update_option('zlrecipe_image_width', $image_width);
                update_option('zlrecipe_outer_border_style', $outer_border_style);
                update_option('zlrecipe_custom_print_image', $custom_print_image);

                update_option('zlrecipe_trans_fat_label_hide', $trans_fat_label_hide);
                update_option('zlrecipe_cholesterol_label_hide', $cholesterol_label_hide);
                update_option('zlrecipe_category_label_hide', $category_label_hide);
                update_option('zlrecipe_cuisine_label_hide', $cuisine_label_hide);
                update_option('zlrecipe_nutrition_info_label_hide', $nutrition_info_label_hide);

                do_action('zrdn__custom_templates_save', $_POST);
            }
        }

        $printed_copyright_statement = esc_attr($printed_copyright_statement);
        $image_width = esc_attr($image_width);
        $custom_print_image = esc_attr($custom_print_image);

        $zrecipe_attribution_hide = (strcmp($zrecipe_attribution_hide, 'Hide') == 0 ? 'checked="checked"' : '');
        $printed_permalink_hide = (strcmp($printed_permalink_hide, 'Hide') == 0 ? 'checked="checked"' : '');
        $recipe_title_hide = (strcmp($recipe_title_hide, 'Hide') == 0 ? 'checked="checked"' : '');
        $image_hide = (strcmp($image_hide, 'Hide') == 0 ? 'checked="checked"' : '');
        $image_hide_print = (strcmp($image_hide_print, 'Hide') == 0 ? 'checked="checked"' : '');
        $print_link_hide = (strcmp($print_link_hide, 'Hide') == 0 ? 'checked="checked"' : '');

        // Stylesheet processing
        $stylesheet = (strcmp($stylesheet, 'zlrecipe-std') == 0 ? 'checked="checked"' : '');

        // Outer (hrecipe) border style
        $obs = '';
        $borders = array('None' => '', 'Solid' => '1px solid', 'Dotted' => '1px dotted', 'Dashed' => '1px dashed', 'Thick Solid' => '2px solid', 'Double' => 'double');
        foreach ($borders as $label => $code) {
            $obs .= '<option value="' . $code . '" ' . (strcmp($outer_border_style, $code) == 0 ? 'selected="true"' : '') . '>' . $label . '</option>';
        }

        $ingredient_label_hide = (strcmp($ingredient_label_hide, 'Hide') == 0 ? 'checked="checked"' : '');
        $ing_ul = (strcmp($ingredient_list_type, 'ul') == 0 ? 'checked="checked"' : '');
        $ing_ol = (strcmp($ingredient_list_type, 'ol') == 0 ? 'checked="checked"' : '');
        $ing_l = (strcmp($ingredient_list_type, 'l') == 0 ? 'checked="checked"' : '');
        $ing_p = (strcmp($ingredient_list_type, 'p') == 0 ? 'checked="checked"' : '');
        $ing_div = (strcmp($ingredient_list_type, 'div') == 0 ? 'checked="checked"' : '');
        $instruction_label_hide = (strcmp($instruction_label_hide, 'Hide') == 0 ? 'checked="checked"' : '');
        $ins_ul = (strcmp($instruction_list_type, 'ul') == 0 ? 'checked="checked"' : '');
        $ins_ol = (strcmp($instruction_list_type, 'ol') == 0 ? 'checked="checked"' : '');
        $ins_l = (strcmp($instruction_list_type, 'l') == 0 ? 'checked="checked"' : '');
        $ins_p = (strcmp($instruction_list_type, 'p') == 0 ? 'checked="checked"' : '');
        $ins_div = (strcmp($instruction_list_type, 'div') == 0 ? 'checked="checked"' : '');
        $other_options = '';
        $other_options_array = array('Nutrition Info','Prep Time', 'Cook Time', 'Total Time', 'Yield', 'Serving Size', 'Category', 'Cuisine');

        foreach ($other_options_array as $option) {
            $name = strtolower(str_replace(' ', '-', $option));
            $value_hide = strtolower(str_replace(' ', '_', $option)) . '_label_hide';
            $value_hide_attr = ${$value_hide} == "Hide" ? 'checked="checked"' : '';
            $other_options .= '<tr valign="top">
            <td>
            	<label>
            		<input type="checkbox" name="' . $name . '-label-hide" value="Hide" ' . $value_hide_attr . ' /> Hide ' . $option .'
            	</label>
            </td>
        </tr>';
        }

        $settingsParams = array('zrdn_icon' => $zrdn_icon,
            'registered' => true,
            'custom_print_image' => $custom_print_image,
            'zrecipe_attribution_hide' => $zrecipe_attribution_hide,
            'printed_permalink_hide' => $printed_permalink_hide,
            'printed_copyright_statement' => $printed_copyright_statement,
            'stylesheet' => $stylesheet,
            'recipe_title_hide' => $recipe_title_hide,
            'print_link_hide' => $print_link_hide,
            'image_width' => $image_width,
            'image_hide' => $image_hide,
            'image_hide_print' => $image_hide_print,
            'obs' => $obs,
            'ingredient_label_hide' => $ingredient_label_hide,
            'ing_l' => $ing_l,
            'ing_ol' => $ing_ol,
            'ing_ul' => $ing_ul,
            'ing_p' => $ing_p,
            'ing_div' => $ing_div,
            'instruction_label_hide' => $instruction_label_hide,
            'ins_l' => $ins_l,
            'ins_ol' => $ins_ol,
            'ins_ul' => $ins_ul,
            'ins_p' => $ins_p,
            'ins_div' => $ins_div,
            'other_options' => $other_options,
            'registration_url' => self::$registration_url,
            'wp_version' => $wp_version,
            'installed_plugins' => Util::zrdn_get_installed_plugins(),
            'extensions_settings' => apply_filters('zrdn__extention_settings_section', ''),
            'home_url' => home_url(),
            'author_section' => apply_filters('zrdn__authors_get_set_settings', '', $_POST),
            'settings_promo' => apply_filters('zrdn__settings_promo', ''),
            'register_url' => $register_url,
            'registered_clear' => $registered_clear
        );

        Util::print_view('settings', $settingsParams);
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
            //require_once(ZRDN_PLUGIN_DIRECTORY . "gutenberg/RecipeBlock.php");
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

    /**
     * Calculate Total time in raw format
     *
     * @param $prep_time
     * @param $cook_time
     * @return false|null|string
     */
    public static function zrdn_calculate_total_time_raw($prep_time, $cook_time)
    {
        $total_time = NULL;
        $prep = self::zrdn_extract_time($prep_time);
        $cook = self::zrdn_extract_time($cook_time);
        $prep_time_hours = $prep['time_hours'];
        $prep_time_minutes = $prep['time_minutes'];
        $cook_time_hours = $cook['time_hours'];
        $cook_time_minutes = $cook['time_minutes'];
        if ($prep_time_hours || $prep_time_minutes || $cook_time_hours || $cook_time_minutes) {
            $prep_time_total =

            $prep_time_total = sprintf("%02d", $prep_time_hours) . ':' . sprintf("%02d", $prep_time_minutes) . ':00';
            $cook_time_total = sprintf("%02d", $cook_time_hours) . ':' . sprintf("%02d", $cook_time_minutes) . ':00';
            $total_time = date("H:i:s", strtotime($prep_time_total) + strtotime($cook_time_total));
            $time = explode(':', $total_time);
            // converting 01 to 1 using int
            return 'PT' . (int)$time[0] . 'H' . (int)$time[1] . 'M';
        }
        return $total_time;
    }

    // Pulls a recipe from the db
    public static function zrdn_select_recipe_db($recipe_id)
    {
        global $wpdb;

        $selectStatement = sprintf("SELECT * FROM `%s%s` WHERE recipe_id=%d", $wpdb->prefix, RecipeModel::TABLE_NAME, $recipe_id);
        $recipe = $wpdb->get_row($selectStatement);

        return $recipe;
    }

    // function to include the javascript for the Add Recipe button
    public static function zrdn_process_head()
    {
        $css = get_option('zlrecipe_stylesheet');
        Util::print_view('header', array(
                'ZRDN_PLUGIN_URL' => ZRDN_PLUGIN_URL,
                'css' => $css,
                'suffix' => self::$suffix
            )
        );
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

        $recipe_json_ld = array(
            "@context" => "http://schema.org",
            "@type" => "Recipe",
            "description" => $recipe->summary,
            "image" => $recipe->recipe_image,
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
            "recipeYield" => $recipe->yield
        );

        $total_time = self::zrdn_calculate_total_time_raw($recipe->prep_time, $recipe->cook_time);
        if ($total_time) {
            $recipe_json_ld["totalTime"] = $total_time;
        }

        $cleaned_recipe_json_ld = clean_jsonld($recipe_json_ld);

        $author = apply_filters('zrdn__authors_get_author_for_recipe', false, $recipe);

        if ($author) {
            $cleaned_recipe_json_ld["author"] = (object)array(
                "@type" => "Person",
                "name" => $author
            );
        }
        $rating_data = apply_filters('zrdn__ratings_format_amp', '',$recipe->recipe_id, $recipe->post_id);
        if ($rating_data) {
            $cleaned_recipe_json_ld["aggregateRating"] = (object)array(
                "bestRating" => $rating_data['max'],
                "ratingValue" => $rating_data['rating'],
                "ratingCount" => $rating_data['count'],
                "worstRating" => $rating_data['min']
            );
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
        $shortcode_regex = '/\[amd-zlrecipe-recipe:(\d+)\]/';
        $matches = array(); // ensure matches is empty
        preg_match($shortcode_regex, $post->post_content, $matches);
        if (isset($matches[1])) {
            // Find recipe
            $recipe_id = $matches[1];
            $recipe = RecipeModel::db_select($recipe_id);
            $recipe_json_ld = self::jsonld($recipe);
        }

        $metadata['hasPart'] = $recipe_json_ld;

        return $metadata;
    }

    public static function amp_styles()
    {
        $sprite_file = plugins_url('plugins/VisitorRating/images/rating-sprite.png', __FILE__);
        ?>
        .zrdn__rating__container .zrdn_star
        {
        background-image: url('<?php echo $sprite_file ?>');
        background-repeat: no-repeat;
        height: 18px;
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
     *
     *
     * @todo: change this function to make use of the attachment id stored in the recipe object.
     * Get Responsive Image attributes from URL
     *
     * It checks image is not external and return images attributes like srcset, sized etc.
     *
     * @param type $url
     * @return type
     */
    public static function zrdn_get_responsive_image_attributes($url)
    {
        /**
         * set up default array values
         */
        $attributes = array();
        $attributes['url'] = $url;

        //first, try to get from url
        $attachment_id = attachment_url_to_postid($url);
        if (!$attachment_id) $attachment_id = get_post_thumbnail_id();
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
     *  Make Featured image as recipe image
     *
     *  This function is only for jsonld and schema data.
     *
     * @param $post_id
     * @param $post
     * @param $update
     */
    public static function zrdn_post_featured_image($post_id)
    {
        $recipe = new Recipe(false, $post_id);

        global $wpdb;
        $table = $wpdb->prefix . RecipeModel::TABLE_NAME;
        $featured_img = NULL;
        $featured_img = wp_get_attachment_url(get_post_thumbnail_id($post_id));
        $update = array();

        if ($featured_img) {
            // only set featured post image to recipe image if it's currently a featured image or empty
            if ($recipe->is_featured_post_image || empty($recipe->recipe_image)) {
                $update['recipe_image'] = $featured_img;
                $update['is_featured_post_image'] = true;
            }
        } else if ($recipe->is_featured_post_image) { // post has no featured image so clean up
            $update['is_featured_post_image'] = false;
            $update['recipe_image'] = null;
        }

        // run update if need be
        if ($update) {
            $wpdb->update( $table, $update, array( 'recipe_id' => $recipe->recipe_id ) );
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
