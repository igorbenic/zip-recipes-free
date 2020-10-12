<?php
namespace ZRDN;

defined('ABSPATH') or die("you do not have acces to this page!");
if (!class_exists("zrdn_review")) {
    class zrdn_review
    {
        private static $_this;

        function __construct()
        {
            if (isset(self::$_this))
                wp_die(sprintf(__('%s is a singleton class and you cannot create a second instance.', 'zip-recipes'), get_class($this)));
//	        update_option( 'zrdn_review_notice_shown', false );
//	        update_option('zrdn_activation_time', strtotime("-3 month"));
            self::$_this = $this;
            //show review notice, only to free users
            if (defined("ZRDN_FREE") && !is_multisite()) {
                if (!get_option('zrdn_review_notice_shown') && get_option('zrdn_activation_time') && get_option('zrdn_activation_time') < strtotime("-1 month")){
                    add_action('wp_ajax_zrdn_dismiss_review_notice', array($this, 'dismiss_review_notice_callback'));

                    add_action('admin_notices', array($this, 'show_leave_review_notice'));
                    add_action('admin_print_footer_scripts', array($this, 'insert_dismiss_review'));
                }

                //set a time for users who didn't have it set yet.
                if (!get_option('zrdn_activation_time')){
                    update_option('zrdn_activation_time', time());
                }
            }
	        add_action('admin_init', array($this, 'process_get_review_dismiss' ));

        }

        static function this()
        {
            return self::$_this;
        }

        public function show_leave_review_notice()
        {
	        if (isset( $_GET['zrdn_dismiss_review'] ) ) return;
            /*
             * Prevent notice from being shown on Gutenberg page, as it strips off the class we need for the ajax callback.
             *
             * */
            $screen = get_current_screen();
            if ( $screen->parent_base === 'edit' ) return;

            global $wpdb;
            $table = $wpdb->prefix . "amd_zlrecipe_recipes";
            $count = $wpdb->get_var("SELECT count(*) FROM $table WHERE post_id!=0 OR post_id!=null");
            $count = intval($count);

            if ($count<6) return;

            $intro = sprintf(__('You already have %s recipes on your site, awesome!', 'zip-recipes'), $count);

            ?>
            <style>
                .zrdn-container {
                    display: flex;
                    padding:12px;
                }
                .zrdn-container .dashicons {
                    margin-left:10px;
                    margin-right:5px;
                }
                .zrdn-review-image img{
                    margin-top:0.5em;
                }
                .zrdn-buttons-row {
                    margin-top:10px;
                    display: flex;
                    align-items: center;
                }
            </style>
            <div id="message" class="updated fade notice is-dismissible zrdn-review really-simple-plugins" style="border-left:4px solid #333">
                <div class="zrdn-container">
                    <div class="zrdn-review-image"><img width=80px" src="<?php echo ZRDN_PLUGIN_URL?>images/zip-icon-pink.svg" style="height:86px;margin:5px" alt="review-logo"></div>
                    <div style="margin-left:30px">
                        <p>
	                    <?php echo $intro ?>&nbsp;
	                    <?php printf(__('If you have a moment, please consider leaving a review on WordPress.org to spread the word. We greatly appreciate it! If you have any questions or feedback, leave us a %smessage%s.', 'zip-recipes'),'<a href="https://ziprecipes.net/contact" target="_blank">', '</a>'); ?>
                        </p>
                        <i>- Rogier</i>
                        <div class="zrdn-buttons-row">
                            <a class="button button-primary" target="_blank"
                               href="https://wordpress.org/support/plugin/zip-recipes/reviews/#new-post"><?php _e('Leave a review', 'zip-recipes'); ?></a>

                            <div class="dashicons dashicons-calendar"></div><a href="#" id="maybe-later"><?php _e('Maybe later', 'zip-recipes'); ?></a>
                            <div class="dashicons dashicons-no-alt"></div><a href="<?php echo add_query_arg(array('page'=>'zrdn-settings', 'zrdn_dismiss_review'=>1), admin_url('admin.php') )?>" class="review-dismiss"><?php _e('Don\'t show again', 'zip-recipes'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php

        }

        /**
         * Insert some ajax script to dismiss the review notice, and stop nagging about it
         *
         * @since  2.0
         *
         * @access public
         *
         * type: dismiss, later
         *
         */

        public function insert_dismiss_review()
        {
            $ajax_nonce = wp_create_nonce("zrdn_dismiss_review");
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function ($) {
                    $(".zrdn-review.notice.is-dismissible").on("click", ".notice-dismiss", function (event) {
                        zrdn_dismiss_review('dismiss');
                    });
                    $(".zrdn-review.notice.is-dismissible").on("click", "#maybe-later", function (event) {
                        zrdn_dismiss_review('later');
                        $(this).closest('.zrdn-review').remove();
                    });
                    $(".zrdn-review.notice.is-dismissible").on("click", ".review-dismiss", function (event) {
                        zrdn_dismiss_review('dismiss');
                        $(this).closest('.zrdn-review').remove();
                    });

                    function zrdn_dismiss_review(type) {
                        var data = {
                            'action': 'zrdn_dismiss_review_notice',
                            'type': type,
                            'token': '<?php echo $ajax_nonce; ?>'
                        };
                        $.post(ajaxurl, data, function (response) {
                        });
                    }
                });
            </script>
            <?php
        }

        /**
         * Process the ajax dismissal of the review message.
         *
         * @since  2.1
         *
         * @access public
         *
         */

        public function dismiss_review_notice_callback()
        {
            $type = isset($_POST['type']) ? $_POST['type'] : false;

            if ($type === 'dismiss') {
                update_option('zrdn_review_notice_shown',true);
            }
            if ($type === 'later') {
                //Reset activation timestamp, notice will show again in one month.
                update_option('zrdn_activation_time', time());
            }

            wp_die(); // this is required to terminate immediately and return a proper response
        }

	    /**
	     * Dismiss review notice with get, which is more stable
	     */

	    public function process_get_review_dismiss(){
		    if (isset( $_GET['zrdn_dismiss_review'] ) ){
			    update_option( 'zrdn_review_notice_shown', true );
		    }
	    }
    }
}
$zrdn_review = new zrdn_review();
