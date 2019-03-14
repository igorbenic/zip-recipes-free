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
     * Get a recipe from the db
     *
     * @global \ZRDN\Array $wpdb
     * @param type $recipe_id
     * @return type
     */
    public static function db_select($recipe_id) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        $selectStatement = sprintf("SELECT * FROM `%s` WHERE recipe_id=%d", $table, $recipe_id);
        return $wpdb->get_row($selectStatement);
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