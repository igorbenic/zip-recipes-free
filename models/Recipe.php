<?php
/**
 * Created by PhpStorm.
 * User: gezimhome
 * Date: 2018-01-01
 * Time: 10:37
 */

namespace ZRDN;

add_action('admin_init', __NAMESPACE__. '\zrdn_update_recipe_table');
function zrdn_update_recipe_table(){

    if (get_option('zrdn_table_version', 1)!==ZRDN_VERSION_NUM){
        global $wpdb;

        $recipes_table = $wpdb->prefix . Recipe::TABLE_NAME;
        $charset_collate = Util::get_charset_collate();

        // Each column for create table statement is an array element
        $columns = array(
            'recipe_id bigint(20) unsigned NOT NULL AUTO_INCREMENT  PRIMARY KEY',
            'post_id bigint(20) unsigned NOT NULL',
            'nutrition_label_id bigint(20) unsigned NOT NULL',
            'recipe_image_id bigint(20) unsigned NOT NULL',
            'json_image_1x1_id bigint(20) unsigned NOT NULL',
            'json_image_4x3_id bigint(20) unsigned NOT NULL',
            'json_image_16x9_id bigint(20) unsigned NOT NULL',
            'json_image_1x1 text',
            'json_image_4x3 text',
            'json_image_16x9 text',
            'recipe_title text',
            'recipe_image text',
            'summary text',
            'prep_time text',
            'cook_time text',
            'wait_time text',
            'yield text',
            'serving_size varchar(50)',
            'calories varchar(50)',
            'fat varchar(50)',
            'carbs varchar(50)',
            'protein varchar(50)',
            'fiber varchar(50)',
            'sugar varchar(50)',
            'saturated_fat varchar(50)',
            'sodium varchar(50)',
            'vitamin_a varchar(50)',
            'vitamin_c varchar(50)',
            'iron varchar(50)',
            'calcium varchar(50)',
            'ingredients text',
            'ingredients_alt text',
            'enable_ingredients_alt varchar(50)',
            'instructions text',
            'notes text',
            'category varchar(100)',
            'cuisine varchar(50)',
            'trans_fat varchar(50)',
            'cholesterol varchar(50)',
            'is_featured_post_image tinyint(1) NOT NULL DEFAULT 0',
            'created_at timestamp DEFAULT NOW()',
            'author varchar(50)',
            'video_url varchar(255)',
            'non_food int(11)',
	        'hits int(11)',
        );

        $all_columns = apply_filters('zrdn__db_recipe_columns', $columns);

        // For dbDelta to detect different columns, they have to be split using \n (newline).
        // The comma is part of SQL syntax.
        $columns_string = implode(",\n", $all_columns);
        $sql_command = "CREATE TABLE `$recipes_table` ($columns_string) $charset_collate;";

        /**
         * dbDelta is smart enough not to make changes if they're not needed so we don't need to have table
         *  version checks.
         * Also, dbDelta will not drop columns from a table, it only adds new ones.
         */
        if (!function_exists('dbDelta')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        }
        dbDelta($sql_command); // run SQL script

        do_action("zrdn__db_setup", Recipe::TABLE_NAME);
        update_option('zrdn_table_version', ZRDN_VERSION_NUM);
    }
}



class Recipe {

    /**
     * @var string
     */
    const TABLE_NAME = "amd_zlrecipe_recipes";
	/**
	 * Recipe constructor.
	 *
	 * @param int $recipe_id
	 * @param $post_id
	 */
	public function __construct($recipe_id=null, $post_id=null, $title='', $image_url='') {

		if ($post_id) {
			$this->post_id = $post_id;
		}
		if ($recipe_id !== false ) {
			$this->recipe_id = $recipe_id;
		}
		if ($title) {
			$this->recipe_title = $title;
		}
		if ($image_url) {
			$this->recipe_image = $image_url;
		}

		//in preview, we should load placeholders if no recipe id is known
		if ( !$this->recipe_id && isset($_GET['mode']) && $_GET['mode'] === 'zrdn-preview') {
			$this->load_placeholders();
		} else if ($this->recipe_id || $this->post_id) {
			$this->load();
		}
	}

	/**
	 * @var int
	 */
	public $recipe_id;
    /**
     * @var bool
     */
    public $is_placeholder = false;
	/**
	 * @var int
	 */
	public $post_id;

	/**
	 * @var string
	 */
	public $recipe_title;

    /**
     * @var string
     */
    public $permalink = false;

    /**
     * @var string
     */
    public $author;

    public $author_id;

    /**
     * @var string
     */
    public $video_url;
    public $video_url_output;

	/**
	 * @var string
	 */
	public $recipe_image;
	public $json_image_1x1;
	public $json_image_4x3;
	public $json_image_16x9;
    public $json_image_1x1_id;
    public $json_image_4x3_id;
    public $json_image_16x9_id;
    /**
     * JSON should always have an image, so if the normal image is not available we try to set it from the post
     * @var string
     */
	public $recipe_image_json;
	public $recipe_image_id;

	/**
	 * @var string
	 */
	public $summary;

	/**
	 * @var string
	 */
	public $prep_time;

    /**
     * @var string
     */
    public $prep_time_formatted;

	/**
	 * @var string
	 */
	public $cook_time;
    /**
     * @var string
     */
	public $cook_time_formatted;
	/**
	 * @var string
	 */
	public $wait_time;
	/**
	 * @var string
	 */
	public $wait_time_formatted;

	/**
	 * @var string
	 */
	public $total_time;

    /**
     * @var string
     */
    public $total_time_formatted;

    /**
     * @var string
     */
    public $recipe_rating;

	/**
	 * @var string
	 */
	public $yield;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $serving_size;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $calories;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $fat;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $carbs;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $protein;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $fiber;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $sugar;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $saturated_fat;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $sodium;

