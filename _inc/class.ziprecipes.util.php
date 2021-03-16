<?php
namespace ZRDN;

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
        if ($recipe_id === false ) return false;

        //even if on gutenberg, with elementor we have to use classic shortcodes.
        if (Util::uses_gutenberg() && !Util::uses_elementor()){
            return '<!-- wp:zip-recipes/recipe-block {"id":"'.$recipe_id.'"} /-->';
        } else {
            return '[zrdn-recipe id="'.$recipe_id.'"]';
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

        if (strpos($post_data->post_content, 'zrdn-recipe')!==FALSE || strpos($post_data->post_content, 'amd-zlrecipe-recipe')!==FALSE || strpos($post_data->post_content, 'wp:zip-recipes/recipe-block')!==FALSE){
            return true;
        }
        return false;
    }


    /**
     *
     * get the shortcode or block for a page type
     *
     * @param boolean $recipe_id
     * @param boolean $match_all, to get pattern for shortcode without recipeid
     * @param string $type detect, classic, legacy, gutenberg
     * @return string $shortcode
     *
     *
     */


    public static function get_shortcode_pattern($recipe_id=false, $match_all=false, $type = 'detect' )
    {
        //even if on gutenberg, with elementor we have to use classic shortcodes.
        $gutenberg = Util::uses_gutenberg() && !Util::uses_elementor();
        $classic = !$gutenberg;

        if ( $type === 'detect' ) {
        	$type = $gutenberg ? 'gutenberg' : 'classic';
        }

        if ( $type == 'classic' ) {
            if ($recipe_id){
            	return '/(\[zrdn-recipe.*id=["|\']?'.$recipe_id.'["|\']?\])/i';
            }
            if ($match_all){
	            return '/(\[zrdn-recipe.*id=.*?\])/i';
            }
            return '/\[zrdn-recipe.*id=["|\']?([0-9]\d*).*?\]/i';
        }

	    if ( $type == 'gutenberg' ) {
            if ($recipe_id){
                return '/<!-- wp:zip-recipes\/recipe-block {"id":"'.$recipe_id.'".*?} \/-->/i';
            }
            if ($match_all){
                return '/(<!-- wp:zip-recipes\/recipe-block {.*?} \/-->)/i';
            }
            return '/<!-- wp:zip-recipes\/recipe-block {.*?"id":"([0-9]\d*)".*?} \/-->/i';
        }

	    if ( $type == 'legacy' ) {
		    if ($recipe_id){
			    return '/(\[amd-zlrecipe-recipe:'.$recipe_id.'\])/i';
		    }
		    if ($match_all){
			    return '/(\[amd-zlrecipe-recipe:.*?\])/i';
		    }
		    return '/\[amd-zlrecipe-recipe:([0-9]\d*).*?\]/i';
	    }
    }


	public static function minimal_number( $value ) {
		preg_match_all( '/(^[0-9]{1,5})\.([0-9]{1,5})(.*)/', $value, $matches );
		if ( isset( $matches[1][0] ) && isset($matches[2][0]) && isset($matches[3][0]) ) {
			$number = $matches[1][0];
			$first_part = $matches[1][0];
			$second_part = $matches[2][0];
			$text = $matches[3][0];
			if (strlen($first_part) >=2) {
				return $first_part.$text;
			} else {
				return $first_part.'.'.substr($second_part, 0, 1).$text;
			}

		}
		return $value;
	}

    /**
     * Render PHP template
     * @param string $file
     * @param array $options
     * @param Recipe $recipe
     * @param array|bool $settings
     *
     * @return  string $html
     */

	public static function render_template($file, $recipe = false, $settings = false){
        $plugin_dir = trailingslashit(ZRDN_PATH);
		$addon = str_replace('.php', '', $file );
		//check if we have an addon for this file
		if (file_exists($plugin_dir.$file)) {
			$plugin_file = $plugin_dir.$file;
		} elseif (file_exists($plugin_dir."plugins/$addon/views/$addon.php")) {
			$plugin_file = $plugin_dir."plugins/$addon/views/$addon.php";
		} else {
			$plugin_file = $plugin_dir . "views/" . $file;
		}
		$theme_file = trailingslashit( get_stylesheet_directory()) . trailingslashit(basename(ZRDN_PATH)) . $file;
        $file = file_exists($theme_file) ? $theme_file : $plugin_file;
        if (!file_exists($file)) return '';

		if (strpos($file, '.php') !== FALSE) {
            ob_start();
            require $file;
            $contents = ob_get_clean();
        } else {
            $contents = file_get_contents($file);
        }

		if ($recipe) {
			foreach ( $recipe as $fieldname => $value ) {
				if ( is_array( $value ) ) {
					continue;
				}
				$contents = str_replace( '{' . $fieldname . '}', $value,
					$contents );
			}
		}

        if (is_array($settings) && count($settings)>0){
            foreach($settings as $placeholder => $value){
                if (strpos($contents,'{/'.$placeholder.'}')!==FALSE){
                    $value = ($value==='true' || $value==1 || $value) ? true : false;

                    if (!$value){
                        //remove the entire string
                        $contents = preg_replace('/{'.$placeholder.'}.*?{\/'.$placeholder.'}/s', '', $contents);
                    } else {
                        //only remove the thumbnails
                        $contents = str_replace(array('{'.$placeholder.'}','{/'.$placeholder.'}'),'', $contents);
                    }
                } elseif(!is_array($value)) {
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
	 * @param $tabs
     * @param $options
	 */
	public static function settings_header($tabs, $options) {
		?>

		<div class="zrdn-settings-container">
			<ul class="tabs">
				<div class="tabs-content">
					<img class="zrdn-settings-logo" src="<?= trailingslashit( ZRDN_PLUGIN_URL ) . "images/logo-new.png"?>" alt='Zip Recipes'>
					<div class="header-links">
						<div class="tab-links">
							<?php
							$first = true;
							foreach ($tabs as $tab => $data) {
								if (isset($data['cap']) && current_user_can( $data['cap'] ) ) continue;
								$current = $first ? 'current' : '';
								$first = false;

								if (!isset($_GET['id']) && isset($_GET['page']) && $_GET['page'] === $data['page']) {
									$url = '#'.$tab.'#top';
                                } else {
								    $url = add_query_arg(array('page' => $data['page'].'#'.$tab), admin_url('admin.php'));
                                }
								?>
								<li class="tab-link <?=$current?>" data-tab="<?=$tab?>">
                                    <a class="tab-text tab-<?=$tab?>" href="<?php echo $url?>"><?=$data['title']?></a>
								</li>
							<?php } ?>
						</div>
						<div class="documentation-container">
							<div class="documentation">
								<a target="_blank" href="https://ziprecipes.net/knowledge-base-overview/"><?php _e( "Documentation", "zip-recipes" ); ?></a>
							</div>
							<a target="_blank" href="https://ziprecipes.net/support" class="button button-primary"><?php _e( "Support", "zip-recipes" ); ?></a>
                            <?php if ($options) { ?>
							<div id="zrdn-toggle-options">
								<div id="zrdn-toggle-link-wrap">
									<button type="button"
									        id="zrdn-show-toggles"
									        class="button button button-upsell"
									        aria-controls="screen-options-wrap"><?php _e( "Display options",
											"zip-recipes" ); ?>
										<span id="zrdn-toggle-arrows"
										      class="dashicons dashicons-arrow-down-alt2"></span>
									</button>
								</div>
							</div>
							<?php }?>
						</div>
					</div>
				</div>
			</ul>
		</div>

		<?php
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
			    'title' => __("General", "zip-recipes"),
			    'source' => 'general',
			    'class' => 'zrdn-general',
		    ),

		    array(
			    'title' => __("Nutrition", "zip-recipes"),
			    'source' => "nutrition",
			    'class' => 'small',
		    ),

		    array(
			    'title' => __("Authors", "zip-recipes"),
			    'source' => "authors",
			    'class' => 'small',
		    ),

		    array(
			    'title' => __("Add-ons", "zip-recipes"),
			    'source' => "plugins",
			    'class' => 'small',
		    ),

		    array(
			    'title' => __("Advanced", "zip-recipes"),
			    'source' => "advanced",
			    'class' => 'small',
		    ),

		    array(
			    'title' => __("Other plugins", "zip-recipes"),
			    'source' => "other",
			    'class' => 'half-height other-plugins',
                'template' => 'other-plugins.php',
			    'controls' => '<div class="rsp-logo"><a href="https://really-simple-plugins.com/"><img src="'. trailingslashit(ZRDN_PLUGIN_URL) .'images/really-simple-plugins.png" /></a></div>',
		    ),
	    );
	    $defaults = array(
		    'title' => '',
		    'source' => '',
		    'class' => 'small',
		    'can_hide' => true,
            'controls' => '',
        );
	    foreach ($grid_items as $key => $grid_item ) {
		    $grid_items[$key] = wp_parse_args($grid_item, $defaults);
	    }
	    return apply_filters('zrdn_grid_items', $grid_items);
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
				'source'    => 'template',
				'options'   => array(
					'default'               => __( "Default", "zip-recipes" ),
					'custom'                => __( "Custom", "zip-recipes" ),
					'_template_autumn'      => __( "Autumn", "zip-recipes" ),
					'_template_canada'      => __( "Canada", "zip-recipes" ),
					'_template_cozy_orange' => __( "Cozy Orange", "zip-recipes" ),
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
				'label'     => __( 'Recipe presets', 'zip-recipes' ),
				'comment'   => sprintf(__("To get more templates, check out %spremium%s", "zip-recipes"), '<a target="_blank" href="https://ziprecipes.net/premium">', '</a>'),
			),

			'border_style' => array(
				'type'      => 'select',
				'source'    => 'template',
				'default'    => 'dotted',
				'options'   => array(
					'initial'     => __( 'No border', "zip-recipes" ),
					'solid'       => __( 'Solid', "zip-recipes" ),
					'dotted'      => __( 'Dotted', "zip-recipes" ),
					'dashed'      => __( 'Dashed', "zip-recipes" ),
					'double'      => __( 'Double', "zip-recipes" ),
				),
				'table'     => false,
				'label'     => __( 'Border style', 'zip-recipes' ),
			),

			'border_width' => array(
				'type'      => 'number',
				'source'    => 'template',
				'default'    => '1',
				'table'     => false,
				'label'     => __( 'Border width', 'zip-recipes' ),
			),

			'border_radius' => array(
				'type'      => 'number',
				'source'    => 'template',
				'default'    => '15',
				'table'     => false,
				'label'     => __( 'Border radius', 'zip-recipes' ),
			),

			'box_shadow' => array(
				'type'               => 'checkbox',
				'source'             => 'template',
				'default'             => false,
				'disabled'           => true,
				'label'              => __( "Box shadow", 'zip-recipes' ),
			),

			'background_color' => array(
				'type'               => 'colorpicker',
				'source'             => 'template',
				'default'            => 'rgba(0,0,0,0)',
				'disabled'           => true,
				'label'              => __( "Background color", 'zip-recipes' ),
			),

			'border_color' => array(
				'type'               => 'colorpicker',
				'source'             => 'template',
				'default'             => '#000',
				'disabled'           => true,
				'label'              => __( "Border color", 'zip-recipes' ),
			),

			'text_color' => array(
				'type'               => 'colorpicker',
				'source'             => 'template',
				'default'             => '#000',
				'disabled'           => true,
				'label'              => __( "Text color", 'zip-recipes' ),
			),

			'primary_color' => array(
				'type'               => 'colorpicker',
				'source'             => 'template',
				'default'             => '#f37226',
				'disabled'           => true,
				'label'              => __( "Primary color", 'zip-recipes' ),
			),

			'link_color' => array(
				'type'               => 'colorpicker',
				'source'             => 'template',
				'default'             => '#f37226',
				'disabled'           => true,
				'label'              => __( "Link color", 'zip-recipes' ),
			),

			'jump_to_recipe_link' => array(
				'type'               => 'checkbox',
				'source'             => 'general',
				'table'              => false,
				'label'              => __( "Enable jump to recipe link", 'zip-recipes' ),
				'help'              => __( "You can add a link at the top of each recipe which scrolls down to the recipe on the page when the user clicks it.", 'zip-recipes' ),
			),

			'show_summary_on_archive_pages' => array(
				'type'               => 'checkbox',
				'source'             => 'general',
				'table'              => false,
				'default'            => false,
				'label'              => __( "Show summary on archive pages", 'zip-recipes' ),
				'help'              => __( "You can choose to show the recipe summary instead of the recipe on archive pages.", 'zip-recipes' ),
			),

			'ingredients_list_type' => array(
				'type'      => 'select',
				'source'    => 'ingredients',
				'options'   => array(
					'nobullets'   => __( 'List, no bullets', 'zip-recipes' ),
					'numbers'  => __( 'Numbered List', 'zip-recipes' ),
					'bullets'  => __( 'Bulleted List', 'zip-recipes' ),
					'numbers_border_circle'   => __( 'Bordered numbers | circle', 'zip-recipes' ),
					'numbers_border_square'  => __( 'Bordered numbers | square', 'zip-recipes' ),
					'numbers_solid_circle'  => __( 'Colored numbers | disc', 'zip-recipes' ),
					'numbers_solid_square'  => __( 'Colored numbers | square', 'zip-recipes' ),
					'numbers_counter'  => __( 'Counter', 'zip-recipes' ),
					'bullets_border_circle'  => __( 'Circles', 'zip-recipes' ),
					'bullets_border_square'  => __( 'Squares', 'zip-recipes' ),
					'bullets_solid_circle'  => __( 'Discs', 'zip-recipes' ),
					'bullets_solid_square'  => __( 'Blocks', 'zip-recipes' ),
				),
				'disabled'  => array(
					'numbers_border_circle',
					'numbers_border_square',
					'numbers_solid_circle',
					'numbers_solid_square',
					'numbers_counter',
					'bullets_border_circle',
					'bullets_border_square',
					'bullets_solid_circle',
					'bullets_solid_square',
				),
				'table'     => false,
				'label'     => __( 'Ingredients List Type', 'zip-recipes' ),
				'default'   => 'l',
			),

			'instructions_list_type' => array(
				'type'      => 'select',
				'source'    => 'instructions',
				'options'   => array(
					'nobullets'   => __( 'List, no bullets', 'zip-recipes' ),
					'numbers'  => __( 'Numbered List', 'zip-recipes' ),
					'bullets'  => __( 'Bulleted List', 'zip-recipes' ),
					'numbers_border_circle'   => __( 'Bordered numbers | circle', 'zip-recipes' ),
					'numbers_border_square'  => __( 'Bordered numbers | square', 'zip-recipes' ),
					'numbers_solid_circle'  => __( 'Colored numbers | disc', 'zip-recipes' ),
					'numbers_solid_square'  => __( 'Colored numbers | square', 'zip-recipes' ),
					'numbers_counter'  => __( 'Counter', 'zip-recipes' ),
					'bullets_border_circle'  => __( 'Circles', 'zip-recipes' ),
					'bullets_border_square'  => __( 'Squares', 'zip-recipes' ),
					'bullets_solid_circle'  => __( 'Discs', 'zip-recipes' ),
					'bullets_solid_square'  => __( 'Blocks', 'zip-recipes' ),
				),
				'disabled'  => array(
					'numbers_border_circle',
					'numbers_border_square',
					'numbers_solid_circle',
					'numbers_solid_square',
					'numbers_counter',
					'bullets_border_circle',
					'bullets_border_square',
					'bullets_solid_circle',
					'bullets_solid_square',
				),
				'table'     => false,
				'label'     => __( 'Instructions List Type', 'zip-recipes' ),
				'default'   => 'l',
			),

			'copyright_statement' => array(
				'type'      => 'text',
				'source'    => 'copyright',
				'default'   => sprintf(__('Copyright %s', 'zip-recipes'), get_bloginfo('name')),
				'table'     => false,
				'label'     => __( "Copyright statement", 'zip-recipes' ),
			),
			
			'hide_on_duplicate_image' => array(
				'type'               => 'checkbox',
				'source'             => 'recipe_image',
				'table'              => false,
				'label'              => __( "Hide recipe image when post image is the same", 'zip-recipes' ),
				'help'              => __( "When enabled, the recipe image will be hidden if it's the same as the image in the post", 'zip-recipes' ),
			),

			'hide_print_image' => array(
				'type'      => 'checkbox',
				'source'    => 'recipe_image',
				'table'     => false,
				'label'     => __( "Hide Image in print view", 'zip-recipes' ),
			),

			'hide_permalink' => array(
				'type'      => 'checkbox',
				'source'    => 'general',
				'default'    => true,
				'table'     => false,
				'label'     => __( "Hide link to recipe in print view", 'zip-recipes' ),
				'help'     => __( "The link is a direct link to the recipe, at the bottom of your recipe printout", 'zip-recipes' ),
			),

			'hide_print_nutrition_label' => array(
				'type'      => 'checkbox',
				'source'    => 'nutrition_label',
				'table'     => false,
				'label'     => __( 'Hide nutrition label in print view',
					'zip-recipes' ),
			),

			'hide_ingredients_label' => array(
				'type'      => 'checkbox',
				'source'    => 'ingredients',
				'table'     => false,
				'label'     => __( "Hide label", 'zip-recipes' ),
			),

			'hide_instructions_label' => array(
				'type'      => 'checkbox',
				'source'    => 'instructions',
				'table'     => false,
				'label'     => __( "Hide label", 'zip-recipes' ),
			),

			'hide_notes_label' => array(
				'type'      => 'checkbox',
				'source'    => 'notes',
				'table'     => false,
				'label'     => __( 'Hide label', 'zip-recipes' ),
			),

			'hide_tags_label' => array(
				'type'      => 'checkbox',
				'source'    => 'tags',
				'table'     => false,
				'label'     => __( 'Hide label', 'zip-recipes' ),
			),

			'hide_social_label' => array(
				'type'      => 'checkbox',
				'source'    => 'social_sharing',
				'table'     => false,
				'label'     => __( 'Hide label', 'zip-recipes' ),
			),

			'social_icon_type' => array(
				'type'      => 'select',
				'source'    => 'social_sharing',
				'table'     => false,
				'reload_on_change'      => true,
				'options'   => array(
					'logo' => __( 'Official logo', 'zip-recipes' ),
					'square' => __( 'Square', 'zip-recipes' ),
					'round' => __( 'Round', 'zip-recipes' ),
				),
				'default' => 'logo',
				'label'     => __( 'Label style', 'zip-recipes' ),
			),

			'hide_nutrition_text_expl' => array(
				'type'      => 'checkbox',
				'source'    => 'nutrition_text',
				'table'     => false,
				'label'     => __( 'Hide explanation', 'zip-recipes' ),
			),

			'print_image' => array(
				'type'                  => 'upload',
				'source'                => 'general',
				'low_resolution_notice' => __( "Image resolution too low, or image size not generated",
					"zip-recipes" ),
				'size'                  => 'zrdn_custom_print_image',
				'table'                 => false,
				'label'                 => __( 'Custom Print Button', 'zip-recipes' ),
			),

			'Authors' => array(
				'type'               => 'checkbox',
				'source'             => 'authors',
				'is_plugin'         => true,
				'disabled'           => true,
				'table'              => false,
				'label'              => __( "Use custom authors", 'zip-recipes' ),
				'help'              => __( "By default, Zip Recipes uses WordPress authors. You can use your own, custom authors as well.", 'zip-recipes' ),
				'comment'           => sprintf(__("The custom author field is a %spremium%s feature", "zip-recipes"), '<a target="_blank" href="https://ziprecipes.net/premium">', '</a>'),
			),

			'default_author' => array(
				'type'               => 'select',
				'source'             => 'authors',
				'options'            => ZipRecipes::$authors,
				'table'              => false,
				'disabled'           => true,
				'label'              => __( "Select a default author", 'zip-recipes' ),
				'condition'         => array(
					'Authors' => true,
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
					'Authors' => true,
				),
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
				'default'   => 'html',
				'table'     => false,
				'label'     => __( 'Choose nutrition label display method', 'zip-recipes' ),
				'help'      => __( 'You can choose if you want to show the label in html format, which can be understood better by search engines, or in image format', 'zip-recipes' ),
			),

			'import_nutrition_data_all_recipes' => array(
				'type'      => 'checkbox',
				'source'    => 'nutrition',
				'disabled'  => true,
				'table'     => false,
				'label'     => __( 'Import nutrition data for all recipes', 'zip-recipes' ),
				'help'     => __( 'This will process in the background, and may take a while. It will pause if you close the browser, or disable the setting.', 'zip-recipes' ),
			),

			'RecipeActions' => array(
				'type'      => 'checkbox',
				'source'    => 'plugins',
				'is_plugin' => true,
				'disabled'  => true,
				'table'     => false,
				'label'     => __( 'Social Recipe sharing', 'zip-recipes' ),
				'comment'   => sprintf(__("Check out %spremium%s to see our recipe sharing features", "zip-recipes"), '<a target="_blank" href="https://ziprecipes.net/premium">', '</a>'),

			),

			'add_print_button' => array(
				'type'      => 'checkbox',
				'source'    => 'actions',
				'table'     => false,
				'default'   => true,
				'label'     => __( "Add Print Button", 'zip-recipes' ),
			),

			'recipe_action_yummly' => array(
				'type'      => 'checkbox',
				'source'    => 'actions',
				'disabled'    => 'true',
				'reload_on_change'      => true,
				'table'     => false,
				'label'     => sprintf( __( 'Add %s sharing button',
					'zip-recipes' ), 'Yummly' ),
			),

			'recipe_action_bigoven' => array(
				'type'      => 'checkbox',
				'source'    => 'actions',
				'disabled'    => 'true',
				'reload_on_change'      => true,
				'table'     => false,
				'label'     => sprintf( __( 'Add %s sharing button',
					'zip-recipes' ), 'BigOven' ),
			),

			'recipe_action_pinterest' => array(
				'type'      => 'checkbox',
				'source'    => 'actions',
				'table'     => false,
				'reload_on_change'      => true,
				'disabled'    => 'true',
				'label'     => sprintf( __( 'Add %s sharing button',
					'zip-recipes' ), 'Pinterest' ),
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
				//'default'   => true,
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
				//'default'     => false,
				'condition' => array(
					'VisitorRating' => false,
				),
				'label'     => __( 'Recipe Reviews', 'zip-recipes' ),
			),

//			'Import' => array(
//				'type'      => 'checkbox',
//				'source'    => 'plugins',
//				'is_plugin' => true,
//				'disabled'  => true,
//				'table'     => false,
//				'label'     => __( 'Import', 'zip-recipes' ),
//			),

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

			'use_zip_css' => array(
				'type'      => 'checkbox',
				'source'    => 'advanced',
				'default'    => true,
				'table'     => false,
				'label'     => __( "Use Zip Recipes style",
					'zip-recipes' ),
			),

			'restart_tour' => array(
				'type'      => 'button',
				'source'    => 'advanced',
				'post_get'    => 'get',
				'action'    => 'zrdn_restart_tour',
				'default'    => true,
				'table'     => false,
				'label'     => __( "Restart tour",
					'zip-recipes' ),
			),

			'import_ratings_to_reviews' => array(
				'type'      => 'checkbox',
				'source'    => 'advanced',
				'disabled'    => true,
				'default'    => true,
				'table'     => false,
				'label'     => __( "Import ratings to reviews", 'zip-recipes' ),
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
	 * Get list type for certain list style
	 * @param string $list_style
	 *
	 * @return string
	 */

    public static function get_list_type($list_style){
    	if (strpos($list_style, 'numbers')!== FALSE ) {
    		return 'ol';
	    } else {
    		return 'ul';
	    }
    }

	/**
     * Get a demo recipe id. Fall back to random recipe
     *
	 * @return int
	 */
    public static function get_demo_recipe_id(){
	    $demo_recipe_id = get_option('zrdn_demo_recipe_id');
	    if (!$demo_recipe_id){
		    $recipes = Util::get_recipes( array() );
		    if ( count( $recipes ) > 0 ) {
			    $demo_recipe_id = $recipes[0]->recipe_id;
		    }
	    }

	    return $demo_recipe_id;
    }

	/**
	 * Get a preview post id, optionally with a shortcode for a recipe
	 * @param int $recipe_id
	 *
	 * @return int|\WP_Error
	 */

    public static function get_preview_post_id( $recipe_id ){
	    $preview_post_id = get_option('zrdn_preview_post_id');

	    //make sure it exists. Otherwise we need to create a new one.
	    $post = get_post( $preview_post_id );
	    //if not, create one.
	    if (!$post) {
		    $page = array(
			    'post_title'   => __("Zip Recipes preview post (do not delete)", "zip-recipes"),
			    'post_type'    => "post",
			    'post_status'  => 'private',
			    'post_content'  => __("Save your recipe to see the preview", "zip-recipes"),
		    );

		    // Insert the post into the database
		    $preview_post_id = wp_insert_post( $page );
		    update_option('zrdn_preview_post_id', $preview_post_id);
	    }

	    //if it's trashed, restore it.
	    if ( get_post_status( $post ) === 'trash' ) {
		    $updated_post = array(
			    'post_title'   => __("Zip Recipes preview post (do not delete)", "zip-recipes"),
			    'ID'    => $post->ID,
			    'post_status'  => 'private',
		    );
		    wp_update_post($updated_post);
        }

	    //set post content to current recipe
	    if ( $recipe_id !== false ) {
		    $shortcode = Util::get_shortcode($recipe_id);
		    $args = array(
			    'post_content' => $shortcode,
			    'ID'           => $preview_post_id,
		    );
		    wp_update_post( $args );
	    }
	    return $preview_post_id;
    }

    /**
	 * Get list class for certain list style
	 * @param string $list_style
	 *
	 * @return string
	 */

    public static function get_list_class($list_style){
    	$class = '';
    	if ($list_style==='nobullets' || $list_style === 'numbers' || $list_style === 'bullets') {
    		$class = $list_style;
	    } else {
		    if ( strpos( $list_style, 'numbers' ) !== false ) {
			    $class .= ' zrdn-numbered';
		    }
		    if ( strpos( $list_style, 'border' ) !== false ) {
			    $class .= ' zrdn-bordered';
		    }
		    if ( strpos( $list_style, 'circle' ) !== false ) {
			    $class .= ' zrdn-round';
		    }
		    if ( strpos( $list_style, 'square' ) !== false ) {
			    $class .= ' zrdn-square';
		    }
		    if ( strpos( $list_style, 'solid' ) !== false ) {
			    $class .= ' zrdn-solid';
		    }
		    if ( strpos( $list_style, 'bullets' ) !== false ) {
			    $class .= ' zrdn-bullets';
		    }
		    if ( strpos( $list_style, 'counter' ) !== false ) {
			    $class .= ' zrdn-counter';
		    }
	    }
		return $class;
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
		if ($plugin === 'structured-data') return true;

		if (isset($fields[$plugin])){
			return Util::get_option($plugin);
		}

		return false;
	}

	/**
	 * Check if an array of blocks contains the type
	 * @param $blocks
	 * @param $blocktype
	 *
	 * @return bool
	 */

	public static function template_contains_block($blocks, $blocktype){
		$blocks_with_settings = array_filter($blocks, function ($var) use(&$blocktype) {
			$blocktypes = array();
			if (isset($var['blocks'])){
				$blocktypes = array_column($var['blocks'], 'type');
			}
			return (in_array($blocktype, $blocktypes));
		});
		return !empty($blocks_with_settings);
	}

	/**
	 * Insert block into array
	 * @param $blocks
	 * @param $new_block
	 *
	 * @return array
	 */
	public static function add_block_to_array($blocks, $new_block) {
		$index = array_search($new_block['type'], array_column($blocks, 'type'));
		if ($index) {
			$blocks[$index] = $new_block;
		} else {
			$blocks[] = $new_block;
		}
		return $blocks;
	}

	/**
	 * remove block of certain type from template array
	 * @param array $recipe_blocks_layout
	 * @param string $type_to_remove
	 *
	 * @return array
	 */

	public static function remove_block_from_array($recipe_blocks_layout, $type_to_remove ) {
		//for each block, find the block in the "all" list, and get it's data
		foreach($recipe_blocks_layout as $index => $column_blocks ) {
			if (isset($column_blocks['blocks']) && is_array($column_blocks['blocks'])) {
				$index_in_sub_array = array_search( $type_to_remove, array_column( $column_blocks['blocks'], 'type' ) );
				if ( $index_in_sub_array !== false ) {
					unset($recipe_blocks_layout[ $index ]['blocks'][ $index_in_sub_array ]);
				}
				//if no blocks left, set previous or next one to 100 if this is a 50 block, and remove this one
				if ( count($column_blocks['blocks']) === 0 ){
					if ( $column_blocks['type'] === 'block-50' ) {
						if ($index % 2 == 0) {
							$recipe_blocks_layout[$index+1]['type'] = 'block-100';
						} else {
							$recipe_blocks_layout[$index-1]['type'] = 'block-100';
						}
					}
					unset($recipe_blocks_layout[$index]);
				}
			}
		}
		return $recipe_blocks_layout;
	}

	/**
	 * Get social SVG with color
	 * @param $service
	 * @param $type
	 * @param $color
	 *
	 * @return string
	 */

	public static function get_social_svg($service, $type, $color ){
		$svg = file_get_contents(trailingslashit(ZRDN_PATH)."images/social-$type-$service.svg");
		return str_replace('{color}', $color, $svg);
	}

	/**
	 * Migrate a setting
	 * @param string $source
	 * @param string $new_source
	 * @param string $fieldname
	 * @param string|bool $new_fieldname
	 * @param bool $invert
	 */
	public static function migrate_setting($source, $new_source, $fieldname, $new_fieldname = false, $invert = false) {
		if (!current_user_can('manage_options')) return;

		if (!$new_fieldname) $new_fieldname = $fieldname;

		//get old setting
		$settings = get_option("zrdn_settings_$source", array());

		$new_settings = get_option("zrdn_settings_$new_source", array());
		if ( isset($settings[$fieldname]) ){
			$value = $settings[$fieldname];
			if ($invert && is_bool($value)) $value = !$value;
			$new_settings[$new_fieldname] = $value;
			update_option("zrdn_settings_$new_source", $new_settings );
		}
	}

	/**
     * Get old setting
	 * @param string $fieldname
	 * @param string $type
	 *
	 * @return mixed
	 */
	public static function get_old_setting($fieldname, $type){
		$old_image_options = get_option( "zrdn_settings_$type");
		return isset($old_image_options[$fieldname]) ? $old_image_options[$fieldname] : false;
    }

	/**
	 * Get the value for a ZRDN field
	 * @param string $name
	 *
	 * @return bool|mixed
	 */

    public static function get_option($fieldname){
        if($fieldname == 'all'){
	        //get all sources
            $sources = Util::grid_items();
            $sources = array_column($sources, 'source');
	        $sources[] = 'template';

            //check if we have blocks with settins
            $all_blocks = ZipRecipes::all_available_blocks();
	        $blocks_with_settings = array_filter($all_blocks, function ($var) {
		        return (isset($var['settings']) && $var['settings'] == true);
	        });
	        $block_sources = array_column($blocks_with_settings, 'type');
	        $sources = array_merge($sources , $block_sources);
	        $zrdn_settings = array();

            //foreach source get settings
            foreach($sources as $source){
	            $fields = Util::get_fields($source);
	            foreach ($fields as $fieldname => $field_data ) {
		            $zrdn_settings[$fieldname] = Util::get_option($fieldname);
	            }
            }

	        $zrdn_settings['amp_on'] = function_exists('is_amp_endpoint') ? is_amp_endpoint() : false;
	        return $zrdn_settings;
        }

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
	 * Get a youtube thumb url
	 * @param $src
	 *
	 * @return bool|string
	 */

	public static function youtube_thumbnail( $src ) {
		$thumbnail = false;
		$youtube_pattern
			= '/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/videoseries\?list=RD|embed\/|watch\?v=)([^#\&\?]*).*/i';
		if ( preg_match( $youtube_pattern, $src, $matches ) ) {
			$youtube_id = $matches[1];
			//check if it's a video series. If so, we get the first video
			if ($youtube_id === 'videoseries') {
				//get the videoseries id
				$series_pattern = '/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/videoseries\?list=RD|embed\/|watch\?v=)[^#\&\?]*\?list=(.*)/i';
				//if we find the unique id, we save it in the cache
				if ( preg_match( $series_pattern, $src, $matches ) ) {
					$series_id = $matches[1];

					$youtube_id = get_transient("zrdn_youtube_videoseries_video_id_$series_id");
					if (!$youtube_id){
						//we do a get on the url to retrieve the first video
						$youtube_id = Util::youtube_get_video_id_from_series($src);
						set_transient( "zrdn_youtube_videoseries_video_id_$series_id", $youtube_id,
							WEEK_IN_SECONDS );
					}
				} else{
					$youtube_id = Util::youtube_get_video_id_from_series($src);
				}
			}
			/*
			 * The highest resolution of youtube thumbnail is the maxres, but it does not
			 * always exist. In that case, we take the hq thumb
			 * To lower the number of file exists checks, we cache the result.
			 *
			 * */
			$thumbnail = get_transient( "zrdn_youtube_image_$youtube_id" );
			if ( ! $thumbnail || ! file_exists( $thumbnail ) ) {
				$thumbnail
					= "https://img.youtube.com/vi/$youtube_id/maxresdefault.jpg";
				if ( ! Util::remote_file_exists( $thumbnail ) ) {

					$thumbnail
						= "https://img.youtube.com/vi/$youtube_id/hqdefault.jpg";
				}
				set_transient( "zrdn_youtube_image_$youtube_id", $thumbnail,
					WEEK_IN_SECONDS );
			}
		}

		return $thumbnail;
	}


	/**
	 * Get the first video id from a video series
	 *
	 * @param string $src
	 *
	 * @return string
	 */

	public static function youtube_get_video_id_from_series($src){
		$output = wp_remote_get($src);
		$youtube_id = false;
		if (isset($output['body'])) {
			$body = $output['body'];
			$body = stripcslashes($body);
			$series_pattern = '/VIDEO_ID\': "([^#\&\?].*?)"/i';
			if ( preg_match( $series_pattern, $body, $matches ) ) {
				$youtube_id = $matches[1];
			}
		}
		return $youtube_id;
	}

	/**
	 * Get list of cuisines
	 * @return array
	 */
	public static function get_cuisines(){
        global $wpdb;
		$table = $wpdb->prefix . "amd_zlrecipe_recipes";
		return wp_list_pluck((array) $wpdb->get_results("SELECT DISTINCT cuisine from $table WHERE cuisine != '' "), 'cuisine');
	}

	/**
     * Get list of categories connected to a recipe
	 * @return array
	 */

	public static function get_recipe_categories(){
		$all_categories = get_transient('zrdn_recipe_categories');
		if (!$all_categories) {
			$args = array(
				'post_status'=>'publish',
				'number' => -1,
			);
			$recipes = Util::get_recipes($args);
			$all_categories = array();
			$cats = array();
			foreach ($recipes as $index => $recipe) {
				$recipe = new Recipe($recipe->recipe_id);

				$post_categories = wp_get_post_categories($recipe->post_id);
				foreach ($post_categories as $c) {
					$cat = get_category($c);
					if (!isset($all_categories[$cat->slug])){
						$all_categories[$cat->slug] = array(
							'id' => $cat->cat_ID,
							'name' => $cat->name,
							'count' => $cat->category_count,
						);
					}

				}

			}
			set_transient('zrdn_recipe_categories', $all_categories, HOUR_IN_SECONDS);
		}

		return $all_categories;
    }



	public static function remote_file_exists( $url ) {
	    if ( !function_exists('curl_version') ) return false;

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		// don't download content
		curl_setopt( $ch, CURLOPT_NOBODY, 1 );
		curl_setopt( $ch, CURLOPT_FAILONERROR, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		$result = curl_exec( $ch );
		curl_close( $ch );
		if ( $result !== false ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update option
	 * @param string $fieldname
	 * @param mixed $value
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
		if (!is_array($zrdn_settings)) $zrdn_settings = array();
		$field = ZipRecipes::$field;
		$old_value = isset($zrdn_settings[$fieldname]) ? $zrdn_settings[$fieldname] : false;
		$zrdn_settings[$fieldname] = apply_filters('zrdn_update_option', $field::sanitize($fieldname, $new_value), $old_value, $fieldname, $source);

		do_action('zrdn_update_option', $new_value, $old_value, $fieldname, $source);
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
            'cuisine' => '',
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

        if ( $args['cuisine'] !=='' ) {
	        $search_sql .= " AND ( cuisine = '".$args['cuisine']."')";
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

	/**
	 * Insert an element into an array at a position
	 * @param int $position
	 * @param array $array
	 * @param array $element
	 *
	 * @return array
	 */
    public static function insert_into_array($position, $array, $element){
	    $array = array_slice($array, 0, $position, true) +
	    $element +
	    array_slice($array, $position, count($array)-$position, true);
	    return $array;
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
