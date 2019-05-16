<?php
/**
 * Created by PhpStorm.
 * User: gezimhome
 * Date: 2018-01-01
 * Time: 10:37
 */

namespace ZRDN;

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
	}

	/**
	 * @var int
	 */
	public $recipe_id;

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
	public $recipe_image;

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
	public $instructions;

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

    public function load(){
        $recipe_data = false;
        if ($this->recipe_id){
            $recipe_data = $this->db_select($this->recipe_id);
        }

        if (!$recipe_data && $this->post_id){
            $recipe_data = $this->db_select_by_post_id($this->post_id);
            $this->recipe_id = $recipe_data->recipe_id;
        }

        if ($recipe_data) {
            $recipe = get_object_vars($recipe_data);
            foreach ($recipe as $fieldname => $value) {
                $this->{$fieldname} = $value;
            }
        }
    }

    public function save(){
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        $update_arr = array(
            'post_id' => intval($this->post_id),
            'recipe_title' => sanitize_text_field($this->recipe_title),
            'recipe_image' => sanitize_text_field($this->recipe_image),
            'summary' => wp_kses_post($this->summary),
            'prep_time' => $this->prep_time,
            'cook_time' => $this->cook_time,
            'yield' => sanitize_text_field($this->yield),
            'serving_size' => sanitize_text_field($this->serving_size),
            'calories' => sanitize_text_field($this->calories),
            'fat' => sanitize_text_field($this->fat),
            'carbs' => sanitize_text_field($this->carbs),
            'protein' => sanitize_text_field($this->protein),
            'fiber' => sanitize_text_field($this->fiber),
            'sugar' => sanitize_text_field($this->sugar),
            'saturated_fat' => sanitize_text_field($this->saturated_fat),
            'sodium' => sanitize_text_field($this->sodium),
            'ingredients' => wp_kses_post($this->ingredients),
            'instructions' => wp_kses_post($this->instructions),
            'notes' => wp_kses_post($this->notes),
            'category' => sanitize_text_field($this->category),
            'cuisine' => sanitize_text_field($this->cuisine),
            'trans_fat' =>sanitize_text_field( $this->trans_fat),
            'cholesterol' => sanitize_text_field($this->cholesterol),
            'vitamin_a' => sanitize_text_field($this->vitamin_a),
            'vitamin_c' => sanitize_text_field($this->vitamin_c),
            'calcium' => sanitize_text_field($this->calcium),
            'iron' => sanitize_text_field($this->iron),
        );
        $wpdb->update(
            $table,
            $update_arr,
            array('recipe_id' => $this->recipe_id)
        );
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
     * Delete review row from table
     *
     * @global \ZRDN\Array $wpdb
     * @param array $delete
     * @return boolean
     */
    public static function db_delete($delete = array()) {
        if (!empty($delete)) {
            global $wpdb;
            $table = $wpdb->prefix . self::TABLE_NAME;
            return $wpdb->delete($table, $delete);
        }
        return FALSE;
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