	/**
	 *
	 * @var string
	 */
	public $ingredients;
    /**
     *
     * @var string
     */
    public $ingredients_alt;

    /**
     *
     * @var bool
     */
    public $enable_ingredients_alt;


    /**
	 *
	 * @var string
	 */
	public $instructions;
	public $non_food;

	/**
	 *
	 * @var string
	 */
	public $notes;
	public $formatted_notes = '';

	/**
	 * varchar(100)
	 * @var string
	 */
	public $category;
	public $categories;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $cuisine;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $trans_fat;

	/**
	 * varchar(50)
	 * @var string
	 */
	public $cholesterol;

	/**
	 * This is a `timestamp` in DB. Not sure what's up.
	 * @var string
	 */
	public $created_at;

    /**
     * varchar(50)
     * @var string
     */
    public $vitamin_a;

    /**
     * varchar(50)
     * @var string
     */
    public $vitamin_c;

    /**
     * varchar(50)
     * @var string
     */
    public $iron;

    /**
     * varchar(50)
     * @var string
     */
    public $calcium;

    /**
     * varchar(250)
     * @var string
     */
    public $nutrition_label;

    /**
     * @var int
     */

    public $nutrition_label_id;

    public $is_featured_post_image = false;

    public $fat_daily;
    public $calories_daily;
	public $hits;
	public $saturated_fat_daily;
    public $cholesterol_daily;
    public $sodium_daily;
    public $carbs_daily;
    public $fiber_daily;
    public $has_nutrition_data = false;
    public $preview = false;
    public $summary_rich = '';
    public $nested_ingredients = array();
    public $nested_instructions = array();
    public $jsonld = '';
    public $keywords = array();


