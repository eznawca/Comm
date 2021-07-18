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

/**
 * Functions available from PHP7.3
 */
if (!function_exists('array_key_first')) {
	/**
	 * Gets the first key of an array
	 */
	function array_key_first($array) {
		if($array === []) {return null;}
		foreach($array as $key => $unused) return $key;
	}
}

if (!function_exists('array_key_last')) {
	/**
	 * Gets the last key of an array
	 */
	function array_key_last($array) {
		if($array === []) {return null;}
		return array_key_first(array_slice($array, -1, null, true));
	}
}
/**
 * Functions only available in php8
 */
if (!function_exists('str_starts_with')) {
	/**
	 * Checks if a string starts with a given substring
	 * case-sensitive
	 */
	function str_starts_with($haystack, $needle) {
		$len_h = strlen($haystack);
		$len_n = strlen($needle);
		if ($len_n > $len_h) return false;
		return strncmp($haystack, $needle, $len_n) === 0;
	}
}

if (!function_exists('str_ends_with')) {
	/**
	 * Checks if a string ends with a given substring
	 * case-sensitive
	 */
	function str_ends_with($haystack, $needle) {
		$len_h = strlen($haystack);
		$len_n = strlen($needle);
		if ($len_n > $len_h) return false;
		return $needle === '' || $needle === substr($haystack, - strlen($needle));
	}
}

if (!function_exists('str_contains')) {
	/**
	 * Determine if a string contains a given substring
	 * case-sensitive
	 */
	function str_contains($haystack, $needle) {
		$len_h = strlen($haystack);
		$len_n = strlen($needle);
		if ($len_n > $len_h) return false;
		return strpos($haystack, $needle) !== 0;
	}
}

if (!function_exists('fdiv')) {
	/**
	 * fdiv — Divides two numbers, according to IEEE 754
	 */
	function fdiv(float $dividend, float $divisor): float {
		if ($dividend == 0 && $divisor == 0) return NAN;
		if ($dividend == INF && $divisor == INF) return NAN;

		if ($divisor == 0) {
			if ($dividend > 0) return INF; else return -INF;
		}
		return @($dividend / $divisor);
	}
}