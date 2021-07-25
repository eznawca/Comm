<?php
/**
 * Test of the library Eznawca/Comm
 *
 * @author  Andrzej Mazur <eznawca@gmail.com>
 */

require __DIR__ . '/vendor/autoload.php';

use Eznawca\Comm\Comm;

echo <<<CSS
<style>
	*, *::before, *::after {
		box-sizing: border-box;
	}
	body {
		font-family: system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans","Liberation Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
		font-size: 1rem;
		line-height: 1.5;
		color: #212529;
		background-color: #fff;
	}
</style>
CSS;

echo '<h1>Test of the library Eznawca/Comm</h1>';
echo '<ul>';
	echo '<li>Authors: <a rel="author" href="https://eznawca.pl">Andrzej Mazur</a>';
	echo '<li>Packagist.org: <a href="https://packagist.org/packages/eznawca/comm">packages/eznawca/comm</a>';
	echo '<li>Canonical Repository: <a href="https://github.com/eznawca/Comm">github.com/eznawca/Comm</a>';
	echo '<li>PHP_VERSION (ID): <b>'.PHP_VERSION.'</b> ('.PHP_VERSION_ID.')</a>';
echo '</ul>';


$testStr = 'This is an example of a long sentence.'."\n".'Now a fragment in `Polish`:"Dzień dobry Świecie".'."\n".'Next another sentence in `German`: "Diese Allgemeinen Geschäftsbedingungen des Verkäufers gelten für alle Verträge."';
$email = 'eznawca@gmail.com';


r(Comm::toggleErrorDisplay(), 'Comm::toggleErrorDisplay()');
Comm::r(['Poland' => 'PLN', 'Germany' => 'EUR'], 'Comm::r($variable, "Description") - Prints human-readable information about a variable. Better version of var_dump()/print_r()');
r(Comm::convertToObject(['first' => 'one', 'second' => ['sub1' => 'x1', 'sub2' => 'y2']]), 'Comm::convertToObject(["first" => "one", "second" => ["sub1" => "x1", "sub2" => "y2"]])');
r(Comm::str_short_file('very_very_long_filename.php', 16), 'Comm::str_short_file("very_very_long_file_name.php", 16)');
r(Comm::str_short_name($testStr, 32, true), 'Comm::str_short_name($testStr, 32, true)');
r(Comm::chmod_max(__FILE__), 'Comm::chmod_max(__FILE__)');
r(Comm::str_rot47($testStr), 'Comm::str_rot47($testStr)');
r(Comm::str_rot18($testStr), 'Comm::str_rot18($testStr)');
r($base64_encode = Comm::base64_encode_url($testStr), '$base64_encode = Comm::base64_encode_url($testStr)');
r(Comm::base64_decode_url($base64_encode), 'Comm::base64_decode_url($base64_encode)');
r($encode = Comm::encode($testStr), '$encode = Comm::encode($testStr)');
r(Comm::decode($encode), 'Comm::decode($encode)');
r(Comm::date_like_facebook(time()), 'Comm::date_like_facebook(time())');
r(Comm::remote_ip(), 'Comm::remote_ip()');
r(Comm::query_build_http(['debug' => 'On', 'author' => 'Andrzej']), 'Comm::query_build_http(["debug" => "On", "author" => "Andrzej"])');
//r(Comm::disabledCache(), 'Comm::disabledCache()');
r($uid = Comm::gen_time_id(), '$uid = Comm::gen_time_id()');
r(Comm::dekode_time_id($uid), 'Comm::dekode_time_id($uid)');
r(Comm::pol2ascii($testStr), 'Comm::pol2ascii($testStr)');
r(Comm::de2ascii($testStr), 'Comm::de2ascii($testStr)');
r(Comm::accent2ascii($testStr), 'Comm::accent2ascii($testStr)');
r(Comm::str2slug($testStr), 'Comm::str2slug($testStr)');
r(Comm::substr_words($testStr, 32), 'Comm::substr_words($testStr, 32)');
r(Comm::is_uppercase($testStr), 'Comm::is_uppercase($testStr)');
r(Comm::in_arraypos('function', [$testStr, 'Test Comm']), 'Comm::in_arraypos("function", [$testStr, "Test Comm"])');
r(Comm::passwd_random(), 'Comm::passwd_random() // $polish_human = false');
r(Comm::validate_email_domain($email), 'Comm::validate_email_domain('.$email.')');
r(Comm::get_gravatar($email), 'Comm::get_gravatar('.$email.')');
r(Comm::add_ascii_words($testStr), 'Comm::add_ascii_words($testStr)');
r(Comm::get_search_string($testStr), 'Comm::get_search_string($testStr)');
r(Comm::prepare_query_string($testStr), 'Comm::prepare_query_string($testStr)');
r(Comm::nl2paragraphs($testStr), 'Comm::nl2paragraphs($testStr)');
r(Comm::nl2br($testStr), 'Comm::nl2br($testStr)');
r(Comm::fopen_seekend('', 'r', 0), 'Comm::fopen_seekend("", "r", 0)');
r(Comm::backtick2tag($testStr, 'mark'), 'Comm::backtick2tag($testStr, "mark")');
r(Comm::highlight_fast('świecie', $testStr), 'Comm::highlight_fast("Świecie", $testStr)');
r(Comm::highlight('świecie', $testStr), 'Comm::highlight("świecie", $testStr)');
r(Comm::str2rgb($testStr), 'Comm::str2rgb($testStr)');
r($magic = Comm::magicstr_gen(), '$magic = Comm::magicstr_gen()');
r(Comm::magicstr_test($magic), 'Comm::magicstr_test($magic)');
r(Comm::referer_test(), 'Comm::referer_test()');
r($strhash = Comm::strhash_gen($testStr), '$strhash = Comm::strhash_gen($testStr)');
r(Comm::strhash_test($strhash), 'Comm::strhash_test($strhash)');
r(Comm::highlightText('$email2 = $email;'), 'Comm::highlightText("$email2 = $email;")');


echo '<h2>Global functions available independent of PHP version</h2>';
r('r($testStr)', 'Dumps information about a variable. Shorter version Comm:r()');
r('rb($testStr)', 'Dumps information about a variable and terminates execution of the script. Shorter version Comm:rb()');
r(array_key_first(['one' => 1, 2 => 'two']), 'array_key_first(["one" => 1, 2 => "two"]) - Get the first key of the given array. Only available from PHP7.3');
r(array_key_last(['one' => 1, 2 => 'two']), 'array_key_last(["one" => 1, 2 => "two"]) - Gets the last key of an array. Only available from PHP7.3');
r(str_starts_with($testStr, 'This is'), 'str_starts_with($testStr, "This is") - Checks if a string starts with a given substring. Only available in PHP8');
r(str_ends_with($testStr, 'Verträge."'), 'str_ends_with($testStr, \'Verträge."\') - Checks if a string ends with a given substring. Only available in PHP8');
r(str_contains($testStr, 'Polish'), 'str_contains($testStr, "Polish") - Determine if a string contains a given substring. Only available in PHP8');
r(fdiv(123, 0), 'fdiv(123, 0) - Divides two numbers, according to IEEE 754. Only available in PHP8');