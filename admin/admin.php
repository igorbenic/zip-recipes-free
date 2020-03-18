<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

if ( ! class_exists( "zrdn_admin" ) ) {
	class zrdn_admin {
		private static $_this;
		public $error_message = "";
		public $success_message = "";
		public $task_count = 0;

		function __construct() {
			if ( isset( self::$_this ) ) {
				wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.',
					get_class( $this ) ) );
			}

			self::$_this = $this;


			add_action( 'admin_menu', array( $this, 'register_admin_page' ),
				20 );

		}

		static function this() {
			return self::$_this;
		}


		// Register a custom menu page.
		public function register_admin_page() {
			if ( ! cmplz_user_can_manage() ) {
				return;
			}

			$warnings      = $this->get_warnings( true, true );
			$warning_count = count( $warnings );
			$warning_title = esc_attr( sprintf( '%d plugin warnings',
				$warning_count ) );
			$menu_label    = sprintf( __( 'Complianz %s', 'complianz-gdpr' ),
				"<span class='update-plugins count-$warning_count' title='$warning_title'><span class='update-count'>"
				. number_format_i18n( $warning_count ) . "</span></span>" );


			global $cmplz_admin_page;
			$cmplz_admin_page = add_menu_page(
				__( 'Complianz', 'complianz-gdpr' ),
				$menu_label,
				'manage_options',
				'complianz',
				array( $this, 'main_page' ),
				cmplz_url . 'assets/images/menu-icon.png',
				CMPLZ_MAIN_MENU_POSITION
			);

			add_submenu_page(
				'complianz',
				__( 'Dashboard', 'complianz-gdpr' ),
				__( 'Dashboard', 'complianz-gdpr' ),
				'manage_options',
				'complianz',
				array( $this, 'main_page' )
			);

			add_submenu_page(
				'complianz',
				__( 'Wizard', 'complianz-gdpr' ),
				__( 'Wizard', 'complianz-gdpr' ),
				'manage_options',
				'cmplz-wizard',
				array( $this, 'wizard_page' )
			);

			do_action( 'cmplz_cookiebanner_menu' );

			do_action( 'cmplz_integrations_menu' );

			add_submenu_page(
				'complianz',
				__( 'Settings' ),
				__( 'Settings' ),
				'manage_options',
				"cmplz-settings",
				array( $this, 'settings' )
			);

			add_submenu_page(
				'complianz',
				__( 'Proof of consent', 'complianz-gdpr' ),
				__( 'Proof of consent', 'complianz-gdpr' ),
				'manage_options',
				"cmplz-proof-of-consent",
				array( COMPLIANZ::$cookie_admin, 'cookie_statement_snapshots' )
			);

			do_action( 'cmplz_admin_menu' );

			if ( defined( 'cmplz_free' ) && cmplz_free ) {
				global $submenu;
				$class                  = 'cmplz-submenu';
				$submenu['complianz'][] = array(
					__( 'Upgrade to premium', 'complianz-gdpr' ),
					'manage_options',
					'https://complianz.io/l/pricing'
				);
				if ( isset( $submenu['complianz'][6] ) ) {
					if ( ! empty( $submenu['complianz'][6][4] ) ) // Append if css class exists
					{
						$submenu['complianz'][6][4] .= ' ' . $class;
					} else {
						$submenu['complianz'][6][4] = $class;
					}
				}
			}


		}



	}
}