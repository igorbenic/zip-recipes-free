<?php
namespace ZRDN;

add_filter('zrdn__settings_promo', __NAMESPACE__.'\zrdn_settings_promo', 10, 1);
/**
 * promo on the settings page
 * @param $output
 * @return string
 */

function zrdn_settings_promo($output) {

    $discount = zrdn_discount();
    if (strlen($discount)>0) {
        $html = $discount;
    } else {
        $html = Util::view('settings_promo', array());
    }
    return $html;
}

add_filter('zrdn__editpage_promo', __NAMESPACE__.'\zrdn_discount', 10, 1);
/**
 * promo on the edit page
 * @param $output
 * @return string
 */

function zrdn_discount($html='') {
    if (get_option('zrdn_discount_dismissed')) return;

    $activation_time = get_option('zrdn_activation_time');
    $time_passed = time() - $activation_time;

    if ($activation_time && ($time_passed<24*HOUR_IN_SECONDS) ){

        $time_left = (24*HOUR_IN_SECONDS-$time_passed);
        $zero    = new \DateTime("@0");
        $offset  = new \DateTime("@$time_left");
        $diff    = $zero->diff($offset);
        $h = sprintf("%02d", $diff->h);
        $m = sprintf("%02d", $diff->i);
        $s = sprintf("%02d", $diff->s);

        $html =  Util::view('discount', array(
            'discount_code'=>'bFIsbJ45',
            'hours_left'=> $h,
            'minutes_left'=> $m,
            'seconds_left'=> $s,
        ));
    }

    return $html;
}



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
