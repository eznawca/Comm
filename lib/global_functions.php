<?php

/**
 * Global space name for easy use
 */

if (!function_exists('r')) {
	/**
	 * Function better than var_dump()/print_r()
	 */
	function r($v, $o = '')	{
		Eznawca\Comm\Comm::r($v, $o);
	}
}

if (!function_exists('rb')) {
	/**
	 * Function better than var_dump()/print_r()
	 * and terminate the current script
	 */
	function rb($v, $o = '') {
		Eznawca\Comm\Comm::rb($v, $o);
	}
}
