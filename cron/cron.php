<?php

defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
/*
  Schedule crons
*/
add_action( 'plugins_loaded', 'zrdn_schedule_cron' );
function zrdn_schedule_cron() {
//	add_action( 'admin_init', array( ZRDN\ZipRecipes::$recipe_sharing, 'daily_sync' ) );
}

add_filter( 'cron_schedules', 'zrdn_filter_cron_schedules' );
function zrdn_filter_cron_schedules( $schedules ) {
	$schedules['zrdn_monthly'] = array(
		'interval' => MONTH_IN_SECONDS,
		'display'  => __( 'Once every month' )
	);
	$schedules['zrdn_weekly']  = array(
		'interval' => WEEK_IN_SECONDS,
		'display'  => __( 'Once every week' )
	);
	$schedules['zrdn_daily']   = array(
		'interval' => DAY_IN_SECONDS,
		'display'  => __( 'Once every day' )
	);

	return $schedules;
}