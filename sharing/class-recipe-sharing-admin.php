<?php
namespace ZRDN;
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );



if ( ! class_exists( "ZRDN_recipe_sharing_admin" ) ) {

	class ZRDN_recipe_sharing_admin {
		private static $_this;

		function __construct() {
			if ( isset( self::$_this ) ) {
				wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.',
					get_class( $this ) ) );
			}

			self::$_this = $this;

			add_action('admin_init', array($this, 'update_user' ));
			add_action('admin_init', array($this, 'check_if_sharing_form_was_updated' ));
			add_action('admin_init', array($this, 'check_if_sync_should_run'));
			add_action('admin_init', array($this, 'check_if_validation_should_run'));
			
		}

		static function this() {
			return self::$_this;
		}


		//chunked, progress
		public function daily_sync(){
			if ( ! wp_doing_cron() && ! current_user_can( 'manage_options' )) {
				error_log('No permissions');
				return false;
			}
			
			if ( ! use_rdb_api() ) {
				error_log('No RDB API used');
				return false;
			} else {
				update_option( 'zrdn_sync_started', true);
				update_option('zrdn_syncable_recipes_offset', 0);
			}
		}

		public function check_if_sync_should_run(){
			if (get_option( 'zrdn_sync_started' )) {
				//get last offset
				$offset = get_option('zrdn_syncable_recipes_offset', 0);
				$recipes = $this->get_syncable_recipes($offset);
				$offset += 20;
				update_option('zrdn_syncable_recipes_offset', $offset);
				if ($recipes['count'] == 0) {
					  update_option( 'zrdn_sync_started', false );
				} else {
					$this->sync_recipes($recipes);
				}
			}
		}

		public function get_syncable_recipes( $offset ) {
			$languages          = $this->get_supported_languages();
			$data               = array();
			$count_all   		= 0;

			foreach ( $languages as $language ) {
				$args = array( 
					'offset' 	=> $offset,
					'number' 	=> 20,
					'sync' 		=> true, 
					'language' 	=> $language,
				);
				$recipes = Util::get_recipes( $args );

				$index = 0;
				foreach ( $recipes as $recipe ) {
					if( $recipe->zip_sharing_status !== 'waiting_approval' && $recipe->zip_sharing_status !== 'approved') continue;
					if( !$recipe->share_this_recipe ) continue;
					if( get_option('zrdn_demo_recipe_id') == $recipe->recipe_id) continue;
					$count_all += 1;
					
					$data[ $language ][$index] = $recipe;
					$full_size_image = wp_get_attachment_image_src($recipe->recipe_image_id, 'full');
					$data[ $language ][$index]->featured_image_url = $full_size_image[0];
					$nutrition_api_enabled = Util::get_option('AutomaticNutrition'); 
					if ($nutrition_api_enabled) {
						$data[ $language ][$index]->nutrition_api_enabled = true;
					} else {
						$data[ $language ][$index]->nutrition_api_enabled = false;
					}

					$recipe_class = new Recipe($recipe->recipe_id);
					
					// add ingredients as array
					$data[ $language ][$index]->nested_instructions = $recipe_class->nested_instructions[0];
					$data[ $language ][$index]->nested_ingredients = $recipe_class->nested_ingredients[0];
					$data[ $language ][$index]->category = array();

					// Make categories an array. Fallback for legacy categories
					// Legacy categories 
					if((!is_array($recipe_class->categories) || count($recipe_class->categories)==0) && $recipe_class->category){
						$cat_index = 0;
						$categories = explode(",", $recipe_class->category);

						foreach ($categories as $category_name ) {
							$data[ $language ][$index]->category[$cat_index] = trim($category_name);
							$cat_index++;
						}
					}

					// New categories
					if (is_array($recipe_class->categories) && count($recipe_class->categories)>0){
						$cat_index = 0;
						foreach ($recipe_class->categories as $category_id ) {
							$data[ $language ][$index]->category[$cat_index] = get_the_category_by_ID($category_id);
							$cat_index++;
						}
					}


					$serving_size = $data[ $language ][$index]->serving_size;
					if (is_numeric($serving_size)) {
						$data[ $language ][$index]->serving_size = $serving_size . ' portion';
					}
					
					//$c = new Recipe( $recipe->recipe_id, $language );
					
					$index++;
				}
			}

			$data['count'] = $count_all;
			return $data;
		}

		/**
		 * Runs once a week to check if the CDB should be synced
		 *
		 * @hooked zrdn_every_week_hook
		 */

		public function sync_recipes( $recipes ) {
			//if ( !function_exists('curl_version') ) return false;
			$error = false;
			$msg = "";
			$api_key = $this->get_api_key();

			$data = $recipes;
			$data['sharing_data']['nutrition_api_enabled'] = Util::get_option('AutomaticNutrition') ? 'enabled' : 'disabled';
			$data['sharing_data']['api_key'] = $api_key;


			//if no syncable recipes are found, exit.
			if ( $data['count'] == 0 ) {
				update_option( 'zrdn_sync_recipes_complete', true );
				$msg   = __( "No syncable recipes are found.",
					"zip-recipes" );
				$error = true;
			}

			unset( $data['count'] );

			if ( get_transient( 'zrdn_recipedatabase_request_active' ) ) {
				$error = true;
				$msg
				       = __( "A request is already running. Please be patient until the current request finishes",
					"zip-recipes" );
			}
			if ( ! $error ) {
				set_transient( 'zrdn_recipedatabase_request_active', true,
					MINUTE_IN_SECONDS );
				$data['website'] = '<a href="' . esc_url_raw( site_url() )
				                   . '">' . esc_url_raw( site_url() ) . '</a>';
				$json            = json_encode( $data );
				$endpoint        = trailingslashit( ZRDN_RECIPEDATABASE_URL )
				                   . 'wp-json/recipedatabase/v1/sharerecipes';


				$ch = curl_init();

				curl_setopt( $ch, CURLOPT_URL, $endpoint );
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
				curl_setopt( $ch, CURLOPT_POST, 1 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Content-Length: ' . strlen( $json )
					)
				);

				$result = curl_exec( $ch );
		
				$error  = ( $result == 0
				            && strpos( $result,
						'<title>502 Bad Gateway</title>' ) === false ) ? false
					: true;
				if ( $error ) {
					$msg = __( "Could not connect to recipedatabase.org",
						"zip-recipes" );
				}

				curl_close( $ch );
				delete_transient( 'zrdn_recipedatabase_request_active' );
			}

			if ( ! $error ) {
				$result = json_decode( $result );
				if ( isset( $result->data->error ) ) {
					$msg   = $result->data->error;
					$error = true;
				} else {
					$result = $result->data;

					foreach ($result as $recipe_id => $edamam_sharing_status) {
						$recipe = new Recipe($recipe_id);
        				if (isset($recipe) && isset($sharing_status)) {
	        				$recipe->edamam_sharing_status = $edamam_sharing_status->status;
	        				$recipe->save();
        				}
        				
					}
				}
			}

			if ( ! $error && isset( $result->status )
			     && $result->status !== 200
			) {
				$error = true;
			}
			
			update_option( 'zrdn_sync_recipes_complete', true );
			return $msg;
		}

		/**
		 * Run a validation process for sharing
		 */

		public function check_if_validation_should_run(){
			// update_option( 'zrdn_validation_completed', false );
			if ( get_option( 'zrdn_validation_completed') !== true)  {
				$offset = get_option('zrdn_validate_recipes_offset', 0);

				//get most populare recipes
				$args = array(
					'order_by' => 'desc',
					'order' => 'DESC',
					'number' => 20,
					'offset' => $offset,
				);

				$recipes = Util::get_recipes($args);
				$offset += 20;
				update_option('zrdn_validate_recipes_offset', $offset);

				if (count($recipes) == 0) {
					update_option('zrdn_validation_completed', true);
					update_option('zrdn_validate_recipes_offset', 0);
				} else {
					foreach ($recipes as $recipe) {
						if( $recipe->zip_sharing_status == 'approved' || $recipe->zip_sharing_status == 'declined') continue;
						$this->validate_recipe($recipe->recipe_id, $recipe->post_id);
					}
				}
			}
		}
		/** 
		 * Function for checking if a recipe is ready for sharing with the recipe database.
		 * 
		 * @param bool $count
		 *
		 * @return int|array
		 */

		public function validate_recipe($recipe_id, $post_id){

			$recipe = new Recipe($recipe_id, $post_id);
			$recipe->save();

		}


	

		public function delete_from_sharing($recipe_id){


			$msg   = '';
			$error = false;

			if ( ! $error ) {
				set_transient( 'zrdn_recipedatabase_request_active', true,
					MINUTE_IN_SECONDS );

				$data['recipe_id'] = $recipe_id;
				$data['api_key'] = $this->get_api_key();

				$json            = json_encode( $data );
				$endpoint        = trailingslashit( ZRDN_RECIPEDATABASE_URL )
				                   . 'wp-json/recipedatabase/v1/delete';

				$ch = curl_init();

				curl_setopt( $ch, CURLOPT_URL, $endpoint );
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
				curl_setopt( $ch, CURLOPT_POST, 1 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Content-Length: ' . strlen( $json )
					)
				);

				$result = curl_exec( $ch );

			
				$error  = ( $result == 0
				            && strpos( $result,
						'<title>502 Bad Gateway</title>' ) === false ) ? false
					: true;
				if ( $error ) {
					$msg = __( "Could not connect to recipedatabase.org",
						"zip-recipes" );
				}

				curl_close( $ch );

				
				delete_transient( 'zrdn_recipedatabase_request_active' );
			}

			if ( ! $error ) {
				$result = json_decode( $result );
				
			}

			if ( ! $error && isset( $result->status )
			     && $result->status !== 200
			) {
				$error = true;
			}
		}

		public function revoke_all_recipes_from_sharing(){
			$api_key = $this->get_api_key();
			// recipe_selling should be turned off
			if (use_rdb_api()) return;
		
			// API key should be set
			if (!isset($api_key)) return;
			// revoke_request should only be one
			if (get_option( 'sharing_revoke_request_completed', false ) ) return;

			$msg   = '';
			$error = false;

			if ( ! $error ) {
				set_transient( 'zrdn_recipedatabase_request_active', true,
					MINUTE_IN_SECONDS );

				$data['api_key'] = $api_key;
				

				$json            = json_encode( $data );
				$endpoint        = trailingslashit( ZRDN_RECIPEDATABASE_URL )
				                   . 'wp-json/recipedatabase/v1/revoke';

				$ch = curl_init();

				curl_setopt( $ch, CURLOPT_URL, $endpoint );
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
				curl_setopt( $ch, CURLOPT_POST, 1 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Content-Length: ' . strlen( $json )
					)
				);

				$result = curl_exec( $ch );

			
				$error  = ( $result == 0
				            && strpos( $result,
						'<title>502 Bad Gateway</title>' ) === false ) ? false
					: true;
				if ( $error ) {
					$msg = __( "Could not connect to recipedatabase.org",
						"zip-recipes" );
				}

				curl_close( $ch );

				
				delete_transient( 'zrdn_recipedatabase_request_active' );
			}

			if ( ! $error ) {
				$result = json_decode( $result );
				update_option('sharing_revoke_request_completed', true);
			}

			if ( ! $error && isset( $result->status )
			     && $result->status !== 200
			) {
				$error = true;
			}
		}


		 /**
		 * Get an array of languages used on this site in format array('en' => 'en')
		 *
		 * @param bool $count
		 *
		 * @return int|array
		 */

		public function get_supported_languages( $count = false ) {
			$site_locale = $this->zrdn_sanitize_language( get_locale() );

			$languages = array( $site_locale => $site_locale );

			if ( function_exists( 'icl_register_string' ) ) {
				$wpml      = apply_filters( 'wpml_active_languages', null,
					array( 'skip_missing' => 0 ) );
				/**
				 * WPML has changed the index from 'language_code' to 'code' so
				 * we check for both.
				 */
				$wpml_test_index = reset($wpml);
				if (isset($wpml_test_index['language_code'])){
					$wpml      = wp_list_pluck( $wpml, 'language_code' );
				} elseif (isset($wpml_test_index['code'])) {
					$wpml      = wp_list_pluck( $wpml, 'code' );
				} else {
					$wpml = array();
				}
				$languages = array_merge( $wpml, $languages );
			}

			/**
			 * TranslatePress support
			 * There does not seem to be an easy accessible API to get the languages, so we retrieve from the settings directly
			 */

			if (class_exists('TRP_Translate_Press')){
				$trp_settings = get_option('trp_settings', array());
				if (isset($trp_settings['translation-languages'])) {
					$trp_languages = $trp_settings['translation-languages'];
					foreach( $trp_languages as $language_code){
						$key = substr( $language_code, 0, 2 );
						$languages[$key] = $key;
					}
				}
			}

			if ( $count ) {
				return count( $languages );
			}

			//make sure the en is always available.
			if ( ! in_array( 'en', $languages ) ) {
				$languages['en'] = 'en';
			}


			return $languages;
		}



		/**
		 * Validate a language string
		 *
		 * @param $language
		 *
		 * @return bool|string
		 */

		public function zrdn_sanitize_language( $language ) {
			$pattern = '/^[a-zA-Z]{2}$/';
			if ( ! is_string( $language ) ) {
				return false;
			}
			$language = substr( $language, 0, 2 );

			if ( (bool) preg_match( $pattern, $language ) ) {
				$language = strtolower( $language );

				return $language;
			}

			return false;
		}

		public function get_api_key(){

			$api_key = !empty(Util::get_option('rdb_api_key')) ? Util::get_option('rdb_api_key') : false;
			return $api_key;
		}

		public function update_user(){
			$msg   = '';
			$error = false;

			if (!get_option('zrdn_recipe_selling_updated')) {
				$msg   = 'Selling info not updated';
				$error = true;
			}
			

			if ( !use_rdb_api() ) {
				$error = true;
				$msg
				       = __( 'Recipe sharing is turned off.',
					"zip-recipes" );
			}

			$website_url = get_site_url();
			$full_name = Util::get_option('recipe_selling_full_name') ? Util::get_option('recipe_selling_full_name') : 'deleted';
			$contact_email = Util::get_option('recipe_selling_contact_email');
			$paypal_email = Util::get_option('recipe_selling_paypal_email');
			$api_key = $this->get_api_key();

			if (!isset($website_url)) {
		        $error = true;
		        $msg .= "No website_url. ";
		    }

		    if (!isset($full_name)) {
		        $error = true;
		        $msg .= "No full_name. ";
		    }

		    if (!isset($contact_email)) {
		        $error = true;
		        $msg .= "No contact_email. ";
		    }

		    if (!isset($paypal_email)) {
		        $error = true;
		        $msg .= "No paypal_email. ";
		    }

			if ( get_transient( 'zrdn_recipedatabase_request_active' ) ) {
				$error = true;
				$msg
				       = __( "A request is already running. Please be patient until the current request finishes",
					"zip-recipes" );
			}

			if ( ! $error ) {
				set_transient( 'zrdn_recipedatabase_request_active', true,
					MINUTE_IN_SECONDS );

				$data['website_url'] 	= $website_url;
				$data['full_name'] 		= $full_name;
				$data['contact_email'] 	= $contact_email;
				$data['paypal_email'] 	= $paypal_email;
				$data['api_key'] 		= $api_key;

				$json            = json_encode( $data );
				$endpoint        = trailingslashit( ZRDN_RECIPEDATABASE_URL )
				                   . 'wp-json/recipedatabase/v1/update';

				$ch = curl_init();

				curl_setopt( $ch, CURLOPT_URL, $endpoint );
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
				curl_setopt( $ch, CURLOPT_POST, 1 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Content-Length: ' . strlen( $json )
					)
				);

				$result = curl_exec( $ch );

			
				$error  = ( $result == 0
				            && strpos( $result,
						'<title>502 Bad Gateway</title>' ) === false ) ? false
					: true;
				if ( $error ) {
					$msg = __( "Could not connect to recipedatabase.org",
						"zip-recipes" );
				}

				curl_close( $ch );

				delete_transient( 'zrdn_recipedatabase_request_active' );
			}

			if ( ! $error ) {
				$result = json_decode( $result );
				//recipe creation also searches fuzzy, so we can now change the recipe name to an asterisk value
				//on updates it will still match.
				if ( isset( $result->data->error ) ) {
					$msg   = $result->data->error;
					$error = true;
				} else {
					$result = $result->data;
					if (!empty($result->api_key)) {
						Util::update_option('rdb_api_key', $result->api_key);
					}
					update_option( 'zrdn_recipe_selling_updated', false );	
				}
			}

			if ( ! $error && isset( $result->status )
			     && $result->status !== 200
			) {
				$error = true;
			}
			if (!empty($result->api_key)) {
				return $result->api_key;
			} else {
				return false;
			}
		}

		public function check_if_sharing_form_was_updated(){
			if (isset( $_POST['zrdn_enable_recipe_selling'] ) ){
				update_option( 'zrdn_recipe_selling_updated', true );

				if (
					$_POST['zrdn_recipe_selling_terms_and_conditions'] 
					&& $_POST['zrdn_recipe_selling_copyright'] 
					&& is_email( $_POST['zrdn_recipe_selling_contact_email'] ) 
					&& is_email( $_POST['zrdn_recipe_selling_paypal_email'] )
				) {
					update_option( 'zrdn_enable_recipe_selling', true );
				} else {
					update_option( 'zrdn_enable_recipe_selling', false );
				}
				
			}
		}

	} // end of class
} // end of class exists