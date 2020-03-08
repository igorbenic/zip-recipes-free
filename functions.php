<?php
if (!function_exists('zrdn_is_free')){
	/**
	 * check if we're in the free version
	 * @return bool
	 */
	 function zrdn_is_free(){
	 	return true;
		return !defined('ZRDN_PREMIUM');
	}
}