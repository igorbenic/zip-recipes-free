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

/**
 * Get number from string, and limit to three digits max.
 * @param string $value
 * @return string
 */

if (!function_exists('zrdn_minimal_number')) {
	function zrdn_minimal_number( $value ) {
		preg_match_all( '/(^[0-9]{1,5})\.([0-9]{1,5})(.*)/', $value, $matches );
		if ( isset( $matches[1][0] ) && isset($matches[2][0]) && isset($matches[3][0]) ) {
			$number = $matches[1][0];
			$first_part = $matches[1][0];
			$second_part = $matches[2][0];
			$text = $matches[3][0];
			if (strlen($first_part) >=2) {
				return $first_part.$text;
			} else {
				return $first_part.'.'.substr($second_part, 0, 1).$text;
			}

		}
		return $value;
	}
}