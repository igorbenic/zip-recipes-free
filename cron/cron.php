<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

//switch to Cron here.

/*
  Schedule cron jobs if useCron is true
  Else start the functions.
*/
add_action( 'plugins_loaded', 'zrdn_schedule_cron' );
function zrdn_schedule_cron() {
	$useCron = true;
	if ( $useCron ) {
		if ( ! wp_next_scheduled( 'zrdn_every_week_hook' ) ) {
			wp_schedule_event( time(), 'zrdn_weekly',
				'zrdn_every_week_hook' );
		}

		if ( ! wp_next_scheduled( 'zrdn_every_day_hook' ) ) {
			wp_schedule_event( time(), 'zrdn_daily', 'zrdn_every_day_hook' );
		}

		if ( ! wp_next_scheduled( 'zrdn_every_month_hook' ) ) {
			wp_schedule_event( time(), 'zrdn_monthly',
				'zrdn_every_month_hook' );
		}

		add_action( 'zrdn_every_day_hook', array( ZRDN\ZipRecipes::$recipe_sharing, 'daily_sync' ) );

	} else {

		add_action( 'admin_init', array( ZRDN\ZipRecipes::$recipe_sharing, 'daily_sync' ) );
		// add_action( 'admin_init', array( ZRDN\ZipRecipes::$recipe_sharing, 'update_user' ) );
		//add_action( 'admin_init', array( ZRDN\ZipRecipes::$recipe_sharing, 'revoke_all_recipes_from_sharing' ) );

		// add_action( 'init',
		// 	array( zrdn_recipe_sharing_admin::sync_recipes(), 'cron_check_last_updated_status' ),
		// 	100 );
	}
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


register_deactivation_hook( __FILE__, 'zrdn_clear_scheduled_hooks' );
function zrdn_clear_scheduled_hooks() {
	wp_clear_scheduled_hook( 'zrdn_every_week_hook' );
	wp_clear_scheduled_hook( 'zrdn_every_day_hook' );
}



