<?php

namespace ZRDN;

class UsageStats {
	const DESCRIPTION = "Stats and telemetry.";
	const VERSION = "1.0";
	public $suffix = '';

	function __construct() {
		if ( $this->isDisabled() ) {
			return false;
		}

		$this->suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

		add_action( "zrdn__init_hooks", array( $this, 'init_hooks' ) );
	}

	private function isDisabled()
	{
		$disabled = false;
		$pluginOptions = get_option(ZipRecipes::PLUGIN_OPTION_NAME, array());
		if(isset($pluginOptions[get_class()]) && $pluginOptions[get_class()]["active"]) {
			$disabled = false;
		}
		else {
			$disabled = true;
		}

		return $disabled;
	}

	public function init_hooks() {
		Util::log( "In init_hooks" );

		// Shortcode
		add_action("zrdn__usage_stats", array($this, 'load_piwik'));
	}

	public function load_piwik() {
		// show piwik script
		wp_enqueue_script("zrdn_piwik", ZRDN_PLUGIN_URL . "scripts/piwik{$this->suffix}.js", /*deps*/ array(), /*version*/ "2.0", /*in_footer*/ true);
	}
}