    /**
     * Get a recipe from the db
     *
     * @global \ZRDN\Array $wpdb
     * @param int $recipe_id
     * @return object $recipe
     */
    public static function db_select($recipe_id) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        $selectStatement = sprintf("SELECT * FROM `%s` WHERE recipe_id=%d", $table, $recipe_id);
        $recipe =  $wpdb->get_row($selectStatement);
        return $recipe;
    }


    /**
     * Get list of nutrition data only
     * @return array nutrition data
     */

    public function nutrition_data(){
        $nutrition_data = array(
            'yield' => $this->yield,
            'serving_size' => $this->serving_size,
            'calories' => $this->calories,
            'fat' => $this->fat,
            'carbs' => $this->carbs,
            'protein' => $this->protein,
            'fiber' => $this->fiber,
            'sugar' => $this->sugar,
            'saturated_fat' => $this->saturated_fat,
            'sodium' => $this->sodium,
            'trans_fat' => $this->trans_fat,
            'cholesterol' => $this->cholesterol,
            'vitamin_a' => $this->vitamin_a,
            'vitamin_c' => $this->vitamin_c,
            'iron' => $this->iron,
            'calcium' => $this->calcium,
            'nutrition_label' => $this->nutrition_label,
            'nutrition_label_id' => $this->nutrition_label_id,
            'fat_daily' => $this->fat_daily,
            'saturated_fat_daily' => $this->saturated_fat_daily,
            'cholesterol_daily' => $this->cholesterol_daily,
            'sodium_daily' => $this->sodium_daily,
            'carbs_daily' => $this->carbs_daily,
            'fiber_daily' => $this->fiber_daily,
            'has_nutrition_data' => $this->has_nutrition_data,
        );

        return $nutrition_data;
    }

    /**
     * Load the recipe
     */

    public function load(){
        $recipe_data = false;
        if ($this->recipe_id){
            $recipe_data = $this->db_select($this->recipe_id);
        }

        if (!$recipe_data && $this->post_id ){
            $recipe_data = $this->db_select_by_post_id($this->post_id);
            if ($recipe_data) $this->recipe_id = $recipe_data->recipe_id;
        }

        if ($recipe_data) {
            $db_recipe = get_object_vars($recipe_data);
            foreach ($this as $fieldname => $value) {
                if (isset($db_recipe[$fieldname])){
                    $value = $db_recipe[$fieldname];
                    $this->{$fieldname} = apply_filters('zrdn_field_value', $value, $fieldname, $this->recipe_id);
                }
            }
        }

        $custom_author = $this->author;
	    if ($this->post_id){
		    $post = get_post($this->post_id);
		    if ($post) {
			    $this->author_id = $post->post_author;
			    $user_data = get_userdata($this->author_id);
			    if ($user_data) {
				    $this->author = $user_data->display_name;
			    }
		    }
	    }

        $this->author = apply_filters('zrdn_author_value', $this->author, $custom_author, $this->recipe_id, $this->post_id);
        $this->author_id = apply_filters('zrdn_author_id', $this->author_id, $custom_author, $this->recipe_id, $this->post_id);

        $this->total_time = $this->calculate_total_time_raw($this->prep_time, $this->cook_time);

        //formatted times
        $this->cook_time_formatted = $this->format_duration($this->cook_time);
        $this->wait_time_formatted = $this->format_duration($this->wait_time);
        $this->prep_time_formatted = $this->format_duration($this->prep_time);
        $this->total_time_formatted = $this->format_duration($this->total_time);

        //Recipe permalink
        $this->permalink = get_permalink($this->post_id);

        //check if the connected post is a valid post
        $post = get_post($this->post_id);
        if (!$post || get_post_type($this->post_id)==='revision') $this->post_id = false;

        //check if we should load the nutrition label
        if ($this->nutrition_label_id>0){
            $img = wp_get_attachment_image_src($this->nutrition_label_id, 'full');
            if ($img) {
                $this->nutrition_label = $img[0];
            }
        }

        //backwards compatibility: update attachment id if it's not there
        if (!$this->recipe_image_id && strlen($this->recipe_image)>0){
            $recipe_image_id = attachment_url_to_postid($this->recipe_image);
            if (!$recipe_image_id) $recipe_image_id = get_post_thumbnail_id($this->post_id);
            $this->recipe_image_id = $recipe_image_id;
            $this->save();
        }

        //check if we should load the recipe image based on ID
        if ($this->recipe_image_id>0){
            $this->recipe_image = $this->get_image_url_by_size($this->recipe_image_id, 'zrdn_recipe_image_main');
        }

        //check if image is also featured image for connected post
        $this->is_featured_post_image = false;
        if ($this->post_id && Util::get_option('hide_on_duplicate_image')){
            $recipe_image_id = get_post_thumbnail_id( $this->post_id );
            if ($recipe_image_id == $this->recipe_image_id){
                $this->is_featured_post_image = true;
            }
        }

        //set a separate json image
        //if this recipe has an image, use it
	    if (!empty($this->recipe_image)){
		    $this->recipe_image_json = $this->generate_recipe_image_json($this->recipe_image_id);
	    } elseif ($this->post_id>0){
		    $post_thumbnail_id = get_post_thumbnail_id($this->post_id);
		    $this->recipe_image_json = $this->generate_recipe_image_json($post_thumbnail_id);
	    } else {
		    $this->recipe_image_json = $this->generate_recipe_image_json();
	    }

        /**
         * Load the category from the post
         */

        $post_categories = wp_get_post_categories($this->post_id);
        $cats = array();
        $all_from_post = true;
        foreach ($post_categories as $c) {
            $cat = get_category($c);
            if (!term_exists($c) ) {
            	continue;
            }

            $cats[] = $cat->name;
	        $this->categories[] = $cat->term_id;
            //if all categories are loaded from wordpress, we leave the categories string blank.
            if (strpos( $this->category, $cat->name) === FALSE) {
	            $all_from_post = false;
            }
        }
		if (strlen($this->category)===0) $all_from_post = true;
        $this->category = implode(', ',$cats);
	    if ($all_from_post){
		    $this->category = '';
	    }

        /**
         * get daily values, which are based on other, stored values.
         */
        $this->fat_daily = self::calculate_daily_value('fat', $this->fat);
        $this->saturated_fat_daily = self::calculate_daily_value('saturated_fat', $this->saturated_fat);
        $this->cholesterol_daily = self::calculate_daily_value('cholesterol', $this->cholesterol);
        $this->sodium_daily = self::calculate_daily_value('sodium', $this->sodium);
        $this->carbs_daily = self::calculate_daily_value('carbs', $this->carbs);
        $this->fiber_daily = self::calculate_daily_value('fiber', $this->fiber);
        $this->calories_daily = self::calculate_daily_value('calories', $this->calories);
        $this->has_nutrition_data = false;
        if (
            $this->calories != null ||
            $this->fat != null ||
            $this->carbs != null ||
            $this->protein != null ||
            $this->fiber != null ||
            $this->sugar != null ||
            $this->saturated_fat != null ||
            $this->cholesterol != null ||
            $this->sodium != null ||
            $this->trans_fat != null
        ) {
            $this->has_nutrition_data = true;
        }

        if ( strlen($this->video_url) ) {
	        $this->video_url_output = ( strpos( $this->video_url, '_value' ) !== false ) ? $this->video_url : wp_oembed_get( $this->video_url );
        }
	    $this->formatted_notes = $this->richify_item($this->zrdn_format_image($this->notes), 'notes');
	    $this->summary_rich =  $this->richify_item($this->zrdn_format_image($this->summary), 'summary');
	    $this->nested_ingredients = $this->get_nested_items($this->ingredients);
	    $this->nested_instructions = $this->get_nested_items($this->instructions);

	    //check if post_id is a revision
        if ($this->post_id && get_post_type($this->post_id) === 'revision'){
            $this->post_id = false;
        }

	    $this->jsonld = json_encode( $this->jsonld() );
    }

    /**
     * Get url of an image by size. If fallback is true, we use the full size as fallback size
     * @param int $image_id
     * @param string $size
     * @param bool $fallback
     * @return string $url
     */

    public function get_image_url_by_size($image_id, $size, $fallback=true){
        $url = false;
        $img = wp_get_attachment_image_src($image_id, $size);

        if ($img) {
            $url = $img[0];
            $is_original = !$img[3];
            if (!$fallback && $is_original) return false;
        }
        return $url;
    }

    /**
     * Get json for images.
     * @param int|bool $image_id
     * @return array|string image_json
     */

	public function generate_recipe_image_json($image_id=false){

		$image_1x1 = $image_4x3 = $image_16x9 = false;

		//load in object
		if ($this->json_image_1x1_id==0 || $this->json_image_1x1_id===$image_id) {
			if ($image_id>0) {
				//we first try to get three different ratio's
				$image_1x1 = $this->get_image_url_by_size($image_id, 'zrdn_recipe_image_json_1x1', false);
				if (!$image_1x1) $image_1x1 = $this->get_image_url_by_size($image_id, 'zrdn_recipe_image_json_1x1_s', false);
			}

			if ($image_1x1) $this->json_image_1x1 = $image_1x1;
		}
		if ($this->json_image_4x3_id==0 || $this->json_image_4x3_id===$image_id) {
			if ($image_id>0) {
				//we first try to get three different ratio's
				$image_4x3 = $this->get_image_url_by_size($image_id, 'zrdn_recipe_image_json_4x3', false);
				if (!$image_4x3) $image_4x3 = $this->get_image_url_by_size($image_id, 'zrdn_recipe_image_json_4x3_s', false);
			}

			if ($image_4x3) $this->json_image_4x3 = $image_4x3;
		}

		if ($this->json_image_16x9_id==0 || $this->json_image_16x9_id===$image_id) {
			if ($image_id>0) {
				//we first try to get three different ratio's
				$image_16x9 = $this->get_image_url_by_size($image_id, 'zrdn_recipe_image_json_16x9', false);
				if (!$image_16x9) $image_16x9 = $this->get_image_url_by_size($image_id, 'zrdn_recipe_image_json_16x9_s', false);

			}
			if ($image_16x9) $this->json_image_16x9 = $image_16x9;
		}

		if ($this->json_image_1x1 && $this->json_image_4x3 && $this->json_image_16x9) {
			return array(
				$this->json_image_1x1,
				$this->json_image_4x3,
				$this->json_image_16x9,
			);
		} else {
			//no ratio sizes found, so just return the image url as json image.
			return $this->recipe_image;
		}
	}

    /**
     * Load recipe with placeholders instead of actual data
     */

    public function load_placeholders(){

        $this->is_placeholder = true;
        $recipe = get_object_vars($this);

        foreach ($recipe as $fieldname => $value) {
        	if ($fieldname === 'recipe_image_id' ) continue;
        	if (!$this->{$fieldname}) $this->{$fieldname} = '<span class="'.$fieldname . '_value">&nbsp;</span>';
        }
        $this->has_nutrition_data = true;
        $actual_recipe_id = false;
        $actual_post_id = false;
        $this->total_time = NULL;
        $this->author = apply_filters( 'zrdn_author_value', false, $actual_recipe_id, $actual_post_id);

        if (!$this->prep_time) $this->prep_time = 'PT99H99M';
        if (!$this->cook_time) $this->cook_time = 'PT99H99M';
        if (!$this->wait_time) $this->wait_time = 'PT99H99M';
        if (!$this->keywords) $this->keywords = array('{keywords_value}' ,'{keywords_value}');

        if (!$this->nutrition_label) $this->nutrition_label = ZRDN_PLUGIN_URL . '/images/s.png';
	    $this->is_featured_post_image = false;

	    if (!$this->recipe_image_id) {
	    	$this->recipe_image = ZRDN_PLUGIN_URL.'/images/recipe-default-bw.png';
	    	$this->recipe_image_id = 0;
	    }

        //get random recipe id for some functionality, like ratings
        if (!$actual_recipe_id) {
            global $wpdb;
            $table = $wpdb->prefix . self::TABLE_NAME;
            $recipe_id = $wpdb->get_var("SELECT max(recipe_id) FROM $table");
        } else {
            $recipe_id = $actual_recipe_id;
        }
        $this->preview = true;
        $this->recipe_id = $recipe_id;
	    $this->nested_ingredients = $this->get_nested_items($this->ingredients);
	    $this->nested_instructions = $this->get_nested_items($this->instructions);

	    if ($this->post_id) {
		    $this->keywords = get_the_tags($this->post_id);
	    } else {
		    $this->keywords = array();
	    }
    }

	/**
	 * Load defaults for the template editor
	 */

    public function load_default_data(){
	    $this->recipe_image = ZRDN_PLUGIN_URL.'/images/demo-recipe.jpg';
	    $this->recipe_image_id = 0;
	    $this->recipe_title = __( 'Creme Brulee', 'zip-recipes' );
	    $this->permalink = site_url();
		$this->preview = false;

	    $this->author_id = get_current_user_id();
	    $user_data = get_userdata($this->author_id);
	    if ($user_data) {
		    $this->author = $user_data->display_name;
	    }

	    $this->video_url = 'https://www.youtube.com/embed/FKqN8wNBbcI';
	    $this->video_url_output = '<iframe width="1120" height="630" src="https://www.youtube.com/embed/FKqN8wNBbcI" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>';
	    $this->summary_rich = $this->summary = __("This is a great recipe for all occasions", "zip-recipes");
	    $this->prep_time = 'PT0H20M';
	    $this->cook_time = 'PT0H10M';
	    $this->wait_time = 'PT0H10M';
	    $this->total_time = 'PT0H30M';
	    $this->cook_time_formatted = $this->format_duration($this->cook_time);
	    $this->wait_time_formatted = $this->format_duration($this->wait_time);
	    $this->prep_time_formatted = $this->format_duration($this->prep_time);
	    $this->total_time_formatted = $this->format_duration($this->total_time);

	    $this->recipe_rating = 5;
	    $this->yield = '4';
	    $this->serving_size = __("1 bowl", "zip-recipes");
	    $this->calories = '406.74 kcal';
	    $this->fat = '41.41 g';
	    $this->carbs = '4.3 g';
	    $this->protein = '5.26 g';
	    $this->fiber = '0';
	    $this->sugar = '3.79 g';
	    $this->saturated_fat = '24.72 g';
	    $this->sodium = '121.49 mg';
	    $this->trans_fat = '';
	    $this->cholesterol = '315.29 mg';

	    $this->vitamin_a = '44.17 %';
	    $this->vitamin_c = '0.8 %';
	    $this->iron = '2.76 %';
	    $this->calcium = '10.44 %';

	    $this->fat_daily = self::calculate_daily_value('fat', $this->fat);
	    $this->saturated_fat_daily = self::calculate_daily_value('saturated_fat', $this->saturated_fat);
	    $this->cholesterol_daily = self::calculate_daily_value('cholesterol', $this->cholesterol);
	    $this->sodium_daily = self::calculate_daily_value('sodium', $this->sodium);
	    $this->carbs_daily = self::calculate_daily_value('carbs', $this->carbs);
	    $this->fiber_daily = self::calculate_daily_value('fiber', $this->fiber);
	    $this->calories_daily = self::calculate_daily_value('calories', $this->calories);
	    $this->has_nutrition_data = true;

	    $this->summary_rich = $this->summary = __("Easy to make, but with a big wow factor", "zip-recipes");
	    $this->fat_daily;
	    $this->calories_daily;
	    $this->saturated_fat_daily;
	    $this->cholesterol_daily;
	    $this->sodium_daily;
	    $this->carbs_daily;
	    $this->fiber_daily;
	    $this->ingredients = '2 cups heavy or light cream, or half-and-half'."\n".
		                    '1 vanilla bean, split lengthwise, or 1 teaspoon vanilla extract'."\n".
		                    '1/8 teaspoon salt'."\n".
		                    '5 egg yolks'."\n".
		                    '1/2 cup sugar, more for topping'."\n";

	    $this->instructions =   'In a saucepan, combine cream, vanilla bean and salt and cook over low heat just until hot.'."\n".
								'In a bowl, beat yolks and sugar together until light.'."\n".
								'Pour into four 6-ounce ramekins and place ramekins in a baking dish'."\n".
								'Fill dish with boiling water halfway up the sides of the dishes'."\n".
								'Place ramekins in a broiler 2 to 3 inches from heat source'."\n";

        $this->notes = __("Cool completely! Refrigerate for several hours and up to a couple of days", "zip-recipes");
	    $this->nested_ingredients = $this->get_nested_items($this->ingredients);
	    $this->nested_instructions = $this->get_nested_items($this->instructions);
	    $this->formatted_notes =  $this->notes;
	    $this->category;
	    $this->categories = get_categories(array('number' => 2));
	    $this->cuisine = __('French', 'zip-recipes');
	    $this->created_at = time();

	    $this->keywords = get_tags(array('number' => 3));
	    if (empty($this->keywords)) {
		    $this->keywords = array(
			    $object = (object) [
				    'name' => __( 'Example tag 1', 'zip-recipes' ),
			    ],
			    $object = (object) [
				    'name' => __( 'Example tag 2', 'zip-recipes' ),
			    ],
			    $object = (object) [
				    'name' => __( 'Example tag 3', 'zip-recipes' ),
			    ],
		    );
	    }
    }

    /**
     * Save recipe to database
     */

    public function save(){
        if (!current_user_can('edit_posts')) return;
        global $wpdb;

        $table = $wpdb->prefix . self::TABLE_NAME;

        $update_arr = array(
            'post_id' => intval($this->post_id),
            'nutrition_label_id' => intval($this->nutrition_label_id),
            'recipe_title' => stripslashes(sanitize_text_field($this->recipe_title)),
            'author' => sanitize_text_field($this->author),
            'recipe_image' => sanitize_text_field($this->recipe_image),
            'recipe_image_id' => sanitize_text_field($this->recipe_image_id),
            'json_image_1x1' => sanitize_text_field($this->json_image_1x1),
            'json_image_4x3' => sanitize_text_field($this->json_image_4x3),
            'json_image_16x9' => sanitize_text_field($this->json_image_16x9),
            'json_image_1x1_id' => intval($this->json_image_1x1_id),
            'json_image_4x3_id' => intval($this->json_image_4x3_id),
            'json_image_16x9_id' => intval($this->json_image_16x9_id),
            'summary' => stripslashes(wp_kses_post($this->summary)),
            'prep_time' => Util::validate_time($this->prep_time),
            'cook_time' => Util::validate_time($this->cook_time),
            'wait_time' => Util::validate_time($this->wait_time),
            'yield' => sanitize_text_field($this->yield),
            'serving_size' => stripslashes(sanitize_text_field($this->serving_size)),
            'calories' => sanitize_text_field($this->calories),
            'fat' => sanitize_text_field($this->fat),
            'carbs' => sanitize_text_field($this->carbs),
            'protein' => sanitize_text_field($this->protein),
            'fiber' => sanitize_text_field($this->fiber),
            'sugar' => sanitize_text_field($this->sugar),
            'saturated_fat' => sanitize_text_field($this->saturated_fat),
            'sodium' => sanitize_text_field($this->sodium),
            'ingredients' => stripslashes(wp_kses_post($this->ingredients)),
            'ingredients_alt' => stripslashes(wp_kses_post($this->ingredients_alt)),
            'enable_ingredients_alt' => sanitize_title($this->enable_ingredients_alt),
            'instructions' => stripslashes(wp_kses_post($this->instructions)),
            'notes' => stripslashes(wp_kses_post($this->notes)),
            'category' => stripslashes(sanitize_text_field($this->category)),
            'cuisine' => stripslashes(sanitize_text_field($this->cuisine)),
            'trans_fat' =>sanitize_text_field( $this->trans_fat),
            'cholesterol' => sanitize_text_field($this->cholesterol),
            'vitamin_a' => sanitize_text_field($this->vitamin_a),
            'vitamin_c' => sanitize_text_field($this->vitamin_c),
            'calcium' => sanitize_text_field($this->calcium),
            'iron' => sanitize_text_field($this->iron),
            'video_url' => esc_url_raw($this->video_url),
            'non_food' => boolval($this->non_food),
        );

        //if an id was passed, we load the URL to keep it in sync with the new ID.
        if ($this->nutrition_label_id>0){
            //check if we should load the recipe
            $img = wp_get_attachment_image_src($this->nutrition_label_id, 'full');
            if ($img) {
                if (isset($img[0])) $this->nutrition_label = $img[0];
            }
        }

        //no recipe_id->create new.
        if (!$this->recipe_id){
            $wpdb->insert(
                $table,
                $update_arr
            );
            $this->recipe_id = $wpdb->insert_id;
        } else {
            $this->maybe_delete_old_nutrition_label();
            $wpdb->update(
                $table,
                $update_arr,
                array('recipe_id' => $this->recipe_id)
            );
        }
    }

    /**
     * Format an ISO8601 duration for human readibility
     *
     * @param $duration
     * @return string
     */
    public function format_duration($duration)
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

        try {
            $time = new \DateInterval($duration);
	        $minutes =  $time->i;
	        $hours = $time->h;
	        $hour_string = $minute_string = '';
	        if ($minutes >0 ) {
		        if ($minutes > 1 ) {
	        	    $minute_string = sprintf($date_abbr['i']['plural'], $minutes);
		        } else {
			        $minute_string = sprintf($date_abbr['i']['singular'], $minutes);
		        }
	        }
	        if ($hours >0 ) {
		        if ( $hours > 1 ) {
			        $hour_string = sprintf( $date_abbr['h']['plural'], $hours );
		        } else {
			        $hour_string = sprintf( $date_abbr['h']['singular'], $hours );
		        }
		        if (strlen($minute_string)>0) $hour_string .= ', ';
	        }
	        $string = $hour_string . $minute_string;
        } catch (\Exception $e) {
        }
        return $string;
    }

    /**
     * if the nutrition label id is changed, we delete the old one to prevent cluttering of database
     */

    private function maybe_delete_old_nutrition_label(){
        if (!current_user_can('edit_posts')) return;

        global $wpdb;

        $table = $wpdb->prefix . self::TABLE_NAME;
        //get current nutrition label, so we can remove the previous one
        $old_nutrition_label_id =  $wpdb->get_var($wpdb->prepare("SELECT nutrition_label_id FROM $table WHERE recipe_id=%s", intval($this->recipe_id)));
        if ($old_nutrition_label_id!=$this->nutrition_label_id){
            //delete old label before saving new one.
            wp_delete_attachment( $old_nutrition_label_id,true );
        }
    }

	/**
	 * track a recipe hit
	 */

	public function track_hit(){
		if (!$this->recipe_id) return;
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE_NAME;
		$this->hits++;
		$wpdb->update(
			$table,
			array(
				'hits' => $this->hits,
			),
			array('recipe_id' => $this->recipe_id)
		);

	}



    /**
     * Get % from daily value
     *
     * @param string $type
     * @param string $value
     * @return string $daily_value in %
     */

    public function calculate_daily_value($type, $value){
        //in case of preview mode, return default value
        if (strpos($value, '_value')!==FALSE){
            return str_replace("_value", "_value_daily", $value);
        }

        //get number value
        $value = str_replace(array(' ', 'mg', 'g', 'µg', 'kcal'), '', $value);

        $daily_values = array(
            'fat' => 65, //g
            'saturated_fat' => 20, //g
            'cholesterol'=>300, //mg
            'sodium'=>2400, //mg
            'carbs'=>300,//g
            'fiber'=>25, //g
            'vitamin_a'=>1000, //re
            'vitamin_c'=>60, //mg
            'iron'=>14,//mg
            'calcium'=>1100,//mg
            'calories'=>2400,//mg
        );

        if (empty($value) || $value==0) return 0;
        if (!isset($daily_values[$type])) return 0;

        $daily_value = $daily_values[$type];
        $p = round(($value/$daily_value)*100,1).'%';
        return $p;
    }


    /**
     * Get a recipe from the db by post_id
     *
     * @global \ZRDN\Array $wpdb
     * @param int $post_id
     * @return object $recipe
     */
    public static function db_select_by_post_id($post_id) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        $selectStatement = sprintf("SELECT * FROM %s WHERE post_id=%s", $table, intval($post_id));
        $recipe =  $wpdb->get_row($selectStatement);
        return $recipe;
    }

	/**
	 * Get formatted items (ingredients or instructions) in a nested array (see return).
	 * @param $items string Unformatted string of ingredients or instructions.
	 *
	 * @return array Nested array with formatted items for each sublist. E.g.:
	 *  [
	 *      ["4 skinless, boneless chicken breast halves", "1 1/2 tablespoons vegetable oil"]
	 *      ["subtitle for second part", "4g of onions", "5g of beans"]
	 * ]
	 */
	public function get_nested_items($items)
	{
		$nested_list = array();
		if (!$items) {
			return array();
		}

		$raw_items = explode("\n", $items);
		foreach ($raw_items as $raw_item) {
			// don't add items that are empty
			if (strlen(trim($raw_item)) < 1) {
				continue;
			}
			$number_of_sublists = count($nested_list);
			$item_array = $this->zrdn_format_item($raw_item);

			// if last item is an array
			if ($number_of_sublists > 0 && is_array($nested_list[$number_of_sublists - 1])) {
				$subtitle = $this->get_subtitle($raw_item);
				if ($subtitle) {
					array_push($nested_list, array($item_array));
				} else {
					array_push($nested_list[$number_of_sublists - 1], $item_array);
				}
			} else {
				array_push($nested_list, array($item_array));
			}
		}

		if (isset($nested_list[0])) {
			return $nested_list;
		} else {
			return array();
		}
	}

	/**
	 *  Return subtitle for item.
	 * @param string $item //Raw ingredients/instructions item
	 * @return string
	 */
	private function get_subtitle($item)
	{
		preg_match("/^!(.*)/", $item, $matches);

		$title = "";
		if (count($matches) > 1) {
			$title = $matches[1];
		}

		return $title;
	}


	/**
	 * Generate a json string for this recipe
	 * @return array
	 */

	public function jsonld()
	{
		//if it's not a food item, return empty
		if ($this->non_food) return array();

		$formattedIngredientsArray = array();
		foreach (explode("\n", $this->ingredients) as $item) {
			$itemArray = $this->zrdn_format_item($item);
			$formattedIngredientsArray[] = strip_tags( do_shortcode($itemArray['content']) );
		}

		$formattedInstructionsArray = array();
		foreach (explode("\n", $this->instructions) as $item) {
			$itemArray = $this->zrdn_format_item($item);
			$formattedInstructionsArray[] = strip_tags( do_shortcode($itemArray['content']) );
		}

		$keywords= false;
		$this->keywords = wp_get_post_tags( $this->post_id );
		if ( $this->keywords ){
			$keywords = implode(',',wp_list_pluck($this->keywords,'name'));
		}

		$category = '';

		if (is_array($this->categories) && count($this->categories)>0){
			$category_id = $this->categories[0];
			$cat = get_category($category_id);
			$category = $cat->name;
		} else if((!is_array($this->categories) || count($this->categories)==0) && $this->category){
			$category =  $this->category;
		}

		$description = $this->summary;
		if (strlen($description)===0) $description = $this->recipe_title;
		$description = trim(preg_replace('/\s+/', ' ', strip_tags($description)));

		$recipe_json_ld = array(
			"@context" => "http://schema.org",
			"@type" => "Recipe",
			"description" => $description,
			"image" => $this->recipe_image_json,
			"recipeIngredient" => $formattedIngredientsArray,
			"name" => $this->recipe_title,
			"recipeCategory" => $category,
			"recipeCuisine" => $this->cuisine,
			"nutrition" => array(
				"@type" => "NutritionInformation",
				"calories" => $this->calories,
				"fatContent" => $this->fat,
				"transFatContent" => $this->trans_fat,
				"cholesterolContent" => $this->cholesterol,
				"carbohydrateContent" => $this->carbs,
				"proteinContent" => $this->protein,
				"fiberContent" => $this->fiber,
				"sugarContent" => $this->sugar,
				"saturatedFatContent" => $this->saturated_fat,
				"sodiumContent" => $this->sodium
			),
			"cookTime" => $this->cook_time,
			"prepTime" => $this->prep_time,
			"recipeInstructions" => $formattedInstructionsArray,
			"recipeYield" => $this->yield,
		);

		if ($keywords){
			$recipe_json_ld['keywords'] = $keywords;
		}

		if (!empty($this->video_url)){
			$thumbnail_url = Util::youtube_thumbnail($this->video_url);
			$recipe_json_ld['video'] = array(
				"@type" => "VideoObject",
				"name" => $this->recipe_title,
				"embedUrl" =>  $this->video_url,
				"contentUrl" =>  $this->video_url,
			);

			$post_id = $this->post_id;
			if ($post_id ) {
				$post = get_post( $post_id );
				$publish_date = get_post_time('U', false, $post->ID);
				$recipe_json_ld['video']['uploadDate'] = date('Y-m-d', $publish_date );
			}

			if ($thumbnail_url) {
				$recipe_json_ld['video']["thumbnailUrl"] = $thumbnail_url;
			}

			if ($this->summary) {
				$recipe_json_ld['video']["description"] = $this->summary;
			} else {
				$recipe_json_ld['video']["description"] =  $this->recipe_title;
			}
		}
		if ($this->total_time) {
			$recipe_json_ld["totalTime"] = $this->total_time;
		}
		$cleaned_recipe_json_ld = $this->clean_jsonld($recipe_json_ld);

		$author = $this->author;
		if ($author) {
			$cleaned_recipe_json_ld["author"] = (object)array(
				"@type" => "Person",
				"name" => $author
			);
		}
		$rating_data = apply_filters('zrdn__ratings_format_amp', '',$this->recipe_id, $this->post_id);
		if ($rating_data && $rating_data['count']>0) {
			$itemReviewed = $recipe_json_ld;
			unset($itemReviewed['@context']);
			$rating = array(
				"bestRating" => $rating_data['max'],
				"ratingValue" => $rating_data['rating'],
				"itemReviewed" => $this->recipe_title,
				"ratingCount" => $rating_data['count'],
				"worstRating" => $rating_data['min']
			);

			$cleaned_recipe_json_ld["aggregateRating"] = (object)$rating;
		}
		return $cleaned_recipe_json_ld;
	}

	/**
	 * Remove blank values from JSON-LD. It looks through nested arrays and considers them to be blank if
	 * the all of their keys start with @. E.g.:
	 *
	 * @param $arr array in JSON LD format.
	 *
	 * @return array
	 */
	private function clean_jsonld($arr) {
		$cleaned_crap = array_reduce(array_keys($arr), function ($acc, $key) use ($arr) {
			$value = $arr[$key];
			if (is_array($value)) {
				$cleaned_array = $this->clean_jsonld($value);
				// add array if it has keys that don't start with @
				$array_has_data = count(array_filter(array_keys($cleaned_array), function ($elem) {
						return substr($elem, 0, 1) !== '@';
					})) > 0;

				if ($array_has_data) {
					$acc[$key] = $cleaned_array;
				}
			}
			else {
				if ($value !== "") {
					$acc[$key] = $value;
				}
			}

			return $acc;
		}, array());

		return $cleaned_crap;
	}

	/**
	 * Processes markup for attributes like labels, images and links.
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
	public function zrdn_format_item($item)
	{
		$formatted_item = $item;
		if (preg_match("/^%(\S*)/", $item, $matches)) { // IMAGE Updated to only pull non-whitespace after some blogs were adding additional returns to the output
			// type: image
			// content: $matches[1]
			$attributes = $this->zrdn_get_responsive_image_attributes($matches[1] );
			return array('type' => 'image', 'content' => $matches[1], 'attributes' => $attributes); // Images don't also have labels or links so return the line immediately.
		}

		$retArray = array();
		$subtitle = $this->get_subtitle($item);
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

		$retArray['content'] = $this->richify_item($formatted_item);

		return $retArray;
	}

	/**
	 * Get Responsive Image attributes from URL
	 *
	 * It checks image is not external and return images attributes like srcset, sized etc.
	 *
	 * @param string $url
	 * @return array
	 */

	public function zrdn_get_responsive_image_attributes( $url )
	{

		/**
		 * set up default array values
		 */

		$attributes = array();
		$attributes['url'] = $url;

		$attachment_id = attachment_url_to_postid($url);

		//fallback
		if (!$attachment_id) $attachment_id = get_post_thumbnail_id();

		$attributes['attachment_id'] = $attachment_id;
		$attributes['srcset'] = '';
		$attributes['sizes'] = '';
		$attributes['title'] = '';
		$attributes['alt'] = $this->recipe_title;
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
	 * Convert Image URL to image tag
	 *
	 * @param String $item
	 * @return String
	 */
	public function zrdn_format_image($item)
	{
		preg_match_all('/(%http|%https):\/\/[^ ]+(\.gif|\.jpg|\.jpeg|\.png)/', $item, $matches);
		if (isset($matches[0]) && !empty($matches[0])) {
			foreach ($matches[0] as $image) {
				$attributes = $this->zrdn_get_responsive_image_attributes(str_replace('%', '', $image) );
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
	 * Insert line breaks
	 * @param string $text
	 *
	 * @return string
	 */
	public function insert_breaks($text)
	{
		$output = "";
		$split_string = explode("\r\n\r\n", $text, 10);

		foreach ($split_string as $str) {
			$output .=  $str.'<br>';
		}
		return $output;
	}

	/**
	 *  Replaces the [a|b] pattern with text a that links to b
	 *  Replaces _words_ with an italic span and *words* with a bold span
	 * @param string $item
	 * @param bool $type
	 *
	 * @return string
	 */
	public function richify_item($item, $type=false)
	{
		$output = preg_replace('/\[([^\]\|\[]*)\|([^\]\|\[]*)\]/', '<a href="\\2" target="_blank">\\1</a>', $item);
		$output = preg_replace('/(^|\s)\*([^\s\*][^\*]*[^\s\*]|[^\s\*])\*(\W|$)/', '\\1<span class="bold">\\2</span>\\3', $output);
		$output = preg_replace('/(^|\s)_([^\s_][^_]*[^\s_]|[^\s_])_(\W|$)/', '\\1<span class="italic">\\2</span>\\3', $output);
		if ($type === 'notes' || $type === 'summary') {
			$output = $this->insert_breaks($output);
		}
		return $output;
	}

    /**
     * Delete recipe from table
     *
     * @param int $recipe_id
     * @return boolean
     */

    public function delete() {
        if (!is_user_logged_in()) return false;

        if (!current_user_can('delete_posts')) return false;

        if (!empty($this->recipe_id)) {
            global $wpdb;
            $args = array('recipe_id' => intval($this->recipe_id));
            $table = $wpdb->prefix . self::TABLE_NAME;
            return $wpdb->delete($table, $args);
        }
        return FALSE;
    }


	/**
	 * Calculate Total time in raw format
	 * @param $prep_time
	 * @param $cook_time
	 *
	 * @return string|null
	 * @throws \Exception
	 */

    public function calculate_total_time_raw($prep_time, $cook_time)
    {
        $prep = ZipRecipes::zrdn_extract_time($prep_time);
        $cook = ZipRecipes::zrdn_extract_time($cook_time);
        $prep_time_hours = $prep['time_hours'];
        $prep_time_minutes = $prep['time_minutes'];
        $cook_time_hours = $cook['time_hours'];
        $cook_time_minutes = $cook['time_minutes'];

        if ($prep_time_hours || $prep_time_minutes || $cook_time_hours || $cook_time_minutes) {
            $hours_total = $prep_time_hours+$cook_time_hours;

            $minutes_total = $cook_time_minutes + $prep_time_minutes + ($hours_total * 60);
            //minutes should not be over 59
            $hours = floor($minutes_total / 60);
            $minutes = ($minutes_total % 60);

            // converting 01 to 1 using int
            return 'PT' . (int)$hours . 'H' . (int)$minutes . 'M';
        }

        return NULL;
    }



    /**
     * Insert Recipe in db
     *
     * @param $recipe
     * @return bool|int
     */
    public static function db_insert($recipe){
        if(empty($recipe)){
            return FALSE;
        }
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        $wpdb->insert($table, $recipe);
        return $wpdb->insert_id;
    }

    /**
     * Recipe update db
     *
     * @param $recipe
     * @param $where
     * @return bool|false|int
     */
    public static function db_update($recipe,$where=null){
        if(empty($recipe) || empty($where)){
            return FALSE;
        }
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        return $wpdb->update($table, $recipe, $where);
    }



}


function zrdn_unlink_recipe_from_post($recipe_id){
    if (!current_user_can('edit_posts')) return;
    $recipe = new Recipe($recipe_id);

    $post_id = $recipe->post_id;
    if ($post_id){
        $post = get_post($post_id);
        if ($post){

            $pattern_gutenberg = Util::get_shortcode_pattern($recipe_id, false, 'gutenberg' );
	        $pattern_classic = Util::get_shortcode_pattern($recipe_id, false, 'classic' );
	        $pattern_legacy = Util::get_shortcode_pattern($recipe_id, false, 'legacy' );


	        if (preg_match($pattern_gutenberg, $post->post_content, $matches)) {
                $content = preg_replace($pattern_gutenberg, '', $post->post_content, 1);

                $post = array(
                    'ID' => $post_id,
                    'post_content' => $content,
                );
                wp_update_post($post);
            }elseif (preg_match($pattern_classic, $post->post_content, $matches)) {
		        $content = preg_replace($pattern_classic, '', $post->post_content, 1);

		        $post = array(
			        'ID' => $post_id,
			        'post_content' => $content,
		        );
		        wp_update_post($post);
	        }elseif (preg_match($pattern_legacy, $post->post_content, $matches)) {
		        $content = preg_replace($pattern_legacy, '', $post->post_content, 1);

		        $post = array(
			        'ID' => $post_id,
			        'post_content' => $content,
		        );
		        wp_update_post($post);
	        }

        }
    }

    //set post_id to empty and save.
    $recipe->post_id = false;
    $recipe->save();
}