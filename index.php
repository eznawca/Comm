<?php
/**
 * Test of the library Eznawca/Comm
 *
 * @author  Andrzej Mazur <eznawca@gmail.com>
 */

require __DIR__ . '/vendor/autoload.php';

use Eznawca\Comm\Comm;

echo '<h1>Test of the library Eznawca/Comm</h1>';
echo '<a href="https://packagist.org/packages/eznawca/comm">eznawca/comm - Packagist.org</a>';
echo '<address class="author">By <a rel="author" href="https://eznawca.pl">Andrzej Mazur</a></address>';

$testStr = 'This is an example of a long sentence.'."\n".'Now a fragment in `Polish`:"Dzień dobry Świecie".'."\n".'Next another sentence in `German`: "Diese Allgemeinen Geschäftsbedingungen des Verkäufers gelten für alle Verträge."';
$email = 'eznawca@gmial.com';


r(Comm::toggleErrorDisplay(), 'Comm::toggleErrorDisplay()');
r(Comm::r($testStr, 'String $testStr', true, true), 'Comm::r($value, "String $testStr", true, true)');
r(Comm::convertToObject(['first' => 'one', 'second' => 'two', 'asoc' => ['sub1' => 'x1', 'sub2' => 'y2']]), 'Comm::convertToObject(["first" => "one", "second" => "two"])');
r(Comm::str_short_file($testStr, 16), 'Comm::str_short_file($testStr, 16)');
r(Comm::str_short_name($testStr, 16, true), 'Comm::str_short_name($testStr, 16, true)');
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
r(Comm::substr_words($testStr, 21), 'Comm::substr_words($testStr, 21)');
r(Comm::is_uppercase($testStr), 'Comm::is_uppercase($testStr)');
r(Comm::in_arraypos('function', [$testStr, 'Test Comm']), 'Comm::in_arraypos("function", [$testStr, "Test Comm"])');
r(Comm::passwd_random(), 'Comm::passwd_random() // $polish_human = false');
r(Comm::validate_email_domain($email), 'Comm::validate_email_domain($email)');
r(Comm::get_gravatar($email), 'Comm::get_gravatar($email)');
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


