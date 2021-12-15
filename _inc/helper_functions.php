<?php
namespace ZRDN;
if ( ! function_exists( __NAMESPACE__ . '\zrdn_is_rdb_api_allowed_country' ) ) {

	/**
	 * Check if this website uses a locale that can share the recipes
	 * @return bool
	 */
	function zrdn_is_rdb_api_allowed_country() {

		$allowed = false;
		$confirmed_locales = array(
			"en_AS" => "English (American Samoa)",
			"en_AU" => "English (Australia)",
			"en_BE" => "English (Belgium)",
			"en_BZ" => "English (Belize)",
			"en_BW" => "English (Botswana)",
			"en_CA" => "English (Canada)",
			"en_GU" => "English (Guam)",
			"en_HK" => "English (Hong Kong SAR China)",
			"en_IN" => "English (India)",
			"en_IE" => "English (Ireland)",
			"en_IL" => "English (Israel)",
			"en_JM" => "English (Jamaica)",
			"en_MT" => "English (Malta)",
			"en_MH" => "English (Marshall Islands)",
			"en_MU" => "English (Mauritius)",
			"en_NA" => "English (Namibia)",
			"en_NZ" => "English (New Zealand)",
			"en_MP" => "English (Northern Mariana Islands)",
			"en_PK" => "English (Pakistan)",
			"en_PH" => "English (Philippines)",
			"en_SG" => "English (Singapore)",
			"en_ZA" => "English (South Africa)",
			"en_TT" => "English (Trinidad and Tobago)",
			"en_UM" => "English (U.S. Minor Outlying Islands)",
			"en_VI" => "English (U.S. Virgin Islands)",
			"en_GB" => "English (United Kingdom)",
			"en_US" => "English (United States)",
			"en_ZW" => "English (Zimbabwe)",
			"en" 	=> "English",
		);

		$current_locale = get_locale();

		if (array_key_exists( $current_locale, $confirmed_locales ) ) $allowed = true;

		return $allowed;
	}
}

if ( ! function_exists( __NAMESPACE__ . '\zrdn_use_rdb_api' ) ) {

	/**
	 * Check if this website is allowed to share recipes
	 *
	 * @return bool
	 */
	function zrdn_use_rdb_api() {
		/**
		 * Uncomment to enable recipe sharing
		 */
		return false;
		if (!zrdn_is_rdb_api_allowed_country()) {
			return false;
		}
		$use_rdb_api = false;
		$terms_and_conditions = Util::get_option('recipe_selling_terms_and_conditions') == true ? true : false;
		$copyright = Util::get_option('recipe_selling_copyright') == true ? true : false;
		$contact_email = is_email( Util::get_option('recipe_selling_contact_email')) ? true : false;

		if ($terms_and_conditions && $copyright && $contact_email) {
			$use_rdb_api = true;
		}

		return $use_rdb_api;
	}
}
/**
 * Show a notice with some info
 * @param string $msg
 * @param string $type notice | warning | success
 * @param bool $echo
 * @param bool $include_css
 * @param bool $fadeout
 * @return string|void
 */
function zrdn_notice($msg, $type = 'notice', $echo = true, $include_css=false, $fadeout=false)
{
    if ($msg == '') return;
    $html = "";
    if ($include_css){
        $html .= "<style>
            .zrdn-panel {
              color: #fff;
              background-color: #29b6f6;
              border: 1px solid #29b6f6;
              padding: 10px 15px;
              border-radius: 0.25rem;
              margin: 10px 0;
            }

            .zrdn-panel.zrdn-success {
              background-color: #61ce70;
              border-color: #61ce70;
            }
            .zrdn-panel.zrdn-warning {
              background-color: #f8be2e;
              border-color: #f8be2e;
            }
            </style>";
    }
    $uid = rand (10,10000);
    if ($fadeout) {
        $html .= "
        <script>
            jQuery(document).ready(function ($) {
                console.log('hide alert');
                var zrdn_alert = $('.alert-".$uid."');
                if (zrdn_alert.length) {
                    zrdn_alert.delay(1500).fadeOut(800);
                }
            });
        </script>";
    }
    $html .= '<div class="zrdn-panel alert-'.$uid.' zrdn-' . $type . ' ">' . $msg . '</div>';
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}
