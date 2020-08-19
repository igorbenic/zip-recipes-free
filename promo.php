<?php
namespace ZRDN;

/**
 * @deprecated
 * promo on the edit page
 * @param string $html
 *
 * @return string|void
 * @throws \Exception
 */

function zrdn_discount($html='') {
	if (get_option('zrdn_discount_dismissed')) return;
	$activation_time = get_option('zrdn_activation_time')-1;
	$time_passed = time() - $activation_time;

	if ($activation_time && ($time_passed<24*HOUR_IN_SECONDS) ){

		$time_left = (24*HOUR_IN_SECONDS-$time_passed);
		$zero    = new \DateTime("@0");
		$offset  = new \DateTime("@$time_left");
		$diff    = $zero->diff($offset);
		$h = sprintf("%02d", $diff->h);
		$m = sprintf("%02d", $diff->i);
		$s = sprintf("%02d", $diff->s);

		$args = array(
			'discount_code'=>'bFIsbJ45',
			'hours_left'=> $h,
			'minutes_left'=> $m,
			'seconds_left'=> $s,
		);

		$html = Util::render_template('promo.php', $args);
	}

	return $html;
}
//add_filter('zrdn__editpage_promo', __NAMESPACE__.'\zrdn_discount', 10, 1);



add_action('admin_init', __NAMESPACE__.'\zrdn_set_activation_time');
function zrdn_set_activation_time(){
	if (!get_option('zrdn_activation_time')) {
		update_option('zrdn_activation_time', time());
	}
}

add_action('wp_ajax_zrdn_dismiss_discount_notice', __NAMESPACE__.'\zrdn_dismiss_discount');
function zrdn_dismiss_discount(){
	update_option('zrdn_discount_dismissed', true);
}
