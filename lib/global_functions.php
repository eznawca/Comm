<?php

/**
 * Global space name for easy use
 */

if (!function_exists('r')) {
	function r($v, $o = '')
	{
		Eznawca\Comm\Comm::r($v, $o);
	}
}

if (!function_exists('rb')) {
	function rb($v, $o = '')
	{
		Eznawca\Comm\Comm::rb($v, $o);
	}
}
