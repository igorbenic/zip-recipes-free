<?php
/**
 * Created by PhpStorm.
 * User: gezimhome
 * Date: 2018-04-03
 * Time: 13:56
 */

namespace ZRDN;

abstract class PluginBase {
	public $suffix = '';

	public function __construct() {
		if ( $this->isDisabled() ) {
			return false;
		}

		$this->suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

		return true;
	}

	private function isDisabled() {
		$disabled      = false;
		$pluginOptions = get_option( ZipRecipes::PLUGIN_OPTION_NAME, array() );
		if ( isset( $pluginOptions[ get_class($this) ] ) && $pluginOptions[ get_class($this) ]["active"] ) {
			$disabled = false;
		} else {
			$disabled = true;
		}

		return $disabled;
	}
}