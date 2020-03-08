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

        Util::log("Calling db_setup() action");

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
		if ($recipe_id) {
			$this->recipe_id = $recipe_id;
		}
		if ($title) {
			$this->recipe_title = $title;
		}
		if ($image_url) {
			$this->recipe_image = $image_url;
		}
		if ($this->recipe_id || $this->post_id){
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
    public $author;

    /**
     * @var string
     */
    public $video_url;

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
	public $cook_time;

	/**
	 * @var string
	 */
	public $total_time;

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

	/**
	 * varchar(100)
	 * @var string
	 */
	public $category;

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
    public $saturated_fat_daily;
    public $cholesterol_daily;
    public $sodium_daily;
    public $carbs_daily;
    public $fiber_daily;
    public $has_nutrition_data = false;
    public $preview = false;



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

        if (!$recipe_data && $this->post_id){
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

        //set default author if empty
        $this->author = apply_filters('zrdn_author_value', $this->author, $this->recipe_id, $this->post_id);
        $this->total_time = $this->calculate_total_time_raw($this->prep_time, $this->cook_time);

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
            $this->recipe_image = $this->get_image_url_by_size($this->recipe_image_id, 'zrdn_recipe_image');
        }
        //check if image is also featured image for connected post
        //@todo: deprecate this
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
        } else {
            if ($this->post_id>0){
                $post_thumbnail_id = get_post_thumbnail_id($this->post_id);
                $this->recipe_image_json = $this->generate_recipe_image_json($post_thumbnail_id);
            }
        }

        /**
         * Load the category from the post
         */
        if (strlen($this->category)==0){
            $post_categories = wp_get_post_categories($this->post_id);
            $cats = array();
            foreach ($post_categories as $c) {
                $cat = get_category($c);
                $cats[] = $cat->name;
            }
            $this->category = implode(', ',$cats);
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

        //check if post_id is a revision
        if ($this->post_id && get_post_type($this->post_id) === 'revision'){
            $this->post_id = false;
        }

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
     * @param int $image_id
     * @return array|string image_json
     */

    public function generate_recipe_image_json($image_id){

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
            $this->{$fieldname} = '{' . $fieldname . '_value}';
        }
        $actual_recipe_id = false;
        $actual_post_id = false;
        $this->total_time = NULL;
        if (isset($_GET['id'])){
            $actual_recipe = new Recipe(intval($_GET['id']));
            $actual_recipe_id = $actual_recipe->recipe_id;
            $actual_post_id = $actual_recipe->post_id;
            $this->total_time = $this->calculate_total_time_raw($actual_recipe->prep_time, $actual_recipe->cook_time);
        }
        if (!$actual_post_id && isset($_GET['post_id'])){
            $actual_post_id = intval($_GET['post_id']);
        }

        $this->author = apply_filters('zrdn_author_value', false, $actual_recipe_id, $actual_post_id);

        $this->prep_time = 'PT99H99M';
        $this->cook_time = 'PT99H99M';

        $this->nutrition_label = ZRDN_PLUGIN_URL . '/images/s.png';
        $this->recipe_image = ZRDN_PLUGIN_URL.'/images/recipe-default-bw.png';
        $this->is_featured_post_image = false;

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
        $value = str_replace(array(' ', 'mg', 'g', 'µg'), '', $value);

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
     *
     * @param $prep_time
     * @param $cook_time
     * @return false|null|string
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
            $pattern = Util::get_shortcode_pattern($recipe_id);

            if (preg_match($pattern, $post->post_content, $matches)) {
                $content = preg_replace($pattern, '', $post->post_content, 1);

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