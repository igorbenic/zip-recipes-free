<?php
namespace ZRDN;
use ZRDN\Recipe as RecipeModel;
/**
 * Adds  widget.
 */
class ZRDN_Nutrition_Widget extends \WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		parent::__construct(
			'zrdn_nutrition_widget', // Base ID
			__( 'Nutrition Label', 'zip-recipes' ), // Name
			array( 'description' => __( 'Show the Nutrition Label for a recipe', 'zip-recipes' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
	    if (!is_singular()) return;
		echo $args['before_widget'];

        global $post;
		if ($post){
            $recipe = RecipeModel::db_select_by_post_id($post->ID);
		    if ($recipe) {
		        $recipe_id = $recipe->recipe_id;
                echo do_shortcode("[zrdn-nutrition-label  recipe_id=$recipe_id]");
            }

        }

		echo $args['after_widget'];

	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Nutrition info', 'zip-recipes' );

		/*
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
		*/
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

}
function zrdn_register_nutrition_widget() {
    register_widget(__NAMESPACE__ . '\ZRDN_Nutrition_Widget');
}
add_action('widgets_init', __NAMESPACE__ . '\zrdn_register_nutrition_widget');