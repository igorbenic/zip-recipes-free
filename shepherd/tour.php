<?php
namespace ZRDN;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'you do not have access to this page!' );
}
$zrdn_tour = new zrdn_tour();
class zrdn_tour {
	private static $_this;
	public $capability = 'activate_plugins';
	public $url;
	public $version;

	function __construct() {
		if ( isset( self::$_this ) ) {
			wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.',
				get_class( $this ) ) );
		}

		self::$_this = $this;

		$this->url     = ZRDN_PLUGIN_URL . '/shepherd';
		$this->version = ZRDN_VERSION_NUM;
		add_action( 'wp_ajax_zrdn_cancel_tour', array( $this, 'cancel_tour' ) );
		add_action( 'admin_init', array( $this, 'restart_tour' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	static function this() {
		return self::$_this;
	}

	public function enqueue_assets( $hook ) {
		if ( get_site_option( 'zrdn_tour_started' ) ) {
			if ( $hook !== 'plugins.php'
			     && strpos( $hook, 'zrdn' ) === false
			     && $hook !== 'post.php' && $hook !== 'edit.php'

			) {
				return;
			}

			wp_register_script( 'zrdn-tether',
				trailingslashit( $this->url )
				. 'tether/tether.min.js', "", $this->version );
			wp_enqueue_script( 'zrdn-tether' );

			wp_register_script( 'zrdn-shepherd',
				trailingslashit( $this->url )
				. 'tether-shepherd/shepherd.min.js', "", $this->version );
			wp_enqueue_script( 'zrdn-shepherd' );

			wp_register_style( 'zrdn-shepherd',
				trailingslashit( $this->url )
				. "css/shepherd-theme-arrows.min.css", "",
				$this->version );
			wp_enqueue_style( 'zrdn-shepherd' );

			wp_register_style( 'zrdn-shepherd-tour',
				trailingslashit( $this->url ) . "css/tour.min.css", "",
				$this->version );
			wp_enqueue_style( 'zrdn-shepherd-tour' );

			wp_register_script( 'zrdn-shepherd-tour',
				trailingslashit( $this->url )
				. '/js/tour.js', array( 'jquery' ), $this->version );
			wp_enqueue_script( 'zrdn-shepherd-tour' );

			$logo
				   = '<span class="cmplz-tour-logo"><img class="cmplz-tour-logo" style="height:55px;margin:5px" src="'
				     .         ZRDN_PLUGIN_URL . 'images/zip-icon-pink.svg"></span>';
			$html  = '<div class="cmplz-tour-logo-text">' . $logo
			         . '<span class="cmplz-tour-text">{content}</span></div>';

			$demo_recipe_id = Util::get_demo_recipe_id();
			if (!$demo_recipe_id) return;
			$recipe_post_id = Util::get_preview_post_id( $demo_recipe_id );
			$steps = array(
				0 => array(
					'title'  => __( 'Welcome to Zip Recipes', 'zip-recipes' ),
					'text'   => __( "Create beautiful recipes with extended features, optimized for Google. Start with creating your recipe template or take the tour.", 'zip-recipes' ),
					'attach' => '.zrdn-settings-link',
					'position' => 'right',
					'link'  => admin_url('plugins.php'),
				),
				1 => array(
					'title'  => __( 'Create your template', 'zip-recipes' ),
					'text'   => __( "Here you will be able to style your recipe card. You can select one of our favorite templates or customize your recipe card to your liking with our drag & drop editor.", 'zip-recipes' ),
					'attach' => '.tab-text.tab-Settings',
					'position' => 'bottom',
					'link'   => add_query_arg( array( "page" => "zrdn-template"), admin_url( "admin.php" ) ),
				),
				2 => array(
					'title'  => __( 'Create your template', 'zip-recipes' ),
					'text'   => __( "Edit your recipe blocks for more customization. Drag blocks to inactive or shuffle them around. You're in control! Want to start over, press reset template or load a new predefined template", 'zip-recipes' ),
					'attach' => '.zrdn-inactive-container',
					'position' => 'right',
					'link'   => add_query_arg( array( "page" => "zrdn-template"), admin_url( "admin.php" ) ),
				),
				3 => array(
					'title'  => __( 'Settings', 'zip-recipes' ),
					'text'   => __( "In our settings overview you will find general settings for your recipe card and demo's for our templates. If you need help configuring, please ask support or have a look at our documentation.", 'zip-recipes' ),
					'attach' => '.zrdn-general .zrdn-grid-title',
					'position' => 'right',
					'click' => '.tab-dashboard',
					'link'   => add_query_arg( array( "page" => "zrdn-settings"), admin_url( "admin.php" ) ),
				),
				4 => array(
					'title'  => __( 'Extensions', 'zip-recipes' ),
					'text'   => __( "Have a look at our premium add-ons for ZIP Recipes, taking your recipes to a whole new level!", 'zip-recipes' ),
					'attach' => '.zrdn-about-extensions',
					'position' => 'right',
					'click' => '.tab-extensions',
					'link'   => add_query_arg( array( "page" => "zrdn-settings"), admin_url( "admin.php" ) ),
				),
				5 => array(
					'title'  => __( 'Create your recipe', 'zip-recipes' ),
					'text'   => __( "Here you can start creating your recipes and have an overview of the most important metrics per recipe, including your most popular recipes.", 'zip-recipes' ),
					'attach' => '.zrdn-add-recipe',
					'position' => 'right',
					'link'   => add_query_arg( array( "page" => "zrdn-recipes"), admin_url( "admin.php" ) ),
				),
				6 => array(
					'title'  => __( 'Editing your recipes', 'zip-recipes' ),
					'text'   => __( "The WYSIWYG editor will lead you through the process of creating each new recipe. A preview will be available immediately. Want to create a recipe directly in your WordPress editor. No problem!.", 'zip-recipes' ),
					'attach' => '.zrdn-recipe-save-button',
					'position' => 'right',
					'link'   => add_query_arg( array( "page" => "zrdn-recipes", "id" => $demo_recipe_id), admin_url( "admin.php" ) ),
				),
				7 => array(
					'title'  => __( 'The preview recipe', 'zip-recipes' ),
					'text'   => __( "To make the preview as accurate as possible, Zip Recipes uses a private post. A private post is only visible to you. We recommend not to delete this post.", 'zip-recipes' ),
					'attach' => "#post-$recipe_post_id .row-title",
					'position' => 'right',
					'link'   => admin_url( "edit.php" ),
				),

			);

			if ( Util::uses_gutenberg() ) {
				$steps[8] = array(
					'title'  => __( 'Editing in Gutenberg', 'zip-recipes' ),
					'text'   => __( "Add a Zip Recipes block, then click the block, and the 'cog' wheel on the right top to add a new recipe, or to choose an existing recipe.", 'zip-recipes' ),
					'attach' => '.interface-pinned-items',
					'position' => 'left',
					'link'   => add_query_arg( array( "action" => "edit", "post" => $recipe_post_id), admin_url( "post.php" ) ),
				);
				$steps[9] = array(
					'title'  => __( 'The end', 'zip-recipes' ),
					'text'   => __( "Add a Zip Recipes block, then click the block, and the 'cog' wheel on the right top to add a new recipe, or to choose an existing recipe.", 'zip-recipes' ),
					'attach' => '.interface-pinned-items',
					'position' => 'left',
					'link'   => add_query_arg( array( "action" => "edit", "post" => $recipe_post_id), admin_url( "post.php" ) ),
				);
			} else {
				$steps[8] = array(
					'title'  => __( 'Editing in classic editor', 'zip-recipes' ),
					'text'   => __( "Click on 'create recipe' to start creating a new recipe, or choose an existing recipe.", 'zip-recipes' ),
					'attach' => '#zrdn_recipe_meta_box',
					'position' => 'left',
					'link'   => add_query_arg( array( "action" => "edit", "post" => $recipe_post_id), admin_url( "post.php" ) ),
				);
				$steps[9] = array(
					'title'  => __( 'The end', 'zip-recipes' ),
					'text'   => __( "Get started. Start with your new template and go from there. You can start the tour anytime under Settings. Good luck!", 'zip-recipes' ),
					'attach' => '#zrdn_recipe_meta_box',
					'position' => 'left',
					'link'   => add_query_arg( array( "action" => "edit", "post" => $recipe_post_id), admin_url( "post.php" ) ),
				);
			}

			$steps = apply_filters( 'zrdn_shepherd_steps', $steps );
			wp_localize_script( 'zrdn-shepherd-tour', 'zrdn_tour',
				array(
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
					'html'           => $html,
					'token'          => wp_create_nonce( 'zrdn_tour_nonce' ),
					'nextBtnText'    => __( "Next", "zip-recipes" ),
					'backBtnText'    => __( "Previous", "zip-recipes" ),
					'configure_text' => __( "Create your template", "zip-recipes" ),
					'configure_link' => admin_url( "admin.php?page=zrdn-template" ),
					'endTour'        => __( "End tour", "zip-recipes" ),
					'steps'          => $steps,
				) );

		}
	}

	/**
	 *
	 * @since 7.0
	 *
	 * When the tour is cancelled, a post will be sent. Listen for post and update tour cancelled option.
	 *
	 */

	public function cancel_tour() {
		if ( ! isset( $_POST['token'] )
		     || ! wp_verify_nonce( $_POST['token'], 'zrdn_tour_nonce' )
		) {
			return;
		}
		update_site_option( 'zrdn_tour_started', false );
		update_site_option( 'zrdn_tour_shown_once', true );
	}

	/**
	 * Restart tour when the button has been clicked
	 * @hooked admin_init
	 */
	public function restart_tour() {
		if ( ! isset( $_GET['action'] ) || $_GET['action'] !=='zrdn_restart_tour' ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		update_site_option( 'zrdn_tour_started', true );
		wp_redirect( admin_url( 'plugins.php' ) );
		exit;
	}

}
