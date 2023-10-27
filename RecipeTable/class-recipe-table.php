<?php
namespace ZRDN;

/**
 * Recipes Table Class
 *
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Recipe_Table extends \WP_List_Table {

    /**
     * Number of items per page
     *
     * @var int
     * @since 1.5
     */
    public $per_page = 20;

    /**
     * Number of recipes found
     *
     * @var int
     * @since 1.7
     */
    public $count = 0;

    /**
     * Total recipes
     *
     * @var int
     * @since 1.95
     */
    public $total = 0;

    /**
     * The arguments for the data set
     *
     * @var array
     * @since  2.6
     */
    public $args = array();
    public $most_popular_id = false;

    /**
     * Get things started
     *
     * @since 1.5
     * @see WP_List_Table::__construct()
     */


    public function __construct() {
        global $status, $page;

	    $args            = array(
		    'order_by' => 'hits',
		    'order'    => 'DESC',
		    'number'   => 1,
	    );
	    $recipes         = Util::get_recipes( $args );
	    if ( ! empty( $recipes ) ) {
		    $this->most_popular_id = $recipes[0]->recipe_id;
	    }

        // Set parent defaults
        parent::__construct( array(
            'singular' => __( 'Recipe', 'zip-recipes'),
            'plural'   => __( 'Recipes', 'zip-recipes'),
            'ajax'     => false,
        ) );

    }

    /**
     * Show the search field
     *
     * @since 1.7
     *
     * @param string $text Label for the search box
     * @param string $input_id ID of the search box
     *
     * @return void
     */

    public function search_box( $text, $input_id ) {
        $input_id = $input_id . '-search-input';

        if ( ! empty( $_REQUEST['orderby'] ) )
            echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
        if ( ! empty( $_REQUEST['order'] ) )
            echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
        ?>


        <p class="search-box">
	        <?php
                $cats = Util::get_recipe_categories();
                $selected_category = isset($_GET['zrdn_category_filter']) ? esc_html($_GET['zrdn_category_filter']) : '';
	        ?>
	        <select name="zrdn_category_filter">
		        <option value=""><?php _e("category", 'zip-recipes')?></option>
		        <?php foreach ($cats as $slug => $cat){
		        	$selected = $selected_category === $slug ? "selected" : '';
		        	?>
			        <option <?php echo $selected?> value="<?php echo esc_html($slug) ?>" ><?php echo esc_html($cat['name'])?></option>
		        <?php } ?>
	        </select>
	        <?php
            $cuisines = Util::get_cuisines();
            $selected_cuisine = isset($_GET['zrdn_cuisine_filter']) ? esc_html($_GET['zrdn_cuisine_filter']) : ''; ?>
	        <select name="zrdn_cuisine_filter">
                <option value=""><?php _e("cuisine", 'zip-recipes')?></option>
		        <?php foreach ( $cuisines as $cuisine ){
			        $selected = $selected_cuisine === $cuisine ? "selected" : '';
			        ?>
                    <option <?php echo $selected?> value="<?php echo esc_html($cuisine)?>" ><?php echo esc_html($cuisine)?></option>
		        <?php } ?>
            </select>
            <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
            <input type="text" value="<?php echo esc_html($this->get_search())?>" name="s">
            <?php submit_button( $text, 'button', false, false, array('ID' => 'search-submit') ); ?>
        </p>
        <?php

    }

    /**
     * Gets the name of the primary column.
     *
     * @since 2.5
     * @access protected
     *
     * @return string Name of the primary column.
     */
    protected function get_primary_column_name() {
        return __('Title','zip-recipes');
    }

    /**
     * Output the checkbox column
     *
     * @access      private
     * @since       7.1.2
     * @return      void
     */

    function column_cb( $recipe ) {

        return sprintf(
            '<input type="checkbox" name="%1$s_id[]" value="%2$s" />',
            esc_attr( $this->_args['singular'] ),
            esc_attr( $recipe->recipe_id )
        );

    }

        /**
     * Setup available bulk actions
     *
     * @access      private
     * @since       7.1.2
     * @return      array
     */

    function get_bulk_actions() {

        $actions = array(
            'delete'     => __( 'Delete', 'zip-recipes' ),
            'enable-monetization'     => __( 'Enable monetization', 'zip-recipes' ),
            'disable-monetization'     => __( 'Disable monetization', 'zip-recipes' ),
        );

        return $actions;

    }

    /**
     * Process bulk actions
     *
     * @access      private
     * @since       7.1.2
     * @return      void
     */
    function process_bulk_action() {
        if (!current_user_can('edit_posts')) return false;

        if( !isset($_GET['_wpnonce']) || ! wp_verify_nonce( $_GET['_wpnonce'] , 'bulk-' . $this->_args['plural'] ) ) {
            return;
        }
        
        $ids = isset( $_GET['recipe_id'] ) ? $_GET['recipe_id'] : false;

        if( ! $ids ) {
            return;
        }

        if ( ! is_array( $ids ) ) {
            $ids = array( $ids );
        }

        foreach ( $ids as $id ) {
            if ( 'delete' === $this->current_action() ) {
                $recipe = new Recipe(intval($id));
                $recipe->delete();
            }
            if ( 'enable-monetization' === $this->current_action() ) {
                $recipe = new Recipe(intval($id));
                $recipe->share_this_recipe = true;
                $recipe->save();
            }
            if ( 'disable-monetization' === $this->current_action() ) {
                $recipe = new Recipe(intval($id));
                $recipe->share_this_recipe = false;
                $recipe->save();
            }

        }


    }

    /**
     * This function renders most of the columns in the list table.
     *
     * @since 1.5
     *
     * @param Recipe $recipe
     * @param string $column_name The name of the column
     *
     * @return string Column Name
     */
    public function column_default( $recipe, $column_name ) {
        $value='';

        if ($column_name === 'ID') {
            $value = $recipe->recipe_id;
        }

        return apply_filters( 'zrdn_recipe_column_' . $column_name, $value, $recipe->recipe_id );
    }

	/**
	 * @param Recipe $recipe
	 *
	 * @return string
	 */
    public function column_name( $recipe ) {
        $name       = ! empty( $recipe->recipe_title ) ? $recipe->recipe_title : '<em>' . __( 'Unnamed recipe','zip-recipes') . '</em>';
        $name = apply_filters('zrdn_recipe_name', $name);

        $actions     = array(
            'edit' => '<a href="' . admin_url( 'admin.php?page=zrdn-recipes&id=' . $recipe->recipe_id ) . '">' . __( 'Edit', 'zip-recipes') . '</a>',
        );

        $preview_post_id = get_option('zrdn_preview_post_id');
        if ($recipe->post_id && $recipe->post_id !== $preview_post_id ){
            $actions['unlink'] = '<a class="zrdn-recipe-action" data-action="unlink" data-id="'.$recipe->recipe_id.'" href="#">' . __( 'Unlink from post', 'zip-recipes') . '</a>';
            $actions['delete'] = '<a class="zrdn-recipe-action zrdn-hidden" data-action="delete"  data-id="'.$recipe->recipe_id.'" href="#">' . __( 'Delete', 'zip-recipes') . '</a>';
            $actions['view_post'] = '<a href="'.add_query_arg(array('post'=>$recipe->post_id,'action'=>'edit'),admin_url('post.php')).'">' . __( 'View post', 'zip-recipes') . '</a>';

        } else {
            $actions['delete'] = '<a class="zrdn-recipe-action" data-action="delete"  data-id="'.$recipe->recipe_id.'" href="#">' . __( 'Delete', 'zip-recipes') . '</a>';

        }
        if ($recipe->share_this_recipe == true && zrdn_use_rdb_api() && get_option('zrdn_demo_recipe_id') !== $recipe->recipe_id ){
             $actions['demonetize'] = '<a class="zrdn-recipe-action" data-action="demonetize"  data-id="'.$recipe->recipe_id.'" href="#">' . __( 'Disable monetization', 'zip-recipes') . '</a>';
        } elseif ($recipe->share_this_recipe == false && zrdn_use_rdb_api() && get_option('zrdn_demo_recipe_id') !== $recipe->recipe_id ) {
            $actions['monetize'] = '<a class="zrdn-recipe-action" data-action="monetize"  data-id="'.$recipe->recipe_id.'" href="#">' . __( 'Enable monetization', 'zip-recipes') . '</a>';
        }

        return $name  . $this->row_actions( $actions );
    }

	/**
     * Show the shortcode in the column
	 * @param Recipe $recipe
	 *
	 * @return string
	 */
    public function column_shortcode( $recipe ) {
        return '<div class="zrdn-selectable">[zrdn-recipe id='.$recipe->recipe_id.']</div>';
    }

	/**
     * Show the number of views in the column
	 * @param Recipe $recipe
	 *
	 * @return int
	 */
	public function column_views( $recipe ) {
		return $recipe->hits;
	}

	/**
	 * Show the author
	 * @param Recipe $recipe
	 *
	 * @return string
	 */
	public function column_author( $recipe ) {
		return $recipe->author;
	}

	/**
	 * Show the category
	 * @param Recipe $recipe
	 *
	 * @return string
	 */
	public function column_category( $recipe ) {
		$cats = array();
		if (is_array($recipe->categories) ) {
			foreach ($recipe->categories as $category_id) {
				$cat = get_category($category_id);
				$cats[] = $cat->name;
			}

			echo implode(', ', $cats);
		}


	}

	/**
	 * Show the cuisine
	 * @param Recipe $recipe
	 *
	 * @return string
	 */
	public function column_cuisine( $recipe ) {
		return $recipe->cuisine;
	}

	/**
	 * @param Recipe $recipe
	 *
	 * @return string
	 */
	public function column_details( $recipe ) {
	    if ($this->most_popular_id === $recipe->recipe_id ) {
		    return '<span class="zrdn-badge popular">'.__("Popular","zip-recipes").'</span>';
	    }else if (get_option('zrdn_demo_recipe_id') === $recipe->recipe_id ) {
		    return '<span class="zrdn-badge demo">'.__("Demo","zip-recipes").'</span>';
	    } elseif ( $recipe->non_food == true ) {
			return '<span class="zrdn-badge nonfood">'.__("Non food","zip-recipes").'</span>';
		}
	}

    /**
     * Show the number of views in the column
     * @param $recipe
     *
     * @return mixed
     */
    public function column_sharing_status( $recipe ) {
        if (!zrdn_is_rdb_api_allowed_country()) return;
        $zip_sharing_status = $recipe->zip_sharing_status;
        $edamam_sharing_status = $recipe->edamam_sharing_status;
        $sharing_status = $edamam_sharing_status ? $edamam_sharing_status : $zip_sharing_status;

        if ( get_option('zrdn_demo_recipe_id') === $recipe->recipe_id ) {
            return '';
        }

        if (!$recipe->share_this_recipe || !zrdn_use_rdb_api()) {
            $sharing_status = 'not_activated';
        }

        $missing_values = array_filter($recipe->missing_sharing_values);
        $titles = array(
            'not_activated' => __("Disabled", "zip-recipes"),
            'needs_improvement' => __("Needs improvement", "zip-recipes"),
            'waiting_approval' => __("Pending review", "zip-recipes"),
            'approved' => __("Approved", "zip-recipes"),
            'declined' => __("Rejected", "zip-recipes"),
        );

        if ( ! empty( $sharing_status ) ) {
	        $missing_values_names = array(
		        'recipe_title' => __("Title", 'zip-recipes'),
		        'prep_time' => __("Prep time", 'zip-recipes'),
		        'cook_time' => __("Cook time", 'zip-recipes'),
		        'wait_time' => __("Wait time", 'zip-recipes'),
		        'yield' => __("Serving size", 'zip-recipes'),
		        'serving_size' => __("Servings", 'zip-recipes'),
		        'ingredients' => __("Ingredients", 'zip-recipes'),
		        'instructions' => __("Instructions", 'zip-recipes'),
		        'recipe_image_id' => __("Recipe image", 'zip-recipes'),
	        );

	        $missing_values_result = array();
	        foreach( $missing_values_names as $key => $string ) {
		        if (array_key_exists( $key, $missing_values) ){
			        $missing_values_result[] = $string;
		        }
	        }
            ob_start();
            echo '<span class="zrdn-badge '.$sharing_status.'">'.$titles[$sharing_status].'</span>';

            if ( $sharing_status == 'needs_improvement' ) {
                $missing = sprintf(__('The following fields need your attention: %s', 'zip-recipes'), implode( ', ', $missing_values_result ) );
                echo '<span class="zrdn-tooltip-top tooltip-right" data-zrdn-tooltip="'.
                        $missing.
                     '"><span class="zrdn-tooltip-icon dashicons dashicons-editor-help"></span>
                    </span>';
            }
            // add hidden fields for disabling and enabling monetization
            echo '<span style="display: none;" class="zrdn-badge not_activated">'.$titles['not_activated'].'</span>';
            echo '<span style="display: none;" class="zrdn-badge waiting_approval">'.$titles['waiting_approval'].'</span>';
            return ob_get_clean();
        }

    }

    /**
     * Retrieve the table columns
     *
     * @since 1.5
     * @return array $columns Array of all the list table columns
     */
    public function get_columns() {
        $columns = array(
	        'cb'             => '<input type="checkbox"/>',
	        'ID'             => __( 'ID', 'zip-recipes' ),
	        'name'           => __( 'Name', 'zip-recipes' ),
	        'category'       => __( 'Category', 'zip-recipes' ),
	        'views'          => __( 'Views', 'zip-recipes' ),
	        'cuisine'        => __( 'Cuisine', 'zip-recipes' ),
	        'author'         => __( 'Author', 'zip-recipes' ),
	        'shortcode'      => __( 'Shortcode', 'zip-recipes' ),
	        'sharing_status' => __( 'Monetization', 'zip-recipes' ),
	        'details'        => '',
        );

        if (!zrdn_use_rdb_api()){
            unset($columns['sharing_status']);
        }

        return apply_filters( 'zrdn_recipe_columns', $columns );
    }

    /**
     * Get the sortable columns
     *
     * @since 2.1
     * @return array Array of all the sortable columns
     */
    public function get_sortable_columns() {
        $columns = array(
	        'ID'             => array( 'recipe_id', true ),
	        'name'           => array( 'recipe_title', true ),
	        'views'          => array( 'hits', true ),
	        'sharing_status' => array( 'zip_sharing_status', true ),
        );

	    if (!zrdn_use_rdb_api()){
	        unset($columns['sharing_status']);
        }

	    return $columns;
    }


    /**
     * Retrieve the current page number
     *
     * @since 1.5
     * @return int Current page number
     */
    public function get_paged() {
        return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
    }


    /**
     * Retrieve the current status
     *
     * @since 2.1.7
     * @return int Current status
     */
    public function get_status() {
        return isset( $_GET['status'] ) ? sanitize_title($_GET['status'] ) : 'active';
    }

    /**
     * Retrieves the search query string
     *
     * @since 1.7
     * @return mixed string If search is present, false otherwise
     */
    public function get_search() {
        return ! empty( $_GET['s'] ) ? urldecode( trim( $_GET['s'] ) ) : false;
    }

	/**
	 * Retrieves the category filter
	 *
	 * @since 1.7
	 * @return mixed string If search is present, false otherwise
	 */
	public function get_category_filter() {
		return ! empty( $_GET['zrdn_category_filter'] ) ? urldecode( trim( $_GET['zrdn_category_filter'] ) ) : false;
	}

	/**
	 * Retrieves the cuisine filter
	 *
	 * @since 1.7
	 * @return mixed string If search is present, false otherwise
	 */
	public function get_cuisine_filter() {
		return ! empty( $_GET['zrdn_cuisine_filter'] ) ? urldecode( trim( $_GET['zrdn_cuisine_filter'] ) ) : false;
	}

    /**
     * Build all the reports data
     *
     * @since 1.5
     * @global object $wpdb Used to query the database using the WordPress
     *   Database API
     * @return array $reports_data All the data for customer reports
     */
    public function reports_data() {

        if (!current_user_can('edit_posts')) return array();

        $data    = array();
        $paged   = $this->get_paged();
        $offset  = $this->per_page * ( $paged - 1 );
        $search  = $this->get_search();
        $category  = $this->get_category_filter();
        $cuisine  = $this->get_cuisine_filter();
        $order   = isset( $_GET['order'] )   ? esc_sql( $_GET['order'] )   : 'DESC';
        $orderby = isset( $_GET['orderby'] ) ? esc_sql( $_GET['orderby'] ) : 'recipe_id';

        $args    = array(
            'number'  => $this->per_page,
            'offset'  => $offset,
            'order'   => $order,
            'orderby' => $orderby,
            'searchFields' => 'all',
        );

        $args['search']  = $search;

	    if (strlen($category)>0) {
		    $args['category']  = $category;
	    }
	    if (strlen($cuisine)>0) {
		    $args['cuisine']  = $cuisine;
	    }
        $this->args = $args;
        $recipes  = Util::get_recipes( $args );

        if ( $recipes ) {

            foreach ( $recipes as $recipe ) {
                $data[] = new Recipe($recipe->recipe_id);
            }
        }

        return $data;
    }


    public function prepare_items() {

        $columns  = $this->get_columns();
        $hidden   = array(); // No hidden columns
        $sortable = $this->get_sortable_columns();

        wp_create_nonce( 'zrdn_process_bulk_actions' );
        $this->process_bulk_action();

        $this->_column_headers = array( $columns, $hidden, $sortable );

        $this->items = $this->reports_data();

        $search  = $this->get_search();

        $args['search']  = $search;
        $this->total = Util::count_recipes($args);

        // Add condition to be sure we don't divide by zero.
        // If $this->per_page is 0, then set total pages to 1.
        $total_pages = $this->per_page ? ceil( (int) $this->total / (int) $this->per_page ) : 1;

        $this->set_pagination_args( array(
            'total_items' => $this->total,
            'per_page'    => $this->per_page,
            'total_pages' => $total_pages,
        ) );
    }
}
