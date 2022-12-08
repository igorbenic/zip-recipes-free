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

			add_action( 'zrdn_update_option', array($this, 'maybe_enable_recipe_sharing' ), 10,4);
			add_action( 'admin_init', array($this, 'generate_recipe_sharing_api_key'));
			add_action( 'admin_init', array($this, 'check_if_sync_should_run'));
			add_action( 'admin_init', array($this, 'check_if_validation_should_run'));
            add_filter( "zrdn_tabs", array($this, 'add_recipe_sharing_tab'), 11, 2);
            add_action( 'wp_ajax_zrdn_dismiss_sharing_notice', array($this, 'dismiss_sharing_notice'));
            add_action( "admin_notices", array($this, 'show_notice_sharing'));
		}

		static function this() {
			return self::$_this;
		}

		//chunked, progress
		public function daily_sync(){
			if ( ! wp_doing_cron() && ! current_user_can( 'manage_options' )) {
				return false;
			}
			
			if ( ! zrdn_use_rdb_api() ) {
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

					// add post URL where the recipe is located
					$data[ $language ][$index]->recipe_url = intval($recipe->post_id) ? get_permalink($recipe->post_id) : false;
					$index++;
				}
			}

			$data['count'] = $count_all;
			return $data;
		}

		/**
		 * Runs once a week to check if the CDB should be synced
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
			if (!zrdn_use_rdb_api()) return;

			// update_option( 'zrdn_validation_completed', false );
			if ( !get_option( 'zrdn_validation_completed') )  {
			    $offset = get_option('zrdn_validate_recipes_offset', 0);
				//get most popular recipes
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
		 * @param int $recipe_id
		 * @param int $post_id
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
			if (zrdn_use_rdb_api()) return;
		
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

		/**
		 * Get the api key
		 * @return string|bool
		 */
		public function get_api_key(){
			return !empty(Util::get_option('rdb_api_key')) ? Util::get_option('rdb_api_key') : false;
		}


        /**
		 *
		 * @return bool
		 */
		public function sharing_active(){
			return !empty($this->get_api_key()) && get_option('zrdn_enable_recipe_selling');
		}

        /**
         * Check if the sharing settings were changed.
         *
         * @param $new_value
         * @param $old_value
         * @param $fieldname
         * @param $source
         *
         * @return mixed
         */
        public function maybe_enable_recipe_sharing($new_value, $old_value, $fieldname, $source) {
            if ( !current_user_can('manage_options')) {
                return $new_value;
            }

            if ( $new_value === $old_value ) {
                return $new_value;
            }

            if ( $fieldname === 'recipe_selling_terms_and_conditions'
                || $fieldname === 'recipe_selling_copyright'
                || $fieldname === 'recipe_selling_contact_email'
                || $fieldname === 'recipe_selling_paypal_email'
            ) {
                update_option( 'zrdn_recipe_selling_updated', true );

                if (
                    boolval( $_POST['zrdn_recipe_selling_terms_and_conditions'] )
                    && boolval( $_POST['zrdn_recipe_selling_copyright'] )
                    && is_email( $_POST['zrdn_recipe_selling_contact_email'] )
                ) {
                    update_option( 'zrdn_enable_recipe_selling', true );
                } else {
                    update_option( 'zrdn_enable_recipe_selling', false );
                }

            }
            return $new_value;
        }

        public function generate_recipe_sharing_api_key(){
			if ( !current_user_can('manage_options')) return;
			$msg   = '';
			$error = false;

			if (!get_option('zrdn_recipe_selling_updated')) {
				$msg   = 'Selling info not updated';
				$error = true;
			}

			if (!get_option('zrdn_enable_recipe_selling')) {
				$msg   = 'sharing not enabled';
				$error = true;
			}

			$website_url = get_site_url();
			$user_info = get_userdata(get_current_user_id());
			$full_name = $user_info->display_name;
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

        /**
         * Add recipe sharing tab
         * @param $tabs
         *
         * @return mixed
         */

        public function add_recipe_sharing_tab($tabs){
            if (!current_user_can('manage_options')) return $tabs;

            $tabs['recipe_sharing'] = array(
                'title' => __('Monetize your recipes', 'zip-recipes'),
                'page' => 'zrdn-recipe-sharing',
            );
            return $tabs;
        }

        /**
         * Zip Recipes general settings page
         */
        public static function recipe_sharing_page() {

            if (!current_user_can('manage_options')) return;
            do_action('zrdn_on_settings_page' );
            $field = ZipRecipes::$field;

            ?>
            <div class="wrap" id="zip-recipes">
                <?php //this header is a placeholder to ensure notices do not end up in the middle of our code ?>
                <h1 class="zrdn-notice-hook-element"></h1>

                <div id="zrdn-dashboard">

                    <?php Util::settings_header(apply_filters('zrdn_tabs', array()), true);?>
                    <style>#zrdn-show-toggles{display: none;}</style>
                    <div class="zrdn-main-container">
                        <!--    Dashboard tab   -->
                        <div id="monetize" class="tab-content current">
                            <form id="zrdn-monetize" method="POST">

                                <?php
                                $grid_items = Util::grid_items('monetize');
                                $container = zrdn_grid_container();
                                $element = zrdn_grid_element();
                                $output = '';
                                foreach ($grid_items as $index => $grid_item) {
                                    ob_start();
                                    if ( isset( $grid_item['template' ] ) ) {
                                        echo Util::render_template($grid_item['template']);
                                    } else {
                                        $fields = Util::get_fields($grid_item['source']);
                                        foreach ( $fields as $fieldname => $field_args ) {
                                            $field->get_field_html( $field_args , $fieldname);
                                        }
                                        if (isset($grid_item['footer'])){
                                            echo Util::render_template($grid_item['footer']);
                                        } else {
                                            $field->save_button();
                                        }


                                    }
                                    $contents = ob_get_clean();
                                    $output .= str_replace(array('{class}', '{title}', '{content}', '{index}','{controls}'), array($grid_item['class'], $grid_item['title'],  $contents, $index, $grid_item['controls']), $element);
                                }
                                echo str_replace('{content}', $output, $container);
                                ?>
                            </form>
                        </div>

                        <?php do_action('zrdn_tab_content'); ?>

                    </div>

                </div><!--dashboard close -->


            </div><!--wrap close -->
            <?php
        }

        /**
         * Show notice
         */

        public function show_notice_sharing()
        {
            $screen = get_current_screen();
            if ( $screen->parent_base === 'edit' ) return;
            add_action('admin_print_footer_scripts', array($this, 'dismiss_sharing_notice_script'));
            $dismissed = get_option('zrdn_sharing_notice_dismissed');
            $link_open = '<a class="button button-primary"" href="'.admin_url('admin.php?page=zrdn-recipe-sharing').'">';
            if (!$dismissed) {

            ob_start();
            _e("How it works: ", 'zip-recipes'); ?>
            <ul>
                <li><?php _e('ZIP will share your recipes for non-public and offline publications.', 'zip-recipes'); ?></li>
                <li><?php _e('Earn 1 dollar per month for every recipe shared', 'zip-recipes'); ?></li>
                <li><?php _e("You can always add, edit or remove recipes. You own your recipes. Always.", "zip-recipes") ?></li>
                <li><?php _e("With the 1-minute set-up you can generate more income with ease.", "zip-recipes") ?></li>
            </ul>

            <?php
            $content = ob_get_clean();

            ob_start();
            ?>
            <form action="" method="post">
                <?php wp_nonce_field('zrdn_nonce', 'zrdn_nonce'); ?>
                <?php echo $link_open ?>
                    <?php _e("1-Minute Configuration", "zip-recipes"); ?>
                </a>
                <button class="notice-dismiss notice-dismiss-sharing" target="_blank"></button>
                <a href="#" class="button button-default notice-dismiss-sharing"><?php _e("Thanks, but no thanks", "zip-recipes"); ?></a>
            </form>
            <?php
            $footer = ob_get_clean();

            $class = apply_filters("zrdn_activation_notice_classes", "zrdn-dismiss-notice is-dismissible");
            $title = __("Monetize your recipes", "zip-recipes");
            echo $this->notice_html( $class, $title, $content, $footer);
            }
        }

        /**
         * Process the ajax dismissal of the success message.
         *
         * @since  2.0
         *
         * @access public
         *
         */

        public function dismiss_sharing_notice()
        {
            check_ajax_referer('zrdn-dismiss-sharing-notice', 'nonce');
            update_option('zrdn_sharing_notice_dismissed', true);
            wp_die();
        }

        public function dismiss_sharing_notice_script()
        {
            $ajax_nonce = wp_create_nonce("zrdn-dismiss-sharing-notice");
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function ($) {

                    $(".zrdn-dismiss-notice.notice.is-dismissible").on("click", ".notice-dismiss", function (event) {
                        var data = {
                            'action': 'zrdn_dismiss_sharing_notice',
                            'nonce': '<?php echo $ajax_nonce; ?>'
                        };

                        $.post(ajaxurl, data, function (response) {

                        });
                    });
                    $(".zrdn-dismiss-notice.notice.is-dismissible").on("click", ".notice-dismiss-sharing", function (event) {
                        var data = {
                            'action': 'zrdn_dismiss_sharing_notice',
                            'nonce': '<?php echo $ajax_nonce; ?>'
                        };

                        $.post(ajaxurl, data, function (response) {
                            $(".zrdn-dismiss-notice").hide(300);
                        });
                    });
                });
            </script>
            <?php
        }

        /**
         * @param string $class
         * @param string $title
         * @param string $content
         * @param string|bool $footer
         * @return false|string
         *
         * @since 4.0
         * Return the notice HTML
         *
         */

        public function notice_html($class, $title, $content, $footer=false) {

            ob_start();
            ?>
            <?php if ( is_rtl() ) { ?>
                <style>
                    .zrdn-notice .zrdn-notice-header {
                        justify-content: flex-start;
                    }
                    .zrdn-badge.new{
                        background-color: #F343A0;
                        margin: 9px 0 4px 8px;
                        font-weight: 600;
                        font-size: 11px;
                        padding: 5px 20px;
                        color: #fff;
                        border-radius: 20px;
                    }
                    #zrdn-message .error{
                        border-right-color:#d7263d;
                    }
                    .activate-ssl .button {
                        margin-bottom: 5px;
                    }

                    #zrdn-message .button-primary {
                        margin-left: 10px;
                    }

                    .zrdn-notice-header {
                        height: 60px;
                        border-bottom: 1px solid #dedede;
                        display: flex;
                        flex-direction: row;
                        justify-content: space-between;
                        align-items: center;
                        padding-right: 25px;
                    }
                    .zrdn-notice-header h1 {
                        font-weight: bold;
                    }

                    .zrdn-notice-content {
                        margin-top: 20px;
                        padding-bottom: 20px;
                        padding-right: 25px;
                    }

                    .zrdn-notice-footer {
                        border-top: 1px solid #dedede;
                        height: 35px;
                        display: flex;
                        align-items: center;
                        padding-top: 10px;
                        padding-bottom: 10px;
                        margin-right: 25px;
                        margin-left: 25px;
                    }

                    #zrdn-message {
                        padding: 0;
                        border-right-color: #333;
                    }

                    #zrdn-message .zrdn-notice-li::before {
                        vertical-align: middle;
                        margin-left: 25px;
                        color: lightgrey;
                        content: "\f345";
                        font: 400 21px/1 dashicons;
                    }

                    #zrdn-message ul {
                        list-style: none;
                        list-style-position: inside;
                    }
                    #zrdn-message li {
                        margin-right:30px;
                        margin-bottom:10px;
                    }
                    #zrdn-message li:before {
                        background-color: #F343A0;
                        color: #fff;
                        height: 10px;
                        width: 10px;
                        border-radius:50%;
                        content: '';
                        position: absolute;
                        margin-top: 5px;
                        margin-right:-30px;
                    }

                    .settings_page_rlzrdn_really_simple_ssl #wpcontent #zrdn-message, .settings_page_zip-recipes #wpcontent #zrdn-message {
                        margin: 20px;
                    }
                    <?php echo apply_filters('zrdn_pro_inline_style', ''); ?>
                </style>
            <?php } else { ?>
                <style>
                    .zrdn-notice .zrdn-notice-header {
                        justify-content: flex-start;
                    }
                    .zrdn-badge.new{
                        background-color: #F343A0;
                        margin: 9px 0 4px 8px;
                        font-weight: 600;
                        font-size: 11px;
                        padding: 5px 20px;
                        color: #fff;
                        border-radius: 20px;
                    }
                    #zrdn-message .error{
                        border-left-color:#d7263d;
                    }
                    .activate-ssl .button {
                        margin-bottom: 5px;
                    }

                    #zrdn-message .button-primary {
                        margin-right: 10px;
                    }

                    .zrdn-notice-header {
                        height: 60px;
                        border-bottom: 1px solid #dedede;
                        display: flex;
                        flex-direction: row;
                        justify-content: space-between;
                        align-items: center;
                        padding-left: 25px;
                    }
                    .zrdn-notice-header h1 {
                        font-weight: bold;
                    }

                    .zrdn-notice-content {
                        margin-top: 20px;
                        padding-bottom: 20px;
                        padding-left: 25px;
                    }

                    .zrdn-notice-footer {
                        border-top: 1px solid #dedede;
                        height: 35px;
                        display: flex;
                        align-items: center;
                        padding-top: 10px;
                        padding-bottom: 10px;
                        margin-left: 25px;
                        margin-right: 25px;
                    }

                    #zrdn-message {
                        padding: 0;
                        border-left-color: #333;
                    }

                    #zrdn-message .zrdn-notice-li::before {
                        vertical-align: middle;
                        margin-right: 25px;
                        color: lightgrey;
                        content: "\f345";
                        font: 400 21px/1 dashicons;
                    }

                    #zrdn-message ul {
                        list-style: none;
                        list-style-position: inside;
                    }
                    #zrdn-message li {
                        margin-left:30px;
                        margin-bottom:10px;
                    }
                    #zrdn-message li:before {
                        background-color: #F343A0;
                        color: #fff;
                        height: 10px;
                        width: 10px;
                        border-radius:50%;
                        content: '';
                        position: absolute;
                        margin-top: 5px;
                        margin-left:-30px;
                    }

                    .settings_page_rlzrdn_really_simple_ssl #wpcontent #zrdn-message, .settings_page_zip-recipes #wpcontent #zrdn-message {
                        margin: 20px;
                    }
                    <?php echo apply_filters('zrdn_pro_inline_style', ''); ?>
                </style>
            <?php } ?>
            <div id="zrdn-message" class="notice <?php echo $class?> really-simple-plugins">
                <div class="zrdn-notice">
                    <div class="zrdn-notice-header">
                        <h1><?php echo $title ?></h1><span class="zrdn-badge new">NEW</span>
                    </div>
                    <div class="zrdn-notice-content">
                        <?php echo $content ?>
                    </div>
                    <?php
                    if ($footer ) { ?>
                        <div class="zrdn-notice-footer">
                            <?php echo $footer;?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php

            $content = ob_get_clean();
            return $content;
        }


    } // end of class
} // end of class exists